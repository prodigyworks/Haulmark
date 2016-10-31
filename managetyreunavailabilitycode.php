<?php
	require_once("crud.php");
	
	class UnavailableCrud extends Crud {
		public function postScriptEvent() {
?>
<?php
		}
	}
	
	$crud = new UnavailableCrud();
	$crud->title = "Tyre Unavailability Codes";
	$crud->dialogwidth = 600;
	$crud->table = "{$_SESSION['DB_PREFIX']}tyreunavailabilityreasons";
	$crud->sql = "SELECT * 
				  FROM {$_SESSION['DB_PREFIX']}tyreunavailabilityreasons 
				  ORDER BY name";
	
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
				'name'       => 'defectnumberrequired',
				'length' 	 => 20,
				'label' 	 => 'Defect Number Required',
				'type'       => 'COMBO',
				'options'    => array(
						array(
							'value'		=> 'Y',
							'text'		=> 'Yes'
						),
						array(
							'value'		=> 'N',
							'text'		=> 'No'
						)
					)
			)
		);
		
	$crud->run();
	
?>
