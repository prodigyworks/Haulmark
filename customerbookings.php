<?php
	require_once("crud.php");
	
	class BookingCrud extends Crud {
		
		public function __construct() {
			parent::__construct();
			
			$and = "";
			$customerid = getLoggedOnCustomerID();
			
			if (isset($_GET['date'])) {
				$date = convertStringToDate($_GET['date']);
				$and = "AND DATE(A.startdatetime) = '$date' ";
			}
			
			$this->title = "Bookings";
			$this->allowAdd = false;
			$this->allowEdit = false;
			$this->allowView = true;
			$this->allowRemove = false;
			$this->table = "{$_SESSION['DB_PREFIX']}booking";
			$this->dialogwidth = 1050;
			$this->document = array(
					'primaryidname'	 => 	"bookingid",
					'tablename'		 =>		"bookingdocs"
				);
			$this->sql = 
				   "SELECT A.*, B.registration AS trailername, C.name AS driversname, D.name AS customername, 
				    E.registration AS vehiclename, F.name AS vehicletypename, 
				    H.name AS statusname, I.fullname, J.name AS worktypename,
				    L.name AS nominalledgercodename,
				    (
				    	SELECT M.arrivaltime 
				    	FROM {$_SESSION['DB_PREFIX']}bookingleg M  
				    	WHERE M.bookingid = A.id
				    	ORDER BY M.id
				    	LIMIT 1
				    ) AS startlegdatetime
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
					LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}bookingstatus H 
					ON H.id = A.statusid 
					LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}members I 
					ON I.member_id = A.memberid 
					LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}worktype J 
					ON J.id = A.worktypeid
					LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}nominalledgercode L 
					ON L.id = A.nominalledgercodeid
					WHERE A.customerid = $customerid
					$and
					ORDER BY A.startdatetime DESC";
			
			$this->columns = array(
					array(
						'name'       => 'id',
						'length' 	 => 16,
						'pk'		 => true,
						'editable'	 => false,
						'bind'	 	 => false,
						'showInView' => false,
						'filter'	 => false,
						'label' 	 => 'Booking Number'
					),
					array(
						'name'       => 'bookingref',
						'function'   => 'bookingReference',
						'sortcolumn' => 'A.id',
						'type'		 => 'DERIVED',
						'length' 	 => 16,
						'editable'	 => false,
						'bind' 	 	 => false,
						'filter'	 => false,
						'label' 	 => 'Booking Number'
					),
					array(
						'name'       => 'statusAcknowledged',
						'function'   => 'statusAcknowledged',
						'sortcolumn' => 'A.statusid',
						'type'		 => 'DERIVED',
						'align'		 => 'center',
						'length' 	 => 14,
						'editable'	 => false,
						'bind' 	 	 => false,
						'filter'	 => false,
						'label' 	 => 'Acknowledged'
					),
					array(
						'name'       => 'statusDriverAware',
						'function'   => 'statusDriverAware',
						'sortcolumn' => 'A.statusid',
						'align'		 => 'center',
						'type'		 => 'DERIVED',
						'length' 	 => 14,
						'editable'	 => false,
						'bind' 	 	 => false,
						'filter'	 => false,
						'label' 	 => 'Driver Aware'
					),
					array(
						'name'       => 'statusInProgress',
						'function'   => 'statusInProgress',
						'sortcolumn' => 'A.statusid',
						'type'		 => 'DERIVED',
						'align'		 => 'center',
						'length' 	 => 14,
						'editable'	 => false,
						'bind' 	 	 => false,
						'filter'	 => false,
						'label' 	 => 'In Progress'
					),
					array(
						'name'       => 'statusComplete',
						'function'   => 'statusComplete',
						'sortcolumn' => 'A.statusid',
						'type'		 => 'DERIVED',
						'length' 	 => 14,
						'align'		 => 'center',
						'editable'	 => false,
						'bind' 	 	 => false,
						'filter'	 => false,
						'label' 	 => 'Complete'
					),
					array(
						'name'       => 'statusInvoiced',
						'function'   => 'statusInvoiced',
						'sortcolumn' => 'A.statusid',
						'align'		 => 'center',
						'type'		 => 'DERIVED',
						'length' 	 => 14,
						'editable'	 => false,
						'bind' 	 	 => false,
						'filter'	 => false,
						'label' 	 => 'Invoiced'
					),
					array(
						'name'       => 'collectionRef',
						'function'   => 'collectionReference',
						'sortcolumn' => 'A.startdatetime',
						'type'		 => 'DERIVED',
						'length' 	 => 18,
						'editable'	 => false,
						'bind' 	 	 => false,
						'filter'	 => false,
						'label' 	 => 'Collection Date'
					),
					array(
						'name'       => 'legsummary',
						'bind'		 => false,
						'length' 	 => 60,
						'label' 	 => 'To Location'
					),
					array(
						'name'       => 'ordernumber',
						'length' 	 => 13,
						'label' 	 => 'Order Number'
					),
					array(
						'name'       => 'pallets',
						'length' 	 => 10,
						'required'   => false,
						'datatype'	 => 'integer',
						'align'		 => 'right',
						'label' 	 => 'Pallets'
					),
					array(
						'name'       => 'weight',
						'length' 	 => 12,
						'datatype'	 => 'double',
						'align'		 => 'right',
						'label' 	 => 'Weight'
					),
					array(
						'name'       => 'memberid',
						'type'       => 'DATACOMBO',
						'length' 	 => 30,
						'label' 	 => 'Logged By',
						'table'		 => 'members',
						'table_id'	 => 'member_id',
						'alias'		 => 'fullname',
						'table_name' => 'fullname'
					),
					array(
						'name'       => 'bookingtype',
						'length' 	 => 20,
						'label' 	 => 'Source',
						'editable'	 => false,
						'bind'		 => false,
						'type'       => 'COMBO',
						'options'    => array(
								array(
									'value'		=> 'W',
									'text'		=> 'Online Booking'
								),
								array(
									'value'		=> 'M',
									'text'		=> 'Manual Booking'
								)
							)
					),
					array(
						'name'       => 'notes',
						'length' 	 => 50,
						'type'		 => 'TEXTAREA',
						'showInView' => false,
						'label' 	 => 'Notes'
					)
				);
		}
		
		/* Post header event. */
		public function postHeaderEvent() {
?>
			<style>
				#dateswitch {
					position: absolute;
					top: 38px;
					left: 900px;
					width:200px;
					height:32px;
				}
				#cleardate {
					width:16px;
					height:16px;
				}
			</style>
			<div id="dateswitch">
				<span>Date</span>
				<input class="datepicker" id="switchdate" name="switchdate" value="<?php if (isset($_GET['date'])) echo str_replace("-", "/", $_GET['date']); ?>" />
				<span id="cleardate"><img src='images/delete.png' /></span>
			</div>
