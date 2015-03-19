<?php
	require_once("crud.php");
	
	class UnavailableCrud extends Crud {
		public function postScriptEvent() {
?>
<?php
		}
	}
	
	$crud = new UnavailableCrud();
	$crud->title = "Trailer Unavailability Codes";
	$crud->table = "{$_SESSION['DB_PREFIX']}trailerunavailabilityreasons";
	$crud->sql = "SELECT * FROM {$_SESSION['DB_PREFIX']}trailerunavailabilityreasons ORDER BY name";
	
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
