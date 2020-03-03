<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__.'/vendor/autoload.php';

require_once __DIR__.'/config/pro.php';
//require_once __DIR__.'/config/local.php';


use Knp\Provider\ConsoleServiceProvider;
use Silex\Provider\SwiftmailerServiceProvider;


$app = new Silex\Application();
$app['debug'] = true;


$app->register(new ConsoleServiceProvider(),array(
	'console.name'								=> 'MyConsoleApp',
	'console.version'							=> '0.1.0',
	'console.project_directory' 	=> __DIR__ . "/.."
));


$swiftmailer_config['swiftmailer.options'] = array( 'host' => SWIFT_SMTP_HOST,
													'port' => SWIFT_SMTP_PORT,
													'auth_mode' => SWIFT_SMTP_AUTH_MODE,
													'encryption' => SWIFT_SMTP_ENCRYPT,
													'username' => SWIFT_SMTP_USERNAME,
													'password' => SWIFT_SMTP_PASSWORD
											);

$app->register(new SwiftmailerServiceProvider($swiftmailer_config));

$app['swiftmailer.use_spool'] = false;


return $app;
