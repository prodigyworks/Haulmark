<?php
	require_once("crud.php");
	require_once("datafilter.php");
	
	class TrailerCrud extends Crud {
		
		function __construct() {
			parent::__construct();
			
			$this->title = "Trailers";
			$this->table = "{$_SESSION['DB_PREFIX']}trailerunavailability";
			$this->dialogwidth = 800;
			$this->pagesize = 30;
			$this->torow = 30;
			$this->validateForm = "validateCrudForm";
			$this->document = array(
					'primaryidname'	 => 	"maintenanceid",
					'tablename'		 =>		"trailerunavailabilitydocs"
				);
			
			if (isset($_GET['date'])) {
				$date = convertStringToDate($_GET['date']);
				$and = "AND DATE(A.startdate) <= '$date' 
						AND DATE(A.enddate)   >= '$date'";
				
			} else {
				$and = "";
			}
			
			$supplierid = getLoggedOnSupplierID();
			
			if ($supplierid != 0) {
				$this->sql = 
					"SELECT A.*, A.workcarriedout AS workcarriedout2, B.name, C.registration AS trailername 
					 FROM {$_SESSION['DB_PREFIX']}trailerunavailability A 
					 INNER JOIN {$_SESSION['DB_PREFIX']}trailerunavailabilityreasons B
					 ON B.id = A.reasonid 
					 INNER JOIN {$_SESSION['DB_PREFIX']}trailer C
					 ON C.id = A.trailerid 
					 WHERE A.supplierid = $supplierid
					 $and
					 ORDER BY A.startdate";
					 
			} else {
				$this->sql = 
					"SELECT A.*, A.workcarriedout AS workcarriedout2, B.name, C.registration AS trailername 
					 FROM {$_SESSION['DB_PREFIX']}trailerunavailability A 
					 INNER JOIN {$_SESSION['DB_PREFIX']}trailerunavailabilityreasons B
					 ON B.id = A.reasonid 
					 INNER JOIN {$_SESSION['DB_PREFIX']}trailer C
					 ON C.id = A.trailerid 
					 WHERE 1 = 1
					 $and
					 ORDER BY A.startdate";
			}
		 	
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
						'name'       => 'supplierid',
						'label' 	 => 'Supplier',
						'editable'	 => false,
						'showInView' => false,
						'default'	 => getLoggedOnSupplierID()
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
						'name'       => 'status',
						'length' 	 => 20,
						'label' 	 => 'Status',
						'type'       => 'COMBO',
						'options'    => array(
								array(
									'value'		=> 'S',
									'text'		=> 'Scheduled'
								),
								array(
									'value'		=> 'A',
									'text'		=> 'Awaiting Order Number'
								),
								array(
									'value'		=> 'I',
									'text'		=> 'In Progress'
								),
								array(
									'value'		=> 'C',
									'text'		=> 'Complete'
								)
							)
					),
					array(
						'name'       => 'ordernumber',
						'required'	 => false,
						'length'	 => 20,
						'label' 	 => 'Order Number'
					),
					array(
						'name'       => 'invoicenumber',
						'required'	 => false,
						'length'	 => 20,
						'label' 	 => 'Invoice Number'
					),
					array(
						'name'       => 'workcarriedout',
						'filter'	 => false,
						'showInView' => false,
						'type'	 	 => 'BASICTEXTAREA',
						'required'	 => false,
						'label' 	 => 'Work Carried Out'
					),
					array(
						'name'       => 'totalcost',
						'length'	 => 12,
						'required'	 => false,
						'align'		 => 'right',
						'datatype'	 => 'double',
						'label' 	 => 'Total Cost'
					),
					array(
						'name'       => 'defectnumber',
						'required'	 => false,
						'showInView' => false,
						'length'	 => 20,
						'label' 	 => 'Defect Number'
					),
					array(
						'name'       => 'reasonid',
						'type'       => 'DATACOMBO',
						'length' 	 => 30,
						'label' 	 => 'Reason',
						'table'		 => 'trailerunavailabilityreasons',
						'table_id'	 => 'id',
						'showInVIew' => false,
						'alias'		 => 'name',
						'table_name' => 'name'
					),
					array(
						'name'       => 'reason',
						'type'		 => 'DERIVED',
						'length' 	 => 30,
						'filter'	 => false,
						'bind'		 => false,
						'editable' 	 => false,
						'function'   => 'reason',
						'align'		 => 'left',
						'label' 	 => 'Reason'
					)
				);
		}
		
		public function postScriptEvent() {
?>
			function validateCrudForm() {
				if ($("#defectnumber").val() != "") {
					if ($("#defectnumber").val().length != 7) {
						pwAlert("Defect number must be 7 digits in length");
						return false;
					}
				}
				
				return true;
			}
			
			function reason(node) {
				if (node.defectnumber != null && node.defectnumber != "") {
					return node.name + ": " + node.defectnumber;
				}
				
				return node.name;
			}
			
			$(document).ready(function() {
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
<?php
		}
		
		public function editScreenSetup() {
			include("trailerunavailabilityform.php");
		}
		
		public function afterInsertRow() {
			?>
			var status = rowData['status'];

			if (status == "Scheduled") {
				$(this).jqGrid('setCell', rowid, 4, '', { background: '#AAAAFF' });
			
			} else if (status == "Awaiting Order Number") {
				$(this).jqGrid('setCell', rowid, 4, '', { background: 'yellow' });
			
			} else if (status == "In Progress") {
				$(this).jqGrid('setCell', rowid, 4, '', { background: 'orange' });
			
			} else {
				$(this).jqGrid('setCell', rowid, 4, '', { background: '#99FF99' });
			}
			<?php
		}
		
		public function postAddScriptEvent() {
?>
			$("#reasonid").trigger("change");
<?php			
		}
		
		public function postEditScriptEvent() {
?>
			$("#reasonid").trigger("change");
<?php			
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
	}
	
	$crud = new TrailerCrud();
	$crud->run();
?>