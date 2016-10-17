<?php
	require_once("crud.php");
	
	class nominalledgercodeCrud extends Crud {
		public function postScriptEvent() {
?>
<?php
		}
	}
	
	$crud = new nominalledgercodeCrud();
	$crud->title = "Nominal Ledger Codes";
	$crud->table = "{$_SESSION['DB_PREFIX']}nominalledgercode";
	$crud->sql = "SELECT * FROM {$_SESSION['DB_PREFIX']}nominalledgercode ORDER BY name";
	
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
				'name'       => 'code',
				'length' 	 => 20,
				'label' 	 => 'Code'
			),
			array(
				'name'       => 'name',
				'length' 	 => 60,
				'label' 	 => 'Name'
			)
		);
		
	$crud->run();
	
?>
