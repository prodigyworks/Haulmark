<?php
	require_once("crud.php");
	require_once("datafilter.php");
	
	class AbsenceCrud extends Crud {
		
		
		/* Pre script event. */
		public function preScriptEvent() {
			?>
			var currentID = 0;
			<?php
		}
		
		public function postHeaderEvent() {
			createConfirmDialog("confirmapprovaldialog", "Confirm approval ?", "approve");
			
			?>
				<div id="reasondialog" class="modal">
					<label>Reason</label>
					<textarea id="reasonbox" name="reasonbox" class="tinyMCE" style='width:770px; height: 300px'></textarea>
				</div>
				<div id="reasondivdialog" class="modal">
					<h5>Reason</h5>
					<br>
					<div id="reasondiv" style='width:770px; height: 290px; border: 1px solid black'></div>
				</div>
			<?php
		}
		
		public function postScriptEvent() {
?>
			$(document).ready(
					function() {
					$("#reasondialog").dialog({
							modal: true,
							autoOpen: false,
							title: "Reason for rejection",
							width: 810,
							height: 420,
							buttons: {
								"Save": function() {
									tinyMCE.triggerSave();
									$(this).dialog("close");
									
									post("editform", "rejectAbsence", "submitframe", 
											{ 
												absenceid: currentID, 
												reasonnotes: $("#reasonbox").val() 
											}
										);
								},
								Cancel: function() {
									$(this).dialog("close");
								}
							}
						});
						
						$("#reasondivdialog").dialog({
								modal: true,
								autoOpen: false,
								title: "Reason for rejection",
								width: 810,
								height: 420,
								buttons: {
									"Save": function() {
										$(this).dialog("close");
									}
								}
							});
					}
				);
				
			function statusFormatter(el, cval, opts) {
				if (el == "Rejected") {
					return "<a style='color:red' href='javascript: viewReason(" + opts.uniqueid + ")'>" + el + "</a>";
				}
				
				return el;
		    } 	
				
			function viewReason(id) {
				callAjax(
						"findabsence.php", 
						{ 
							id: id
						},
						function(data) {
							if (data.length > 0) {
								var node = data[0];
								
								$('#reasondiv').html(node.reason); 
								$("#reasondivdialog").dialog("open");
							}
						},
						false
					);
			}
				
				
			/* Full name callback. */
			function fullName(node) {
				return (node.firstname + " " + node.lastname);
			}
			
			function approveAbsence(crudID) {
				currentID = crudID;
				
				$("#confirmapprovaldialog .confirmdialogbody").html("You are about to approve this absence.<br>Are you sure ?");
				$("#confirmapprovaldialog").dialog("open");
			}
			
			function approve() {
				$("#confirmapprovaldialog").dialog("close");
				
				post("editform", "approveAbsence", "submitframe", { absenceid: currentID });
			}
			
			function rejectAbsence(crudID) {
				currentID = crudID;
				
				$("#reasondialog").dialog("open");
			}
			
			function calculateDuration() {
				var startDateStr = $("#startdate").val();
				var endDateStr = $("#enddate").val();
				
				var startDate = new Date(startDateStr.substring(6, 10), (parseFloat(startDateStr.substring(3, 5)) - 1), startDateStr.substring(0, 2));
				var endDate = new Date(endDateStr.substring(6, 10), (parseFloat(endDateStr.substring(3, 5)) - 1), endDateStr.substring(0, 2));
				var days = workingDaysBetweenDates(startDate, endDate);
				
				callAjax(
						"findbankholidays.php", 
						{ 
							startdate: startDateStr,
							enddate: endDateStr
						},
						function(data) {
							if (data.length > 0) {
								var node = data[1];
								
								var bankStartDate = new Date(node.startdate.substring(6, 10), (parseFloat(node.startdate.substring(3, 5)) - 1), node.startdate.substring(0, 2));
								var bankEndDate = new Date(node.enddate.substring(6, 10), (parseFloat(node.enddate.substring(3, 5)) - 1), node.enddate.substring(0, 2));
								var xdays = workingDaysBetweenDates(bankStartDate, bankEndDate);
								
								days -= xdays;
							}
						},
						false
					);
				
				if (days > 0) {
					if ($("#startdate_half").attr("checked") == false) {
						if (startDate.getDay() > 0 && startDate.getDay() < 6) {
							days -= 0.5;
						}
					}
					
					if ($("#enddate_half").attr("checked") == false) {
						if (endDate.getDay() > 0 && endDate.getDay() < 6) {
							days -= 0.5;
						}
					}
				}
				
				$("#daystaken").val(days);
			}
						
			function checkStatus(node) {
				if (node.status != "Pending" && node.status != "Rejected") {
					$("#approvebutton").attr("disabled", true);
					$("#rejectbutton").attr("disabled", true);
				}
			}
			
			function duration(node) {
				return node.daystaken;
			}
			
			function statusName(node) {
				var startDate = new Date(node.startdate.substring(6, 10), (parseFloat(node.startdate.substring(3, 5)) - 1), node.startdate.substring(0, 2));
				var now = new Date();

				if (node.rejectedby != null) {
					return "Rejected";
					
				} else if (startDate.getTime() <= now.getTime()) {
					if (node.acceptedby != null) {
						return "Taken";
						
					} else {
						return "Pending";
					}
				
				} else if (node.acceptedby != null) {
					return "Accepted";
					
				} else {
					return "Pending";
				}
			}
<?php			
		}
		
		function __construct() {
			parent::__construct();
			
			$this->title = "Absences";
			$this->table = "{$_SESSION['DB_PREFIX']}absence";
			$this->dialogwidth = 940;
			$this->onClickCallback = "checkStatus";
	
			if (isset($_GET['id'])) {
				$this->sql = 
					"SELECT A.*, " .
					"B.name, B.payrollnumber " .
					"FROM {$_SESSION['DB_PREFIX']}absence A " .
					"INNER JOIN {$_SESSION['DB_PREFIX']}driver B " .
					"ON B.id = A.memberid " .
					"WHERE A.memberid = " . $_GET['id'];
				
			} else {
				$this->sql = 
					"SELECT A.*, " .
					"B.name " .
					"FROM {$_SESSION['DB_PREFIX']}absence A " .
					"INNER JOIN {$_SESSION['DB_PREFIX']}driver B " .
					"ON B.id = A.memberid";
			}
			
			$this->sql = ($this->sql);
			
			$this->messages = array(
					array('id'		  => 'absenceid'),
					array('id'		  => 'reasonnotes')
				);
			$this->subapplications = array(
					array(
						'id'		  => 'approvebutton',
						'title'		  => 'Approve',
						'imageurl'	  => 'images/approve.png',
						'script' 	  => 'approveAbsence'
					),
					array(
						'id'		  => 'rejectbutton',
						'title'		  => 'Reject',
						'imageurl'	  => 'images/cancel.png',
						'script' 	  => 'rejectAbsence'
					)
				);
				
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
						'name'       => 'memberid',
						'type'       => 'DATACOMBO',
						'length' 	 => 40,
						'label' 	 => 'Driver',
						'table'		 => 'driver',
						'required'	 => true,
						'table_id'	 => 'id',
						'alias'		 => 'name',
						'table_name' => 'name'
					),
					array(
						'name'       => 'requesteddate',
						'datatype'	 => 'date',
						'length' 	 => 15,
						'filter'	 => false,
						'associatedcolumn' => 
							array(
								'startdate_half'
							),
						'label' 	 => 'Request Date'
					),
					array(
						'name'       => 'startdate',
						'filter'	 => false,
						'datatype'	 => 'date',
						'associatedcolumns' => 
							array(
								'startdate_half'
							),
						'length' 	 => 15,
						'onchange'	 => 'calculateDuration',
						'label' 	 => 'Start Date'
					),
					array(
						'name'       => 'startdate_half',
						'type'	 	 => 'CHECKBOX',
						'showInView' => false,
						'filter'	 => false,
						'length' 	 => 15,
						'onchange'	 => 'calculateDuration',
						'label' 	 => 'Full Day'
					),
					array(
						'name'       => 'enddate',
						'filter'	 => false,
						'datatype'	 => 'date',
						'associatedcolumns' => 
							array(
								'enddate_half'
							),
						'length' 	 => 15,
						'onchange'	 => 'calculateDuration',
						'label' 	 => 'End Date'
					),
					array(
						'name'       => 'enddate_half',
						'filter'	 => false,
						'type'	 	 => 'CHECKBOX',
						'showInView' => false,
						'length' 	 => 15,
						'onchange'	 => 'calculateDuration',
						'label' 	 => 'Full Day'
					),
					array(
						'name'       => 'daystaken',
						'filter'	 => false,
						'align'	 	 => 'center',
						'length' 	 => 15,
						'readonly'	 => true,
						'required'	 => false,
						'label' 	 => 'Duration'
					),
					array(
						'name'       => 'absencetype',
						'length' 	 => 20,
						'label' 	 => 'Type',
						'type'       => 'COMBO',
						'options'    => array(
								array(
									'value'		=> 'Unauthorised',
									'text'		=> 'Unauthorised'
								),
								array(
									'value'		=> 'Authorised',
									'text'		=> 'Authorised'
								),
								array(
									'value'		=> 'Sick',
									'text'		=> 'Sick'
								),
								array(
									'value'		=> 'Family Matter',
									'text'		=> 'Family Matter'
								),
								array(
									'value'		=> 'Not In',
									'text'		=> 'Not In'
								),
								array(
									'value'		=> 'Leaver',
									'text'		=> 'Leaver'
								)
							)
					),
					array(
						'name'       => 'status',
						'filter'	 => false,
						'type'		 => 'DERIVED',
						'length' 	 => 30,
						'bind'		 => false,
						'editable' 	 => false,
						'formatter'	 => 'statusFormatter',
						'function'   => 'statusName',
						'label' 	 => 'Status'
					),
					array(
						'name'       => 'absentreason',
						'type'		 => 'TEXTAREA',
						'role'		 => 
							array(
								'MANAGEMENT', 
								'OFFICE',
								'TEAMLEADER',
								'COMPLIANCE',
								'RSM',
								'ADMIN'
							),
						'filter'	 => false,
						'showInView' => false,
						'label' 	 => 'Reason for absence'
					)
				);
		}
	}

	function approveAbsence() {
		$id = $_POST['absenceid'];
		$qry = "UPDATE {$_SESSION['DB_PREFIX']}absence SET " .
				"rejectedby = null, " .
				"rejecteddate = null, " .
				"acceptedby = " . getLoggedOnMemberID() . ", " .
				"accepteddate = NOW() " .
				"WHERE id = $id";
		$result = mysql_query($qry);
		
		if (! $result) {
			logError($qry . " - " . mysql_error());
		}
		
		$qry = "SELECT memberid, absencetype, " .
				"DATE_FORMAT(A.startdate, '%d/%m/%Y') AS startdate, " .
				"DATE_FORMAT(A.enddate, '%d/%m/%Y') AS enddate " .
				"FROM {$_SESSION['DB_PREFIX']}absence  " .
				"WHERE id = $id";
		$result = mysql_query($qry);
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				sendUserMessage(
						$member['memberid'], 
						"Absence approved", 
						"Absence has been approved between " 
						. $member['startdate'] 
						. " and " 
						. $member['enddate'] 
						. ", Type : " 
						. $member['absencetype']
					);
			}
		}
	}
	
	function rejectAbsence() {
		$id = $_POST['absenceid'];
		$reason = $_POST['reasonnotes'];
		
		$qry = "UPDATE {$_SESSION['DB_PREFIX']}absence SET " .
				"acceptedby = null, " .
				"accepteddate = null, " .
				"reason = '" . mysql_escape_string($reason) . "', " .
				"rejectedby = " . getLoggedOnMemberID() . ", " .
				"rejecteddate = NOW() " .
				"WHERE id = $id";
		$result = mysql_query($qry);
		
		if (! $result) {
			logError($qry . " - " . mysql_error());
		}
		
		$qry = "SELECT memberid, absentreason, " .
				"DATE_FORMAT(A.startdate, '%d/%m/%Y') AS startdate, " .
				"DATE_FORMAT(A.enddate, '%d/%m/%Y') AS enddate " .
				"FROM {$_SESSION['DB_PREFIX']}absence  " .
				"WHERE id = $id";
		$result = mysql_query($qry);
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				sendUserMessage(
						$member['memberid'], 
						"Absence rejected", 
						"Absence has been rejected between " 
						. $member['startdate'] 
						. " and " 
						. $member['enddate'] 
						. ", reason: " . $member['absentreason']
					);
			}
		}
	}
?>