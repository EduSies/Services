<?php

date_default_timezone_set('Europe/Madrid');

use PhpAmqpLib\Connection\AMQPConnection;


class NewsletterWorkerSender {

	/**
	 * @var PhpAmqpLib\Connection\AMQPConnection
	 */
	protected $_amqp_con;

	/**
	 * @var \PhpAmqpLib\Channel\AMQPChannel
	 */
	protected $_amqp_ch;

	protected $_emails_sended = 1;

	protected $_push;

	protected $_gateway;

	protected $_smtp_config;

	protected $_log_path;


	public function __construct($pusher){

		$this->initLogPath();
		$this->initSmtp($pusher);
		$this->init();

	}


	protected function initLogPath(){

		$this->_log_path = APP_PATH . '/logs/'.date('Ymd');

		if(!file_exists($this->_log_path)) {
			mkdir($this->_log_path);
		}

		$this->_log_path .= '/'.getmypid().'.txt';
	}


	protected function writeLog($data){

		$flg = fopen($this->_log_path, "a+");
		fwrite($flg, $data."\n");
		fclose($flg);
	}


	protected function initSmtp($pusher){

		if(!empty($GLOBALS['smtp_config'][$pusher])){

			$this->_smtp_config = $GLOBALS['smtp_config'][$pusher];

			$this->_gateway = $this->_smtp_config['gateway'];

			unset($this->_smtp_config['gateway']);

		}else{
			throw new Exception('Invalid Smtp Pusher: ' . $pusher);
		}
	}


	public function init(){

		$this->_amqp_con = new AMQPConnection(IP_RABBITMQ_SERVER, PORT_RABBITMQ_SERVER, USER_RABBITMQ_SERVER, PASS_RABBITMQ_SERVER);
		$this->_amqp_ch = $this->_amqp_con->channel();

		/*
			name: QUEUE_NAME
			passive: false
			durable: true // the queue will survive server restarts
			exclusive: false // the queue can be accessed in other channels
			auto_delete: false //the queue won't be deleted once the channel is closed.
		*/
		$this->_amqp_ch->queue_declare(QUEUE_NAME, false, true, false, false);
		$this->_amqp_ch->basic_qos(null, 1, null);
		$this->_amqp_ch->basic_consume(QUEUE_NAME, '', false, false, false, false, array($this, 'callback'));


		$tr = new dotFwk_SMTP_Smtp($this->_gateway, $this->_smtp_config);
		Zend_Mail::setDefaultTransport($tr);

	}


	public function run(){

		echo " [*] Waiting for messages. To exit press CTRL+C \n";

		try {

			// Loop as long as the channel has callbacks registered
			while(count($this->_amqp_ch->callbacks)) {
				$this->_amqp_ch->wait();
			}

		} catch (Exception $exc) {
			echo "EXCEPTION \n\n";
			echo $exc->getMessage();
			echo $exc->getTraceAsString();
			echo "\n";
		}

	}


	/**
	 * @param PhpAmqpLib\Message\AMQPMessage $msg
	 */
	public function callback($msg){

		$success = $this->sendMail(unserialize($msg->body));

		if(!$success) {
			echo "WRONG PARAMETERS \n";
			//TODO: wrong parametters log it;
		}

		$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);

		$this->_emails_sended++;

