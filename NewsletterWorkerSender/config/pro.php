<?php

//RABBITMQ
define('IP_RABBITMQ_SERVER', '');
define('PORT_RABBITMQ_SERVER', 5672);
define('USER_RABBITMQ_SERVER', '');
define('PASS_RABBITMQ_SERVER', '');

//PUSH
require_once __DIR__ . '/push.php';

define ('RECONNECT', 10000);
