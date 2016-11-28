<?php
	require_once("../system-db.php");
	
	function call($classname, $methodname, $args) {
		start_db();	
		
		try {
			require_once("$classname.php");
			
			$object = new $classname();
		
			return json_encode($object->$methodname($args));
			
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
?>