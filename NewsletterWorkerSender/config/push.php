<?php

//PUSH
$GLOBALS['smtp_config'] =array(
	'gmail_test'	=> array(
		'gateway'	=> 'smtp.gmail.com',
		'port'		=> 587,
		'username'	=> '',
		'password'	=> '',
		'auth'		=> 'login',
		'ssl'		=> 'tls'
	),
	'gi' => array(
		'gateway'	=> '',
		'port'		=> 11125
	),
	'hw' => array(
		'gateway'	=> '',
		'port'		=> 25,
		'username'	=> '',
		'password'	=> '',
		'auth'		=> 'login',
	),
	'hwa10' => array(
		'gateway'	=> '',
		'port'		=> 25,
		'username'	=> '',
		'password'	=> '',
		'auth'		=> 'login',
	),
	'sparkpost' => array(
		'gateway'	=> '',
		'port'		=> 587,
		'username'	=> 'SMTP_Injection',
		'password'	=> '',
		'auth'		=> 'login',
	)
);
