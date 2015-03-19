<?php
	function sendNotification($id, $statusid, $notificationSubject, $notification) {
		$sql = "SELECT A.driverid FROM {$_SESSION['DB_PREFIX']}booking A 
				WHERE A.id = $id";
		$result = mysql_query($sql);
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				sendDriverMessage(
						$member['driverid'], 
						$notificationSubject,
						str_replace("{$id}", getSiteConfigData()->bookingprefix . sprintf("%06d", $id), $notification)
					);
			}
			
		} else {
			logError($sql . " - " . mysql_error(), false);
		}
		
		$sql = "UPDATE {$_SESSION['DB_PREFIX']}booking SET
				statusid = $statusid
				WHERE id = $id";
		$result = mysql_query($sql);
		
		if (! $result) {
			logError($sql . " - " . mysql_error(), false);
		}
	}
	
	class AllocateBookingCrud extends BookingCrud {
		
		public function __construct() {
			parent::__construct();
			
			$this->allowAdd = false;
			$this->allowEdit = false;
			$this->subapplications = array(
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
					WHERE A.statusid IN (3, 4, 9)
					ORDER BY A.id DESC";
		}
		
		public function getNotificationMessage() {
			return "Driver has been notified. An email notification has been sent";
		}
		
		public function postUpdateEvent($id) {
			/* Do nothing. Especially remove the legs. */
		}
		
		public function postScriptEvent() {
			parent::postScriptEvent();
?>
			function notifyStatusComplete(id) {
				notifyStatus(id, 7);
			}

			function notifyStatusDriverAware(id) {
				notifyStatus(id, 5);
			}
			
			function notifyStatusInProgress(id) {
				notifyStatus(id, 6);
			}
			
			function notifyStatusOnHold(id) {
				notifyStatus(id, 3);
			}
			
			function notifyStatusFailed(id) {
				notifyStatus(id, 9);
			}
			
			function notifyStatus(id, statusid) {
				post("editform", "notify", "submitframe", 
						{ 
							notifyid: id,
							notifystatusid: statusid
						}
					);
				
				pwAlert("<?php echo $this->getNotificationMessage(); ?>");
			}
<?php
		}
	}
?>