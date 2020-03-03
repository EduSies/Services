<?php

require_once __DIR__ . '/../bootstrap.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;


$app->get('/checkphone/{phone}/{country}', function ($phone, $country) {

	$phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();

	$MOBILE	= 1;

	try {

		$country   = strtoupper($country);
		$phoneitem = $phoneUtil->parse($phone, $country);

		$validNumber =  $phoneUtil->isValidNumber($phoneitem);
		$validNumberForRegion = $phoneUtil->isValidNumberForRegion($phoneitem, $country);
		$phoneType =	$phoneUtil->getNumberType($phoneitem);

		if ($validNumber && ($phoneType == $MOBILE) && $validNumberForRegion) {
			$response = array('success' => true, 'message' => 'VALID');
		}
		else if (!$validNumber || !$validNumberForRegion) {
			$response = array('success' => false, 'message' => 'INVALIDNUMBER');
		}
		else if ($phoneType != $MOBILE) {
			$response = array('success' => false, 'message' => 'INVALIDTYPE');
		}

	} catch (\libphonenumber\NumberParseException $e) {
		$response = array('success' => false, 'message' => 'ERROR');
	}

	return new JsonResponse($response, 201);
});


$app->post('/enqueue', function (Request $request) use ($app) {

	$data['ip']	= $request->get('ip');

	$data_json = array('success' => true, 'message' => 'NOTSPAM');

	if($data_json['success']){

		$data['phone']		= $request->get('phone');
		$data['text']		= $request->get('message');
		$data['callback']	= $request->get('callback');
		$data_to_enqueue	= json_encode($data);

		$connection = new AMQPConnection(RABBITMQ_HOST, RABBITMQ_PORT, RABBITMQ_USER, RABBITMQ_PASS);
		$channel = $connection->channel();

		/*
			name:	 smsmessages
			passive:	false
			durable:	true // the queue will survive server restarts
			exclusive:	false // the queue can be accessed in other channels
			auto_delete:	false //the queue won't be deleted once the channel is closed.
		*/
		$channel->queue_declare(RABBITMQ_QUEUE, false, true, false, false);
		$msg = new AMQPMessage($data_to_enqueue);
		$channel->basic_publish($msg, '', RABBITMQ_QUEUE);

		$channel->close();
		$connection->close();

		$data_json['message'] = 'ENQUEUED';
	}

	return new JsonResponse($data_json, 201);
});


$app->post('/sendmail', function (Request $request) use ($app) {

	$data['subject']	=	$request->get('subject');
	$data['from']	=	$request->get('from');
	$data['to']	=	$request->get('to');
	$data['body']	=	$request->get('body');

	$message = \Swift_Message::newInstance()
								->setSubject($data['subject'])
								->setFrom(array($data['from'] => 'Motofan.com'))
								->setTo(array($data['to']))
								->setBody($data['body']);

	$app['mailer']->send($message);

	return new JsonResponse($data, 201);
});


$app->run();
