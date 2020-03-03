<?php

require '../autoloader.php';

class dummy{
	
	protected $_arg1;
	
	public function __construct($arg1) {

		$this->_arg1 = $arg1;
	}


	public function run(){
		echo "[hola soy tu hijo, constructor: ".$this->_arg1."]\n";
		while (true){
			sleep(1);	
		}
	}
}



$multi_threads = new MultiThreadsMaintainer('dummy',array('¿que ase?'),  'run', array(), 2);

$multi_threads->run();