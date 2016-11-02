<?php
	require_once("crud.php");
	
	function confirmPasswordChange() {
		$memberid = getLoggedOnMemberID();
		$password = mysql_escape_string(md5($_POST['postednewpassword']));
		$qry = "UPDATE {$_SESSION['DB_PREFIX']}members SET 
				passwd = '$password', 
				metamodifieddate = NOW(), 
				metamodifieduserid = $memberid 
				WHERE member_id = {$_POST['expiredmemberid']}";
		$result = mysql_query($qry);
		
		if (! $result) {
			logError($qry . " = " . mysql_error());
		}
	}
	
	function expire() {
		$memberid = getLoggedOnMemberID();
		$qry = "UPDATE {$_SESSION['DB_PREFIX']}members SET 
				status = 'N', 
				metamodifieddate = NOW(), 
				metamodifieduserid = $memberid 
				WHERE member_id = {$_POST['expiredmemberid']}";
		$result = mysql_query($qry);
		
		if (! $result) {
			logError($qry . " = " . mysql_error());
		}
	}
	
	function live() {
		$memberid = getLoggedOnMemberID();
		$qry = "UPDATE {$_SESSION['DB_PREFIX']}members SET 
				status = 'Y', 
				metamodifieddate = NOW(), 
				metamodifieduserid = $memberid 
				WHERE member_id = {$_POST['expiredmemberid']}";
		$result = mysql_query($qry);
		
		if (! $result) {
			logError($qry . " = " . mysql_error());
		}
	}
	
	class UserCrud extends Crud {
		
		/* Pre command event. */
		public function preCommandEvent() {
			if (isset($_POST['rolecmd'])) {
				if (isset($_POST['roles'])) {
					$counter = count($_POST['roles']);
		
				} else {
					$counter = 0;
				}
				
				$memberid = $_POST['memberid'];
				$currentmemberid = getLoggedOnMemberID();
				$qry = "DELETE FROM {$_SESSION['DB_PREFIX']}userroles 
						WHERE memberid = $memberid";
				$result = mysql_query($qry);
				
				if (! $result) {
					logError(mysql_error());
				}
		
				for ($i = 0; $i < $counter; $i++) {
					$roleid = $_POST['roles'][$i];
					
					$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}userroles 
							(
								memberid, roleid, 
								metacreateddate, metacreateduserid, 
								metamodifieddate, metamodifieduserid
							) 
							VALUES 
							(
								$memberid, '$roleid', 
								NOW(), $currentmemberid, 
								NOW(), $currentmemberid
							)";
					$result = mysql_query($qry);
				};
			}
		}
		
		public function postUpdateEvent($id) {
			/* Event. */
			$qry = "UPDATE {$_SESSION['DB_PREFIX']}members SET 
					fullname = CONCAT(firstname, CONCAT(' ', lastname)) 
					WHERE member_id = $id";
			$result = mysql_query($qry);
			
			if (! $result) {
				logError($qry . " = " . mysql_error());
			}
		}
		
		/* Post header event. */
		public function postHeaderEvent() {
?>
			<script src='js/jquery.picklists.js' type='text/javascript'></script>
			
			<div id="pwdDialog" class="modal">
				<table cellspacing=10>
					<tr>
						<td>
							<label>New Password</label>
						</td>
						<td>
							<input type="password" id="newpassword" />
						</td>
					</tr>
					<tr>
						<td>
							<label>Confirm Password</label>
						</td>
						<td>
							<input type="password" id="confirmnewpassword" />
						</td>
					</tr>
				</table>
			</div>
			<div id="roleDialog" class="modal">
				<form id="rolesForm" name="rolesForm" method="post">
					<input type="hidden" id="memberid" name="memberid" />
					<input type="hidden" id="rolecmd" name="rolecmd" value="X" />
					<select class="listpicker" name="roles[]" multiple="true" id="roles" >
						<?php createComboOptions("roleid", "roleid", "{$_SESSION['DB_PREFIX']}roles", "", false); ?>
					</select>
				</form>
			</div>
<?php
		}
		
		/* Post script event. */
		public function postScriptEvent() {
?>
			var currentRole = null;
			var currentID = null;
			
			function fullName(node) {
				return (node.firstname + " " + node.lastname);
			}
			
			function daysRemaining(node) {
				return node.prorataholidayentitlement - node.daysremaining;
			}
			
			function holidayentitlement_onchange() {
				var startDateStr = $("#startdate").val();
				
				if (isDate(startDateStr)) {
					var startDate = new Date(startDateStr.substring(6, 10), (parseFloat(startDateStr.substring(3, 5)) - 1), startDateStr.substring(0, 2));
					var lastWorkingDate = null;
					
					if (startDate.getFullYear() == <?php echo date("Y"); ?>) {
						var week = getWeek(startDate);
						var prorataHolidayEntitlement = 0;
						
						if (lastWorkingDate != null) {
							var weeks = parseInt(daysBetween(startDate, lastWorkingDate) / 7);
							prorataHolidayEntitlement = ($("#holidayentitlement").val() / 52) * (weeks);
							
						} else {
							prorataHolidayEntitlement = ($("#holidayentitlement").val() / 52) * (52 - week);
						}
						
						$("#prorataholidayentitlement").val(parseInt(prorataHolidayEntitlement));

					} else {
						$("#prorataholidayentitlement").val($("#holidayentitlement").val());
					}
				}
			}
			
			function daysBetween(first, second) {
			    // Copy date parts of the timestamps, discarding the time parts.
			    var one = new Date(first.getFullYear(), first.getMonth(), first.getDate());
			    var two = new Date(second.getFullYear(), second.getMonth(), second.getDate());
			
			    // Do the math.
			    var millisecondsPerDay = 1000 * 60 * 60 * 24;
			    var millisBetween = two.getTime() - one.getTime();
			    var days = millisBetween / millisecondsPerDay;
			
			    // Round down.
			    return Math.floor(days);
			}
			
			function getWeek(date) {
				var onejan = new Date(date.getFullYear(),0,1);
				
				return Math.ceil((((date - onejan) / 86400000) + onejan.getDay()+1)/7);
			}
			
			$(document).ready(function() {
					$("#roles").pickList({
							removeText: 'Remove Role',
							addText: 'Add Role',
							testMode: false
						});
					
					$("#pwdDialog").dialog({
							autoOpen: false,
							modal: true,
							title: "Password",
							buttons: {
								Ok: function() {
									if ($("#newpassword").val() != $("#confirmnewpassword").val()) {
										pwAlert("Passwords do not match");
										return;
									}
									
									post("editform", "confirmPasswordChange", "submitframe", 
											{ 
												expiredmemberid: currentID,
												postednewpassword: $("#newpassword").val() 
											}
										);
									
									$(this).dialog("close");
								},
								Cancel: function() {
									$(this).dialog("close");
								}
							}
						});
						
					$("#roleDialog").dialog({
							autoOpen: false,
							modal: true,
							width: 800,
							title: "Roles",
							buttons: {
								"Save": function() {
									$("#rolesForm").submit();
								},
								Cancel: function() {
									$(this).dialog("close");
								}
							}
						});
				});
				
			function userRoles(memberid) {
				getJSONData('findroleusers.php?memberid=' + memberid, "#roles", function() {
					$("#memberid").val(memberid);
					$("#roleDialog").dialog("open");
				});
			}
			
			function changePassword(memberid) {
				currentID = memberid;
				
				$("#pwdDialog").dialog("open");
			}
				
			function expire(memberid) {
				post("editform", "expire", "submitframe", 
						{ 
							expiredmemberid: memberid
						}
					);
			}
				
			function live(memberid) {
				post("editform", "live", "submitframe", 
						{ 
							expiredmemberid: memberid
						}
					);
			}
<?php
		}
	}

	$crud = new UserCrud();
	$crud->messages = array(
			array('id'		  => 'expiredmemberid'),
			array('id'		  => 'postednewpassword')
		);
	$crud->subapplications = array(
			array(
				'title'		  => 'User Roles',
				'imageurl'	  => 'images/user.png',
				'script' 	  => 'userRoles'
			),
			array(
				'title'		  => 'Expire',
				'imageurl'	  => 'images/cancel.png',
				'script' 	  => 'expire'
			),
			array(
				'title'		  => 'Live',
				'imageurl'	  => 'images/heart.png',
				'script' 	  => 'live'
			),
			array(
				'title'		  => 'Change Password',
				'imageurl'	  => 'images/lock.png',
				'script' 	  => 'changePassword'
			)
		);

	$crud->allowAdd = false;
	$crud->dialogwidth = 950;
	$crud->title = "Users";
	$crud->table = "{$_SESSION['DB_PREFIX']}members";
	
	$crud->sql = 
			"SELECT A.*, B.name, C.name AS suppliername,
			 (
			 	SELECT SUM(D.daystaken) 
			 	FROM {$_SESSION['DB_PREFIX']}holiday D 
			 	WHERE YEAR(D.startdate) = YEAR(NOW()) 
			 	AND D.memberid = A.member_id 
			 	AND D.acceptedby IS NOT NULL
			 ) AS daysremaining 
			 FROM {$_SESSION['DB_PREFIX']}members A 
			 LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}customer B
			 ON B.id = A.customerid 
			 LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}supplier C
			 ON C.id = A.supplierid 
			 ORDER BY A.firstname, A.lastname"; 
			
	$crud->columns = array(
			array(
				'name'       => 'member_id',
				'length' 	 => 6,
				'showInView' => false,
				'bind' 	 	 => false,
				'filter'	 => false,
				'editable' 	 => false,
				'pk'		 => true,
				'label' 	 => 'ID'
			),
			array(
				'name'       => 'login',
				'length' 	 => 30,
				'label' 	 => 'Login ID'
			),
			array(
				'name'       => 'staffname',
				'type'		 => 'DERIVED',
				'length' 	 => 35,
				'bind'		 => false,
				'function'   => 'fullName',
				'sortcolumn' => 'A.firstname',
				'label' 	 => 'Name'
			),
			array(
				'name'       => 'firstname',
				'length' 	 => 30,
				'showInView' => false,
				'label' 	 => 'First Name'
			),
			array(
				'name'       => 'lastname',
				'length' 	 => 30,
				'showInView' => false,
				'label' 	 => 'Last Name'
			),
			array(
				'name'       => 'name',
				'length' 	 => 30,
				'label' 	 => 'Customer',
				'editable'	 => false,
				'bind'		 => false
			),
			array(
				'name'       => 'suppliername',
				'length' 	 => 30,
				'label' 	 => 'Supplier',
				'editable'	 => false,
				'bind'		 => false
			),
			array(
				'name'       => 'email',
				'length' 	 => 60,
				'datatype'	 => 'email',
				'label' 	 => 'Email'
			),
			array(
				'name'       => 'mobile',
				'required'	 => false,
				'length' 	 => 13,
				'label' 	 => 'Cell phone'
			),
			array(
				'name'       => 'status',
				'length' 	 => 20,
				'label' 	 => 'Status',
				'type'       => 'COMBO',
				'options'    => array(
						array(
							'value'		=> 'Y',
							'text'		=> 'Live'
						),
						array(
							'value'		=> 'N',
							'text'		=> 'Expired'
						),
					)
			),
			array(
				'name'       => 'startdate',
				'datatype'	 => 'date',
				'length' 	 => 12,
				'onchange'	 => 'holidayentitlement_onchange',
				'label' 	 => 'Start Date'
			),
			array(
				'name'       => 'holidayentitlement',
				'required'	 => false,
				'length' 	 => 13,
				'onchange'	 => 'holidayentitlement_onchange',
				'align'		 => 'center',
				'label' 	 => 'Holidays'
			),
			array(
				'name'       => 'prorataholidayentitlement',
				'required'	 => false,
				'length' 	 => 13,
				'align'		 => 'center',
				'readonly'	 => true,
				'label' 	 => 'Holidays (Pro Rata)'
			),
			array(
				'name'       => 'remaining',
				'type'		 => 'DERIVED',
				'length' 	 => 12,
				'filter'	 => false,
				'bind'		 => false,
				'editable' 	 => false,
				'function'   => 'daysRemaining',
				'align'		 => 'center',
				'label' 	 => 'Days Remainings'
			),
			array(
				'name'       => 'address',
				'type'		 => 'BASICTEXTAREA',
				'required'	 => false,
				'showInView' => false,
				'filter'     => false,
				'label' 	 => 'Address'
			),
			array(
				'name'       => 'description',
				'type'		 => 'TEXTAREA',
				'required'	 => false,
				'showInView' => false,
				'filter'     => false,
				'label' 	 => 'Details'
			)
		);
		
	$crud->run();
?>
