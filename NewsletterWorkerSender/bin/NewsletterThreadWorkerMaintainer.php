<?php

require_once __DIR__.'/../autoloader.php';

$config = ConfigLoader::getInstance();
$config->load();

define('QUEUE_NAME', $config->getArgument('queue_name'));

require_once 'NewsletterWorkerSender.php';

$threads_maintainer = new MultiThreadsMaintainer(
		'NewsletterWorkerSender',
		array($config->getArgument('pusher')),
		'run',
		array(),
		$config->getArgument('threads')
);


$threads_maintainer->run();
