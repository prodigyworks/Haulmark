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
	$crud->table = "{$_SESSION['DB_PREFIX']}worktype";
	$crud->sql = "SELECT A.* 
				  FROM {$_SESSION['DB_PREFIX']}worktype A
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
		);
		
	$crud->run();
	
?>
