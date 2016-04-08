<?php
	require_once("system-db.php");
	require_once("crud.php");
	
	class CustomerCrud extends Crud {
	}
	
	$customerid = $_GET['id'];
	
	$crud = new CustomerCrud();
	$crud->dialogwidth = 550;
	$crud->title = "Customer Contacts";
	$crud->allowAdd = false;
	$crud->allowRemove = false;
	$crud->table = "{$_SESSION['DB_PREFIX']}members";
	$crud->sql = "SELECT A.*
				  FROM  {$_SESSION['DB_PREFIX']}members A
				  WHERE A.customerid = $customerid
				  ORDER BY A.fullname DESC";
	$crud->columns = array(
			array(
				'name'       => 'member_id',
				'viewname'   => 'uniqueid',
				'length' 	 => 6,
				'showInView' => false,
				'filter'	 => false,
				'bind' 	 	 => false,
				'editable' 	 => false,
				'filter'	 => false,
				'pk'		 => true,
				'label' 	 => 'ID'
			),
			array(
				'name'       => 'customerid',
				'editable'	 => false,
				'filter'	 => false,
				'showInView' => false,
				'default'	 => isset($_GET['id']) ? $_GET['id'] : 0
			),
			array(
				'name'       => 'fullname',
				'length' 	 => 40,
				'label' 	 => 'Name'
			),
			array(
				'name'       => 'email',
				'length' 	 => 60,
				'label' 	 => 'Email'
			),
			array(
				'name'       => 'landline',
				'length' 	 => 20,
				'required'	 => false,
				'label' 	 => 'Telephone'
			),
			array(
				'name'       => 'mobile',
				'length' 	 => 20,
				'required'	 => false,
				'label' 	 => 'Mobile'
			)
		);
		
	$crud->run();
?>
