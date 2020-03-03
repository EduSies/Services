<?php


if(posix_kill($_SERVER['argv'][1], SIGUSR1)){
	echo "killed!\n";
}  else {
	echo "Something fails!\n";
}