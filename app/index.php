<?php

		include_once 'config/config.php';
		
		$host        = "host = $host_val";
    	$port        = "port = $port_val";
    	$dbname      = "dbname = $db_val";
    	$credentials = "user = $user_val password=$ps_val";
    	
    	
		try {
            echo "v2";
		} catch (PDOException $e) {
			echo "ERROR: $e";
			die($e->getMessage());
		}
    	

?>