<?php

/**
 * Psr 0 autoloader
 * @param string $className
 */

define('APP_PATH', __DIR__);

function autoload($className){
	
	$path = str_replace('_', '/', $className) .'.php';
	$required = APP_PATH . '/library/' . $path;
	if(file_exists($required)){
		require_once $required;
	}
}

function psr0Autoload($className) {
	
    $className = ltrim($className, '\\');
    $fileName  = '';
    $namespace = '';
    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    require_once  APP_PATH . '/library/' .$fileName;
}

set_include_path(APP_PATH . '/library');

spl_autoload_register('autoload');
spl_autoload_register('psr0Autoload');
