<?php
	require_once("crud.php");
	
	class ContactCrud extends Crud {
		
		public function postScriptEvent() {
		}
	}

	$crud = new ContactCrud();
	$crud->title = "Vehicle Types";
	$crud->table = "{$_SESSION['DB_PREFIX']}vehicletype";
	$crud->dialogwidth = 400;
	$crud->sql = 
			"SELECT A.* FROM {$_SESSION['DB_PREFIX']}vehicletype A 
			 ORDER BY A.name";
	
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
				'name'       => 'code',
				'length' 	 => 10,
				'label' 	 => 'Code'
			),
			array(
				'name'       => 'name',
				'length' 	 => 30,
				'label' 	 => 'Name'
			),
			array(
				'name'       => 'allegrodayrate',
				'length' 	 => 12,
				'datatype'	 => 'double',
				'align'		 => 'right',
				'label' 	 => 'Our Day Rate'
			),
			array(
				'name'       => 'agencydayrate',
				'length' 	 => 12,
				'datatype'	 => 'double',
				'align'		 => 'right',
				'label' 	 => 'Agency Day Rate'
			),
			array(
				'name'       => 'vehiclecostpermile',
				'length' 	 => 12,
				'datatype'	 => 'double',
				'align'		 => 'right',
				'label' 	 => 'Vehicle Cost Per Mile'
			),
			array(
				'name'       => 'overheadcostpermile',
				'length' 	 => 12,
				'datatype'	 => 'double',
				'align'		 => 'right',
				'label' 	 => 'Overhead Cost Per Mile'
			),
			array(
				'name'       => 'standardratepermile',
				'length' 	 => 12,
				'datatype'	 => 'double',
				'align'		 => 'right',
				'label' 	 => 'Standard Rate Per Mile'
			),
			array(
				'name'       => 'fuelcostpermile',
				'length' 	 => 12,
				'datatype'	 => 'double',
				'align'		 => 'right',
				'label' 	 => 'Fuel Cost Per Mile'
			)
		);
		
	$crud->run();
?>
