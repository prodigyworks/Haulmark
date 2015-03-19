<?php
	require_once("bookingslib.php");
	require_once("allocationfunctions.php");
	
	function notify() {
		sendNotification(
				$_POST['notifyid'], 
				$_POST['notifystatusid'], 
				"Job in progress", 
				"Job {$id} is in progress"
			);
	}
	
	class DriverConfirmedCrud extends AllocateBookingCrud {
		
		public function __construct() {
			parent::__construct();
			
			$this->subapplications = array(
					array(
						'id'		  => 'statusbutton',
						'title'		  => 'Status',
						'imageurl'	  => 'images/accept.png',
						'submenu'	  =>
						array(
								array(
										'id'		=> 'notifyStatusComplete_id',
										'title'		=> 'Complete',
										'script'	=> 'notifyStatusComplete'
								),
								array(
										'id'		=> 'notifyStatusOnHold_id',
										'title'		=> 'On Hold',
										'script'	=> 'notifyStatusOnHold'
								),
								array(
										'id'		=> 'notifyStatusFailed_id',
										'title'		=> 'Failed',
										'script'	=> 'notifyStatusFailed'
								)
							)
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
				
			$this->messages = array(
					array('id'		  => 'notifyid'),
					array('id'		  => 'notifystatusid')
				);
			
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
					WHERE A.statusid IN (3, 6, 9)
					ORDER BY A.id DESC";
		}
		
		public function getNotificationMessage() {
			return "Driver has confirmed that the job is complete. An email notification has been sent";
		}
	}
	
	$crud = new DriverConfirmedCrud();
	$crud->run();
?>
