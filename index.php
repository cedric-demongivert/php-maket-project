<?php 

include_once 'src/bdd.php';

	$controller_name;

	if(!isset($_GET) || !isset($_GET['service'])) {
		
		$controller_name = "categories";
		
	}
	else {
		
		$controller_name = $_GET['service'];
		
	}
	
	$controller = new $controller_name(bdd_connect());