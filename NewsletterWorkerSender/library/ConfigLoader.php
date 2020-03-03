<?php

class ConfigLoader {
	
	private static $_instance = null;
	
	private $_arguments;
	
	private $_config_path;
	
	private function __construct() {
		$this->_config_path = APP_PATH . '/config';
	}
	
	/**
	 * @return ConfigLoader
	 */
	public static function getInstance() {
		
		if (  !self::$_instance instanceof self) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}
	
	public function load(){
		$this->setArguments();
		$this->loadEnvironment();
	}
	
	public function getArgument($key){
		if(isset($this->_arguments[$key])){
			return $this->_arguments[$key];
		}
		throw new Exception('Argument "'.$key.'=value" not found must be initialized');
		
	}
	
	private function loadEnvironment(){
		$env = $this->getArgument('env');
		$config = $this->_config_path . '/' . strtolower($env) . '.php';
		
		if(!file_exists($config)){
			throw new Exception ('Config file "'. $config .'" not found');
		}
		require_once $config;
	}
	
	private function setArguments(){
		
		foreach ($_SERVER['argv'] as $value) {
			
			if(preg_match('/=/', $value)){
				
				$exploded = explode('=', $value);
				if(!isset($this->_arguments[$exploded[0]])){
					$this->_arguments[$exploded[0]] = $exploded[1];
				}else{
					throw new Exception('Repeated argument '.$exploded[0]."\n");
				}
			}
		}
	}
}