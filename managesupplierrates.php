<?php
	require_once("crud.php");
	
	class RateCrud extends Crud {
		
		public function postScriptEvent() {
		}
	}

	$crud = new RateCrud();
	$crud->title = "Supplier Rates";
	$crud->table = "{$_SESSION['DB_PREFIX']}supplierrates";
	$crud->dialogwidth = 600;
	$crud->sql = 
			"SELECT A.*, B.name, C.name AS vehicletypename
			 FROM {$_SESSION['DB_PREFIX']}supplierrates A 
			 INNER JOIN {$_SESSION['DB_PREFIX']}supplier B
			 ON B.id = A.supplierid 
			 INNER JOIN {$_SESSION['DB_PREFIX']}vehicletype C
			 ON C.id = A.vehicletypeid 
			 WHERE A.vehicletypeid = {$_GET['id']}
			 ORDER BY B.name";
	
	$crud->columns = array(
			array(
				'name'       => 'id',
				'length' 	 => 6,
				'pk'		 => true,
				'showInView' => false,
				'editable'	 => false,
				'bind' 	 	 => false,
				'filter'	 => false,
				'label' 	 => 'ID'
			),
			array(
				'name'       => 'vehicletypeid',
				'editable'	 => false,
				'showInView' => false,
				'default'	 => $_GET['id']
			),
			array(
				'name'       => 'vehicletypename',
				'editable'	 => false,
				'bind'	 	 => false,
				'length' 	 => 30,
				'label' 	 => 'Vehicle Type'
			),
			array(
				'name'		 => 'supplierid',
				'type'       => 'DATACOMBO',
				'length' 	 => 30,
				'label' 	 => 'Supplier',
				'table'		 => 'supplier',
				'table_id'	 => 'id',
				'alias'		 => 'name',
				'table_name' => 'name'
			),
			array(
				'name'       => 'rateper24hours',
				'length' 	 => 12,
				'datatype'	 => 'decimal',
				'align'		 => 'right',
				'label' 	 => 'Rate'
			)
		);
		
	$crud->run();
?>
