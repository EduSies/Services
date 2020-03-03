<?php

require '../autoloader.php';

class dummy{
	protected $int;
	public function __construct($int) {
		$this->int = $int;
	}

	public function run($t){
		usleep($t);
		exit($this->int);
	}
}


$thread1 = new Thread(array(new dummy(3), 'run'));
$thread2 = new Thread(array(new dummy(2), 'run'));
$thread3 = new Thread(array(new dummy(1), 'run'));

$thread1->start(10);
$thread2->start(40);
$thread3->start(30);

while ($thread1->isAlive(1) || $thread2->isAlive(2) || $thread3->isAlive(3));

echo "Thread 1 exit code (should be 3): " . $thread1->getExitCode() . "\n";
echo "Thread 2 exit code (should be 2): " . $thread2->getExitCode() . "\n";
echo "Thread 3 exit code (should be 1): " . $thread3->getExitCode() . "\n";