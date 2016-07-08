<?php
	require_once("bookingslib.php");
	require_once("allocationfunctions.php");
	
	function notify() {
		sendNotification(
				$_POST['notifyid'], 
				$_POST['notifystatusid'], 
				"Driver Notification", 
				"Notification of job {$id}"
			);
	}
	
	class DriverBookingCrud extends AllocateBookingCrud {
		
		public function editScreenSetup() {
			include("allocatejobsform.php");
		}
	}
	
	$crud = new DriverBookingCrud();
	$crud->allowAdd = false;
	$crud->allowEdit = true;
	$crud->subapplications = array(
			array(
				'title'		  => 'Allocate',
				'imageurl'	  => 'images/edit.png',
				'script' 	  => 'edit'
			),
			array(
				'id'		  => 'statusbutton',
				'title'		  => 'Status',
				'imageurl'	  => 'images/accept.png',
				'submenu'	  =>
				array(
						array(
								'id'		=> 'notifyStatusInProgress_id',
								'title'		=> 'Job In Progress',
								'script'	=> 'notifyStatusInProgress'
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
						),
						array(
								'id'		=> 'notifyStatusDriverAware_id',
								'title'		=> 'Driver Aware',
								'script'	=> 'notifyStatusDriverAware'
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
	$crud->run();
?>
