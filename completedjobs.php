<?php
	require_once("bookingslib.php");
	
	class AllocateBookingCrud extends BookingCrud {
		
		public function __construct() {
			parent::__construct();
			
			$this->sql = 
				   "SELECT A.*, B.registration AS trailername, C.name AS driversname, D.name AS customername, 
				    E.registration AS vehiclename, F.name AS vehicletypename, G.name AS loadtypename,
				    H.name AS statusname, I.fullname, J.name AS worktypename
					FROM {$_SESSION['DB_PREFIX']}booking A 
					LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}trailer B 
					ON B.id = A.trailerid 
					LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}driver C 
					ON C.id = A.driverid 
					LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}customer D 
					ON D.id = A.customerid 
					LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}vehicle E 
					ON E.id = A.vehicleid 
					LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}vehicletype F 
					ON F.id = A.vehicletypeid 
					LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}loadtype G 
					ON G.id = A.loadtypeid 
					LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}bookingstatus H 
					ON H.id = A.statusid 
					LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}members I 
					ON I.member_id = A.memberid 
					LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}loadtype J 
					ON J.id = A.worktypeid 
					WHERE A.statusid IN (7)
					ORDER BY A.id DESC";
		}
		
		public function postUpdateEvent($id) {
		}		
		
		public function editScreenSetup() {
			include("allocatejobsform.php");
		}
	}
	
	$crud = new AllocateBookingCrud();
	$crud->allowAdd = false;
	$crud->allowEdit = false;
	$crud->dialogwidth = 400;
	$crud->subapplications = array(
			array(
				'title'		  => 'Notify',
				'imageurl'	  => 'images/edit.png',
				'script' 	  => 'edit'
			),
			array(
				'title'		  => 'Map',
				'imageurl'	  => 'images/map.png',
				'script' 	  => 'showMap'
			),
			array(
				'title'		  => 'Delivery Note',
				'imageurl'	  => 'images/print.png',
				'script' 	  => 'printDeliveryNote'
			)
		);
	$crud->run();
?>
