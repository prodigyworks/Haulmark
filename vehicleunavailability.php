<?php
	require_once("crud.php");
	require_once("datafilter.php");
	
	class HolidayCrud extends Crud {
		
		function __construct() {
			parent::__construct();
			
			$this->title = "Vehicle Unavailability";
			$this->table = "{$_SESSION['DB_PREFIX']}vehicleunavailability";
			$this->dialogwidth = 960;
	
			$this->sql = 
				"SELECT A.*, B.name, C.registration AS vehiclename 
				 FROM {$_SESSION['DB_PREFIX']}vehicleunavailability A 
				 INNER JOIN {$_SESSION['DB_PREFIX']}vehicleunavailabilityreasons B
				 ON B.id = A.reasonid 
				 INNER JOIN {$_SESSION['DB_PREFIX']}vehicle C
				 ON C.id = A.vehicleid 
				 ORDER BY A.id DESC";
		 	
			$this->columns = array(
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
						'name'       => 'vehicleid',
						'type'       => 'DATACOMBO',
						'length' 	 => 30,
						'label' 	 => 'Vehicle',
						'table'		 => 'vehicle',
						'table_id'	 => 'id',
						'alias'		 => 'vehiclename',
						'table_name' => 'registration'
					),
					array(
						'name'       => 'startdate',
						'filter'	 => false,
						'datatype'	 => 'timestamp',
						'length' 	 => 20,
						'label' 	 => 'Start Date / Time'
					),
					array(
						'name'       => 'enddate',
						'filter'	 => false,
						'datatype'	 => 'timestamp',
						'length' 	 => 20,
						'label' 	 => 'End Date / Time'
					),
					array(
						'name'       => 'workcarriedout',
						'filter'	 => false,
						'showInView' => false,
						'type'	 	 => 'TEXTAREA',
						'required'	 => false,
						'label' 	 => 'Work Carried Out'
					),
					array(
						'name'       => 'totalcost',
						'length'	 => 12,
						'required'	 => false,
						'datatype'	 => 'double',
						'label' 	 => 'Total Cost'
					),
					array(
						'name'       => 'reasonid',
						'type'       => 'DATACOMBO',
						'length' 	 => 30,
						'label' 	 => 'Reason',
						'table'		 => 'vehicleunavailabilityreasons',
						'table_id'	 => 'id',
						'alias'		 => 'name',
						'table_name' => 'name'
					)
				);
		}
	}
	
	$crud = new HolidayCrud();
	$crud->run();
?>