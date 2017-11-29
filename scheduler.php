<?php require_once 'vendor/autoload.php';

use GO\Scheduler;

// Create a new scheduler
$scheduler = new Scheduler();

// Let the scheduler execute jobs which are due.
$scheduler->php('/schedule/rank.php')
          ->daily(23, 59)
          ->output('/log/rank.log');

$scheduler->php('/schedule/product.php')
		  ->daily(00, 10)
		  ->output('/log/product.log')
		  
$scheduler->run();
