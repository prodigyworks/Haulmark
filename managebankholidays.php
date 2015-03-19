<?php
	require_once("crud.php");
	
	$crud = new Crud();
	$crud->title = "Bank Holidays";
	$crud->table = "{$_SESSION['DB_PREFIX']}bankholiday";
	$crud->dialogwidth = 550;
	$crud->sql = 
			"SELECT A.* " .
			"FROM {$_SESSION['DB_PREFIX']}bankholiday A " .
			"ORDER BY A.startdate";
	$crud->columns = array(
			array(
				'name'       => 'id',
				'viewname'   => 'uniqueid',
				'length' 	 => 6,
				'showInView' => false,
				'bind' 	 	 => false,
				'editable' 	 => false,
				'pk'		 => true,
				'label' 	 => 'ID'
			),
			array(
				'name'       => 'description',
				'length' 	 => 60,
				'label' 	 => 'Name'
			),
			array(
				'name'       => 'startdate',
				'datatype'	 => 'date',
				'length' 	 => 15,
				'label' 	 => 'Start Date'
			),
			array(
				'name'       => 'enddate',
				'datatype'	 => 'date',
				'length' 	 => 15,
				'label' 	 => 'End Date'
			)
		);
		
	$crud->run();
	
?>
