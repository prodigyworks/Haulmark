<?php
	require_once("crud.php");
	
	class RateCodeCrud extends Crud {
		public function postScriptEvent() {
?>
<?php
		}
	}
	
	$crud = new RateCodeCrud();
	$crud->title = "Rate Codes";
	$crud->table = "{$_SESSION['DB_PREFIX']}rate";
	$crud->sql = "SELECT * FROM {$_SESSION['DB_PREFIX']}rate ORDER BY name";
	
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
			)
		);
		
	$crud->run();
	
?>
