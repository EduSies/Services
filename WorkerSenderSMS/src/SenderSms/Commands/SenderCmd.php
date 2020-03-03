<?php

namespace SenderSms\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PhpAmqpLib\Connection\AMQPConnection;


class SenderCmd extends \Knp\Command\Command {


	/**
	 * @var PhpAmqpLib\Connection\AMQPConnection
	 */
	protected $_amqp_con;

	/**
	 * @var \PhpAmqpLib\Channel\AMQPChannel
	 */
	protected $_amqp_ch;


	protected function configure() {
		$this->setName("sendercmd")->setDescription("Command to Send SMS");
	}


	protected function execute(InputInterface $input, OutputInterface $output) {

		echo " [*] Waiting for messages. To exit press CTRL+C", "\n";

		try {

			$this->_connectQueueChannel();

			// Loop as long as the channel has callbacks registered
			while (count($this->_amqp_ch->callbacks)) {
				$this->_amqp_ch->wait();
			}

		} catch (Exception $exc) {

			echo "EXCEPTION\n\n";
			echo $exc->getMessage();
			echo $exc->getTraceAsString();
			echo "\n";

		}
	}


	protected function _connectQueueChannel() {

		$this->_amqp_con = new AMQPConnection(RABBITMQ_HOST, RABBITMQ_PORT, RABBITMQ_USER, RABBITMQ_PASS);
		$this->_amqp_ch = $this->_amqp_con->channel();

		/*
			name: QUEUE_NAME
			passive: false
			durable: true // the queue will survive server restarts
			exclusive: false // the queue can be accessed in other channels
			auto_delete: false //the queue won't be deleted once the channel is closed.
		*/
		$this->_amqp_ch->queue_declare(RABBITMQ_QUEUE, false, true, false, false);
		$this->_amqp_ch->basic_qos(null, 1, null);
		$this->_amqp_ch->basic_consume(RABBITMQ_QUEUE, '', false, false, false, false, array($this, 'callback'));
	}


	/**
	 * @param PhpAmqpLib\Message\AMQPMessage $msg
	 */
	public function callback($amqp_message) {

		$msg = (json_decode($amqp_message->body));

		$result = $this->_sendSms($msg);

		if ($result['success'] && $msg->callback) {
			$this->_sendPostRequest($msg->callback, $result);
		}

		$amqp_message->delivery_info['channel']->basic_ack($amqp_message->delivery_info['delivery_tag']);

		$this->_checkSmsCredit();
	}


	protected function _sendSms($msg) {

		$result	= array('success' => false, 'message' => 'SMS_ERROR');

		$fields = array('login'		=>	urlencode(SMS_API_LOGIN),
										'apiID'		=>	urlencode(SMS_API_ID),
										'phone_1'	=>	urlencode($msg->phone),
										'text_1'	=>	urlencode($msg->text)
							);

		$response = $this->_SendPostRequest(SMS_API_URL, $fields);

		if ($response == '1-OK<br />') {

			$result = array('success' => true, 'message' => 'SMS_OK');

		} else {

			$this->_sendPostRequest($msg->callback, $result);
			$this->_sendErrorEmail($msg, $response);
		}

		return $result;
	}


	protected function _checkSmsCredit() {

		$fields = array('login' => urlencode(SMS_API_LOGIN), 'apiID' => urlencode(SMS_API_ID));

		$response = $this->_SendPostRequest(SMS_CREDIT_URL, $fields);

		if (($response < SMS_MIN_CREDIT) || ($response == 'NO CREDIT')) {

			$this->_sendCreditEmail($response);
		}
	}


	protected function _SendPostRequest($url, $fields) {

		// Preparamos el string para hacer POST (formato querystring)
		$fields_string = $this->_getQuerystring($fields);

		// Abrimos la conexión
		$ch = curl_init();

		// Configuramos la URL, número de variables POST y los datos POST
		curl_setopt($ch,	CURLOPT_URL,	$url);
		curl_setopt($ch,	CURLOPT_RETURNTRANSFER,	1);
		//curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch,	CURLOPT_POST,	count($fields));
		curl_setopt($ch,	CURLOPT_POSTFIELDS,	$fields_string);

		// Ejecutamos POST
		$result = curl_exec($ch);

		// Cerramos la conexión
		curl_close($ch);

		return $result;
	}


	protected function _sendGetRequest($url) {

		// Abrimos la conexión
		$ch = curl_init();

		// Configuramos la URL y tipo de retorno
		curl_setopt($ch,	CURLOPT_HTTPGET,	$url);
		curl_setopt($ch,	CURLOPT_RETURNTRANSFER,	0);

		// Ejecutamos POST
		$result = curl_exec($ch);

		// Cerramos la conexión
		curl_close($ch);

		return $result;
	}


	protected function _getQuerystring($fields) {

		$fields_string = "";

		foreach ($fields as $key => $value) {
			$fields_string .= $key.'='.$value.'&';
		}

		$fields_string = rtrim($fields_string,'&');

		return $fields_string;
	}


	protected function _sendErrorEmail($msg, $response) {

		$subject = "ERROR: SenderSMS - SMS No Enviado";

		$body	 = "CODE ERROR: " . $response . "\n";
		$body .= "IP: " . $msg->ip . "\n";
		$body .= "PHONE: " . $msg->phone . "\n";
		$body .= "TEXT: " . $msg->text;

		$this->_sendEmail($subject, $body);
	}


	protected function _sendCreditEmail($response) {

		// Enviar notifiación cada 200 cŕeditos (20 mensajes x 10 créditos)
		if($response % 200 === 0){

			$subject = "AVISO: SenderSMS - SMS a Punto de Agotarse";

			$body	 = "El Crédito de Mensajes SMS está a Punto de Agotarse.\n";
			$body .= "CRÉDITO SMS: " . $response . "\n";

			$this->_sendEmail($subject, $body);
		}
	}


	protected function _sendEmail($subject, $body) {

		$fields['subject']	=	$subject;
		$fields['from']			=	EMAIL_NOREPLY;
		$fields['to']				=	EMAIL_TECHNICAL;
		$fields['body']			=	$body;

		$this->_SendPostRequest(URL_SENDER_SMS."/sendmail", $fields);
	}


	protected function shutdown() {

		$this->_amqp_ch->close();
		$this->_amqp_con->close();
	}

}
