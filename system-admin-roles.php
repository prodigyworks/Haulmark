<?php
	require_once("crud.php");
	
	class RoleCrud extends Crud {
		
		/* Pre command event. */
		public function preCommandEvent() {
			if (isset($_POST['rolecmd'])) {
				if (isset($_POST['members'])) {
					$counter = count($_POST['members']);
		
				} else {
					$counter = 0;
				}
				
				$roleid = $_POST['roleid'];
				$qry = "DELETE FROM {$_SESSION['DB_PREFIX']}userroles WHERE roleid = (SELECT roleid from {$_SESSION['DB_PREFIX']}roles where id = $roleid)";
				$result = mysql_query($qry);
				
				if (! $result) {
					logError($qry . " - " . mysql_error());
				}
		
				for ($i = 0; $i < $counter; $i++) {
					$memberid = $_POST['members'][$i];
					
					$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}userroles (memberid, roleid, metacreateddate, metacreateduserid, metamodifieddate, metamodifieduserid) VALUES ($memberid, (SELECT roleid from {$_SESSION['DB_PREFIX']}roles where id = $roleid), NOW(), " . getLoggedOnMemberID() . ", NOW(), " .  getLoggedOnMemberID() . ")";
					$result = mysql_query($qry);
					
					if (! $result) {
						logError($qry . " - " . mysql_error());
					}
				};
			}
		}

		/* Post header event. */
		public function postHeaderEvent() {
?>
			<script src='js/jquery.picklists.js' type='text/javascript'></script>
			
			<div id="roleDialog" class="modal">
				<form id="membersForm" name="membersForm" method="post">
					<input type="hidden" id="roleid" name="roleid" />
					<input type="hidden" id="rolecmd" name="rolecmd" value="X" />
					<select class="listpicker" name="members[]" multiple="true" id="members" >
						<?php createComboOptions("member_id", "fullname", "{$_SESSION['DB_PREFIX']}members", "", false); ?>
					</select>
				</form>
			</div>
<?php
		}
		
		/* Post script event. */
		public function postScriptEvent() {
?>
			$(document).ready(function() {
					$("#members").pickList({
							removeText: 'Remove Member',
							addText: 'Add Member',
							testMode: false
						});
					
					$("#roleDialog").dialog({
							autoOpen: false,
							modal: true,
							width: 800,
							title: "Roles",
							buttons: {
								"Save": function() {
									$("#membersForm").submit();
								},
								Cancel: function() {
									$(this).dialog("close");
								}
							}
						});
				});
				
			function userRoles(roleid) {
				getJSONData('finduserroles.php?id=' + roleid, "#members", function() {
					$("#membersForm #roleid").val(roleid);
					$("#roleDialog").dialog("open");
				});
			}
<?php
		}
	}
	
	$crud = new RoleCrud();
	$crud->dialogwidth = 900;
	$crud->title = "Roles";
	$crud->table = "{$_SESSION['DB_PREFIX']}roles";
	$crud->sql = "SELECT A.*, B.label
				  FROM {$_SESSION['DB_PREFIX']}roles A
				  LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}pages B
				  ON B.pageid = A.defaultpageid
				  ORDER BY A.roleid";
	$crud->subapplications = array(
			array(
				'title'		  => 'User Roles',
				'imageurl'	  => 'images/user.png',
				'script' 	  => 'userRoles'
			)
		);
	$crud->columns = array(
			array(
				'name'       => 'id',
				'length' 	 => 20,
				'pk'		 => true,
				'bind'		 => false,
				'editable'	 => false,
				'showInView' => false,
				'label' 	 => 'ID'
			),
			array(
				'name'       => 'roleid',
				'length' 	 => 40,
				'label' 	 => 'Role'
			),
			array(
				'name'       => 'priority',
				'length' 	 => 10,
				'align'		 => 'right',
				'label' 	 => 'Priority'
			),
			array(
				'name'       => 'defaultpageid',
				'type'       => 'DATACOMBO',
				'length' 	 => 20,
				'label' 	 => 'Default Page',
				'table'		 => 'pages',
				'table_id'	 => 'pageid',
				'showInVIew' => false,
				'alias'		 => 'label',
				'required'	 => false,
				'table_name' => 'label'
			),
			array(
				'name'       => 'description',
				'length' 	 => 120,
				'label' 	 => 'Usage'
			),
			array(
				'name'       => 'systemrole',
				'length' 	 => 17,
				'label' 	 => 'System Role',
				'type'       => 'COMBO',
				'options'    => array(
						array(
							'value'		=> 'Y',
							'text'		=> 'Yes'
						),
						array(
							'value'		=> 'N',
							'text'		=> 'No'
						)
					)
			)
		);
		
	$crud->run();
?>
