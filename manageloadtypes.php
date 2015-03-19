<?php
	require_once("crud.php");
	
	class LoadTypeCrud extends Crud {
		public function postScriptEvent() {
?>
<?php
		}
	}
	
	$crud = new LoadTypeCrud();
	$crud->title = "Load Type";
	$crud->table = "{$_SESSION['DB_PREFIX']}loadtype";
	$crud->sql = "SELECT * FROM {$_SESSION['DB_PREFIX']}loadtype ORDER BY name";
	
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
