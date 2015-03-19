<?php
	require_once("crud.php");
	require_once("datafilter.php");
	
	class HolidayCrud extends Crud {
		
		public function postAddScriptEvent() {
		}
		
		/* Pre script event. */
		public function preScriptEvent() {
		}
		
		public function postInsertEvent() {
		}
		
		public function postHeaderEvent() {
		}
		
		public function postScriptEvent() {
		}
		
		function __construct() {
			parent::__construct();
			
			$this->title = "Trailer Unavailability";
			$this->table = "{$_SESSION['DB_PREFIX']}trailerunavailability";
			$this->dialogwidth = 500;
	
			$this->sql = 
				"SELECT A.*, B.name, C.registration AS trailername
				 FROM {$_SESSION['DB_PREFIX']}trailerunavailability A 
				 INNER JOIN {$_SESSION['DB_PREFIX']}trailerunavailabilityreasons B
				 ON B.id = A.reasonid 
				 INNER JOIN {$_SESSION['DB_PREFIX']}trailer C
				 ON C.id = A.trailerid 
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
						'name'       => 'trailerid',
						'type'       => 'DATACOMBO',
						'length' 	 => 30,
						'label' 	 => 'Trailer',
						'table'		 => 'trailer',
						'table_id'	 => 'id',
						'alias'		 => 'trailername',
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
						'name'       => 'reasonid',
						'type'       => 'DATACOMBO',
						'length' 	 => 30,
						'label' 	 => 'Reason',
						'table'		 => 'trailerunavailabilityreasons',
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