<?php
	require_once("crud.php");
	
	class WorkTypeCrud extends Crud {
		public function postScriptEvent() {
?>
<?php
		}
	}
	
	$crud = new WorkTypeCrud();
	$crud->title = "Work Type";
	$crud->dialogwidth = 650;
	$crud->table = "{$_SESSION['DB_PREFIX']}worktype";
	$crud->sql = "SELECT A.*, B.name AS nominalledgercodename
				  FROM {$_SESSION['DB_PREFIX']}worktype A
				  LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}nominalledgercode B
				  ON B.id = A.nominalledgercodeid
				  ORDER BY A.name";
	
	$crud->columns = array(
			array(
				'name'       => 'id',
				'length' 	 => 6,
				'showInView' => false,
				'bind' 	 	 => false,
				'editable' 	 => false,
				'pk'		 => true,
				'label' 	 => 'ID'
			),
			array(
				'name'       => 'name',
				'length' 	 => 60,
				'label' 	 => 'Name'
			),
			array(
				'name'       => 'nominalledgercodeid',
				'type'       => 'DATACOMBO',
				'length' 	 => 60,
				'label' 	 => 'Nominal Ledger Code',
				'table'		 => 'nominalledgercode',
				'table_id'	 => 'id',
				'alias'		 => 'nominalledgercodename',
				'table_name' => 'name'
			)
		);
		
	$crud->run();
	
?>
