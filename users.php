<?php
	require_once("crud.php");
	
	function expire() {
		$qry = "UPDATE {$_SESSION['DB_PREFIX']}members SET status = 'N', metamodifieddate = NOW(), metamodifieduserid = " . getLoggedOnMemberID() . " WHERE member_id = " . $_POST['expiredmemberid'];
		$result = mysql_query($qry);
		
		if (! $result) {
			logError($qry . " = " . mysql_error());
		}
	}
	
	function live() {
		$qry = "UPDATE {$_SESSION['DB_PREFIX']}members SET status = 'Y', metamodifieddate = NOW(), metamodifieduserid = " . getLoggedOnMemberID() . " WHERE member_id = " . $_POST['expiredmemberid'];
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
				$qry = "DELETE FROM {$_SESSION['DB_PREFIX']}userroles WHERE memberid = $memberid";
				$result = mysql_query($qry);
				
				if (! $result) {
					logError(mysql_error());
				}
		
				for ($i = 0; $i < $counter; $i++) {
					$roleid = $_POST['roles'][$i];
					
					$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}userroles (memberid, roleid, metacreateddate, metacreateduserid, metamodifieddate, metamodifieduserid) VALUES ($memberid, '$roleid', NOW(), " . getLoggedOnMemberID() . ", NOW(), " .  getLoggedOnMemberID() . ")";
					$result = mysql_query($qry);
				};
			}
		}

		/* Post header event. */
		public function postHeaderEvent() {
?>
			<script src='js/jquery.picklists.js' type='text/javascript'></script>
			
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
			
			function fullName(node) {
				return (node.firstname + " " + node.lastname);
			}
			
			function holidayentitlement_onchange() {
				var startDateStr = $("#startdate").val();
				var lastWorkingDateStr = $("#lastworkingdate").val();
				
				if (isDate(startDateStr)) {
					var startDate = new Date(startDateStr.substring(6, 10), (parseFloat(startDateStr.substring(3, 5)) - 1), startDateStr.substring(0, 2));
					var lastWorkingDate = null;
					
					if (isDate(lastWorkingDateStr)) {
						lastWorkingDate = new Date(lastWorkingDateStr.substring(6, 10), (parseFloat(lastWorkingDateStr.substring(3, 5)) - 1), lastWorkingDateStr.substring(0, 2));
					}
					
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
					
					$("#roleDialog").dialog({
							autoOpen: false,
							modal: true,
							width: 800,
							title: "Roles",
							buttons: {
								Ok: function() {
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
			array('id'		  => 'expiredmemberid')
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
			)
		);
	$crud->checkconstraints = array(
			array(
				'table'      => 'applicationtables',
				'column' 	 => 'memberid'
			),
			array(
				'table'      => 'applicationtables',
				'column' 	 => 'memberid'
			),
			array(
				'table'      => 'errors',
				'column' 	 => 'memberid'
			),
			array(
				'table'      => 'filter',
				'column' 	 => 'memberid'
			),
			array(
				'table'      => 'loginaudit',
				'column' 	 => 'memberid'
			),
			array(
				'table'      => 'userroles',
				'column' 	 => 'memberid'
			)
		);
	$crud->allowAdd = false;
	$crud->dialogwidth = 950;
	$crud->title = "Users";
	$crud->table = "{$_SESSION['DB_PREFIX']}members";
	
	$crud->sql = 
			"SELECT A.* " .
			"FROM {$_SESSION['DB_PREFIX']}members A " .
			"ORDER BY A.firstname, A.lastname"; 
			
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
				'length' 	 => 60,
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
				'name'       => 'email',
				'length' 	 => 60,
				'label' 	 => 'Email'
			),
			array(
				'name'       => 'landline',
				'length' 	 => 13,
				'label' 	 => 'Land line'
			),
			array(
				'name'       => 'fax',
				'length' 	 => 13,
				'label' 	 => 'Fax'
			),
			array(
				'name'       => 'mobile',
				'length' 	 => 13,
				'label' 	 => 'Cell phone'
			),
			array(
				'name'       => 'status',
				'length' 	 => 30,
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
						)
					)
			),
			array(
				'name'       => 'imageid',
				'type'		 => 'IMAGE',
				'length' 	 => 64,
				'required'	 => false,
				'showInView' => false,
				'label' 	 => 'Image'
			),
			array(
				'name'       => 'title',
				'length'	 => 20,
				'label' 	 => 'Title'
			),
			array(
				'name'       => 'holidayentitlement',
				'length' 	 => 10,
				'onchange'	 => 'holidayentitlement_onchange',
				'align' 	 => 'center',
				'label' 	 => 'Entitlement'
			),
			array(
				'name'       => 'prorataholidayentitlement',
				'length' 	 => 10,
				'readonly'	 => true,
				'align' 	 => 'center',
				'label' 	 => 'Entitlement (Pro Rata)'
			),
			array(
				'name'       => 'daysbooked',
				'length' 	 => 10,
				'align' 	 => 'center',
				'required'	 => false,
				'readonly'	 => true,
				'bind'		 => false,
				'label' 	 => 'Booked'
			),
			array(
				'name'       => 'daystaken',
				'length' 	 => 10,
				'bind'		 => false,
				'readonly'	 => true,
				'required'	 => false,
				'align' 	 => 'center',
				'label' 	 => 'Taken'
			),
			array(
				'name'       => 'daysremaining',
				'type'		 => 'DERIVED',
				'length' 	 => 10,
				'bind'		 => false,
				'readonly'	 => true,
				'required'	 => false,
				'editable'	 => false,
				'function'   => 'daysLeft',
				'sortcolumn' => 'prorataholidayentitlement',
				'align' 	 => 'center',
				'label' 	 => 'Remaining'
			),
			array(
				'name'       => 'absent',
				'length' 	 => 10,
				'bind'		 => false,
				'readonly'	 => true,
				'required'	 => false,
				'align' 	 => 'center',
				'label' 	 => 'Absent'
			),
			array(
				'name'       => 'address',
				'type'		 => 'TEXTAREA',
				'showInView' => false,
				'filter'     => false,
				'label' 	 => 'Address'
			),
			array(
				'name'       => 'description',
				'type'		 => 'TEXTAREA',
				'showInView' => false,
				'filter'     => false,
				'label' 	 => 'Details'
			),
			array(
				'name'       => 'passwd',
				'type'		 => 'PASSWORD',
				'length' 	 => 30,
				'showInView' => false,
				'label' 	 => 'Password'
			),
			array(
				'name'       => 'cpassword',
				'type'		 => 'PASSWORD',
				'length' 	 => 30,
				'bind' 	 	 => false,
				'showInView' => false,
				'label' 	 => 'Confirm Password'
			)
		);
		
	$crud->run();
?>