		if($this->_emails_sended == RECONNECT){

			// This child must die and refresh
			$this->shutdown();
			exit();
		}
	}


	protected function shutdown(){
		$this->_amqp_ch->close();
		$this->_amqp_con->close();
	}


	protected function sendMail($data){

		if($this->checkCorrectMailParams($data)){

			try{
				$mail = new Zend_Mail('utf-8');

				$mail->addTo($data['email']);
				$mail->setFrom($data['from'],$data['sender']);
				$mail->addHeader($data['header']['key'],$data['header']['value']);
				$mail->setDate();
				$mail->setSubject($data['subject']);
				$mail->setBodyHtml($data['html_body']);
				$mail->setBodyText($data['txt_body']);
				$mail->setReturnPath($data['ret_path']);
				$mail->setMessageId(true);
				$mail->send();
				//sleep(3);

				//$data['email'] = "esierra@motofan.com";
				//echo "\n" . $this->_emails_sended . " - Enviado a: ( " . $data['email'] . " )\t\t\t\t" . date('Y-m-d H:i:s', time()) . "\n\n";
				//$info_success = " MAILS TEST SUCCESS: ( " . $this->_emails_sended . " ) ";
				//$info_success .= "\n <--------------------------------------------------------------------------------------> \n\n";
				//$info_success .= " INFO to Send: \n [ " . $data . " ] \n\n [Code: " . $ex->getCode() . "] Msg: " . $ex->getMessage();
				//$info_success .= "\n <--------------------------------------------------------------------------------------> \n";
				//$this->writeLog($info_success);

			} catch (Exception $ex) {

				if( $ex->getCode() == 401 || $ex->getCode() == 501 || $ex->getCode() == 550 || $ex->getCode() == 553 ) {

					$info = "\n\n QUEUE NAME --> " . QUEUE_NAME . " <-- \n";
					$info .= " COUNT MAILS QUEUE ERROR: ( " . $this->_emails_sended . " ) ";
					$info .= "\n <--------------------------------------------------------------------------------------> \n\n";
					$info .= " Error to Send: [ " . $data['email'] . " ] [ return path: " . $data['ret_path'] . " ] \n [Code: " . $ex->getCode() . "] Msg: " . $ex->getMessage();
					//$info .= "\n\n getTraceAsString: \n\n" . $ex->getTraceAsString();
					$info .= "\n <--------------------------------------------------------------------------------------> \n";
					$this->writeLog($info);

					return true;
				}

				$info_error = "\n\n --> QUEUE BLOQUEADA <-- \n";
				$info_error .= " QUEUE NAME --> " . QUEUE_NAME . " <-- \n";
 				$info_error .= " COUNT MAILS QUEUE ERROR: ( " . $this->_emails_sended . " ) ";
 				$info_error .= "\n <--------------------------------------------------------------------------------------> \n\n";
				$info_error .= " Error to Send: [ " . $data['email'] . " ] [ return path: " . $data['ret_path'] . " ] \n [Code: " . $ex->getCode() . "] Msg: " . $ex->getMessage();
 				//$info_error .= "\n\n getTraceAsString: \n\n" . $ex->getTraceAsString();
 				$info_error .= "\n <--------------------------------------------------------------------------------------> \n";
 				$this->writeLog($info_error);

 				//ERROR! sending email, killing child
 				/*echo "\n\n QUEUE NAME --> " . QUEUE_NAME . " <-- \n";
 				echo " COUNT MAILS QUEUE ERROR: ( " . $this->_emails_sended . " )";
 				echo "\n<-------------------------------------------------------------------------------------->\n\n";
 				echo " Error to Send: [ " . $data['email'] . " ] [Code: " . $ex->getCode() . "] Msg: " . $ex->getMessage();
 				//echo "\n\n getTraceAsString: \n\n" . $ex->getTraceAsString();
 				echo "\n<-------------------------------------------------------------------------------------->\n";*/

				$this->shutdown();
				exit(10);
				// Check this exit code for kill parent!
			}

			// Success
			return true;
		} else {

			$info_wrong = " Wrong Mail Params: ( " . $this->_emails_sended . " ) ";
			$info_wrong .= "\n <--------------------------------------------------------------------------------------> \n\n";
			$info_wrong .= " Wrong Mail INFO to Send: \n [ " . $data['email'] . " ] \n [ " . $data['from'] . " ] \n [ " . $data['sender'] . " ] ";
			$info_wrong .= " \n [ " . $data['header']['key'] . " ] \n [ " . $data['header']['value'] . " ] \n [ " . $data['subject'] . " ] \n [ " . empty($data['html_body']) . " ] ";
			$info_wrong .= " \n [ " . empty($data['txt_body']) . " ] \n [ " . $data['ret_path'] . " ] ";
			$info_wrong .= "\n <--------------------------------------------------------------------------------------> \n";
			$this->writeLog($info_wrong);

			// Wrong Mail Params
			return false;
		}
	}


	protected function checkCorrectMailParams($data){

		$correct_data = array(
				'email',
				'from',
				'sender',
				'header',
				'subject',
				'html_body',
				'txt_body',
				'ret_path'
			);

		foreach ($correct_data as $values){
			if(empty($data[$values])){
				return false;
			}
		}

		return true;
	}

}
