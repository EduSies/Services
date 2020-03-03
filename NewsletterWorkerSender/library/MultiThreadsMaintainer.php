<?php

declare(ticks = 1);

class MultiThreadsMaintainer {
	
	protected $_num_childs;
	
	protected $_threads = array();
		
	protected $_stop = false;
	
	protected $_class_name;
	
	protected $_args_constructor;
	
	protected $_method_name;
	
	protected $_args;
	
	/**
	 * 
	 * @param string $class_name
	 * @param array $args_constructor
	 * @param string $method_name
	 * @param array $args arguments of callable method
	 * @param int $childs num of threads
	 * @throws Exception if isn't callable
	 */
	public function __construct($class_name, array $args_constructor, $method_name, array $args = array(), $childs = 1) {
		
		$this->_class_name			= $class_name;
		$this->_method_name		= $method_name;
		$this->_args				= $args;
		$this->_num_childs			= $childs;
		$this->_args_constructor	= $args_constructor;
		pcntl_signal( SIGUSR1, array( $this, 'shutdownHandler' ) );
	}
	
	public function run(){
		
		echo "[START]\n";
		
		$this->createThreads();
		
		$this->doLoop();
		
		$this->killChilds();
	}
	
	protected function createThreads(){
		
		echo "[Creating threads...]\n";
		
		for( $i = 1;  $i<= $this->_num_childs ; $i++ ){
			$this->_createThread();
		}
	}
	
	protected function _createThread(){
		
		$key = $this->getUniquekey();
		$reflection_class = new ReflectionClass($this->_class_name);

		$this->_threads[$key] = new Thread(
				array(
					$reflection_class->newInstanceArgs($this->_args_constructor),
					$this->_method_name
				)
		);
		call_user_func_array(array($this->_threads[$key], 'start'), $this->_args);
	}
	
	protected function getUniquekey(){
		return md5(uniqid(rand(1000, 9999),true));
	}

	protected function wakeUpDeadThreads(){

		$new_threads = array();
		
		$threads = $this->_threads; 

		foreach ($threads as $key => $thread) {
			
			if(!$thread->isAlive()){
				echo "[Thread has died ExitCode:".$this->_threads[$key]->getExitCode()."]\n";
				unset($this->_threads[$key]);
				$this->_createThread();
			}
		}	
	}

	protected function doLoop(){
		
		while(!$this->_stop){
			
			$this->wakeUpDeadThreads();
			
			sleep(1);
		}
	}
	
	public function shutdownHandler(){
		
		$this->_stop = true;
	}
	
	protected function killChilds(){
		
		foreach ($this->_threads as $thread) {
			$thread->kill();
		}
	}
	
}