<?php
		}

		public function postScriptEvent() {
?>
			function collectionReference(node) {
				return node.startlegdatetime;
			}
			
			function refreshScreen() {
				refreshData();
				
				setTimeout(refreshScreen, 60000);
			}
			
			$(document).ready(function() {
					setTimeout(refreshScreen, 60000);
						
					$("#switchdate").change(
							function() {
								navigate("<?php echo $_SERVER['PHP_SELF']; ?>?date=" + $(this).val().replace(/\//g, '-'));
							}
						);
						
					$("#cleardate").click(
							function() {
								navigate("<?php echo $_SERVER['PHP_SELF']; ?>");
							}
						);
				});
				
			function bookingReference(node) {
				return "<?php echo getSiteConfigData()->bookingprefix; ?>" + padZero(node.id, 6);
			}
			
			function statusAcknowledged(node) {
				if (node.confirmed == "Y") {
					return "<img height=24 src='images/tick.png' />";
				}
				
				return "<img height=24 src='images/redtick.png' />";
			}
			
			function statusDriverAware(node) {
				if (node.statusid >= 5) {
					return "<img height=24 src='images/tick.png' />";
				}
				
				return "<img height=24 src='images/redtick.png' />";
			}
			
			function statusInProgress(node) {
				if (node.statusid >= 6) {
					return "<img height=24 src='images/tick.png' />";
				}
				
				return "<img height=24 src='images/redtick.png' />";
			}
			
			function statusComplete(node) {
				if (node.statusid >= 7) {
					return "<img height=24 src='images/tick.png' />";
				}
				
				return "<img height=24 src='images/redtick.png' />";
			}
			
			function statusInvoiced(node) {
				if (node.statusid >= 8) {
					return "<img height=24 src='images/tick.png' />";
				}
				
				return "<img height=24 src='images/redtick.png' />";
			}
<?php			
		}
	}
	
	$crud = new BookingCrud();
	$crud->run(); 
?>
