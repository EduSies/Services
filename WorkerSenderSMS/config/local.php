<?php

//APP CONFIG
define('MAX_MESSAGES',				10);

//SMS CONFIG
define('SMS_API_ID',				'');
define('SMS_API_LOGIN',				'');
define('SMS_API_URL',				'https://www.smsmasivos.es/api/send');
define('SMS_CREDIT_URL',			'https://www.smsmasivos.es/api/credit');
define('SMS_MIN_CREDIT',			1000);

//RABBITMQ CONFIG
define('RABBITMQ_QUEUE', 			'smsmessages');
define('RABBITMQ_HOST', 			'');
define('RABBITMQ_PORT', 			5672);
define('RABBITMQ_USER', 			'');
define('RABBITMQ_PASS', 			'');

//SWIFT SMTP CONFIG
define('SWIFT_SMTP_HOST', 			'smtp.gmail.com');
define('SWIFT_SMTP_PORT', 			465);
define('SWIFT_SMTP_AUTH_MODE',  	'login');
define('SWIFT_SMTP_ENCRYPT',    	'ssl');
define('SWIFT_SMTP_USERNAME',  		'');
define('SWIFT_SMTP_PASSWORD',  		'');

//EMAIL_ADRESSES
define('EMAIL_NOREPLY',         	'no-reply@motofan.com');
define('EMAIL_TECHNICAL', 			'tecnicos@motofan.com');

define('URL_SENDER_SMS',        	'http://sendersms.motofan.local');
