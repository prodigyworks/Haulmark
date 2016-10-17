<?php
	$where = "";
	
	if (isset($_GET['id']) || isset($_GET['sessionid'])) {
		include("system-embeddedheader.php"); 
		
	} else {
		include("system-header.php"); 
	}
	
	require_once("confirmdialog.php");
	
	function search() {
		global $where;
		
		if ($_POST['pk1'] == "") {
			$where = "";
			
		} else {
			$where = " WHERE MATCH(name, filename) AGAINST ('{$_POST['pk1']}'  IN BOOLEAN MODE) ";
		}
	}
	
	function delete() {
		if (isset($_POST['pk1'])) {
			$id = $_POST['pk1'];
			$qry = "DELETE FROM {$_SESSION['DB_PREFIX']}documents WHERE id = $id";
			$result = mysql_query($qry);
		}
	}
	
	createConfirmDialog("confirmdialog", "Delete document ?", "deleteDocumentFromDialog");
	
	if (isset($_GET['id']) || isset($_GET['sessionid'])) {
?>
<div style='background-color: white'>
<form id="documentForm" name="documentForm" onsubmit="return validate()" enctype="multipart/form-data" method="POST" action="system-documentupload.php?<?php if (isset($_GET['sessionid'])) echo "sessionid=" . $_GET['sessionid']; else echo "id=" . $_GET['id']; if (isset($_GET['documentcallback'])) echo "&documentcallback=" . $_GET['documentcallback'] . "&identifier=" . $_GET['identifier'] . "&tablename=" . $_GET['table'] . "&primaryidname=" . $_GET['key'];?>">
	<div id="documentDiv">
		<table cellspacing=10>
			<tr>
				<td>
					<label>Title</label>
				</td>
				<td>
					<input type="text" id="title" name="title" style="width:550px" />
				</td>
				<td>
					<label>Expiry Date</label>
				</td>
				<td>
					<input type="text" class="datepicker" id="expirydate" name="expirydate"   />
				</td>
			</tr>
			<tr>
				<td>
					<label>Document</label>
				</td>
				<td>
					<input type="file" id="document" name="document" style="width:550px" />
				</td>
				<td>
					<label>Role</label>
				</td>
				<td>
					<?php createCombo("roleid", "roleid", "roleid", "{$_SESSION['DB_PREFIX']}roles"); ?>
				</td>
			</tr>
		</table>
		<br>
		<input type="submit" style="margin-left:0px; padding:6px" class="link2"  value="Add Document" id="btnHeanerNotes" />
		<br>
		<br>
	</div>
</form>
</div>
<?php

	} else {
?>
<div id="documentDiv">
<div style='padding:1px'>
	<table cellspacing=8>
		<tr>
			<td>
				<label>SEARCH</label>
			</td>
			<td>
				<input type="text" id="search" name="search" style="width:450px; " />
			</td>
			<td>
				<button id="search" name="search" onclick='search()' style='display:inline; padding:6px' class='link2'>Search</button>
			</td>
		</tr>
	</table>
</div>
<form id="documentForm" name="documentForm" onsubmit="return validate()" enctype="multipart/form-data" method="POST" action="system-documentupload.php">
	<div id="documentDiv">
		<label>TITLE</label>
		<input type="text" id="title" name="title" style="width:550px" /><br>
		<br>
		
		<label>DOCUMENT</label>
		<input type="file" id="document" name="document" style="width:750px" /><br>
		<br>
		<input type="submit" style="margin-left:0px; padding:6px" class="link2"  value="Add Document" id="btnHeanerNotes" />
		<br>
		<br>
</form>
</div>
<?php
	}
?>

<?php
	if (isset($_GET['id']) || isset($_GET['sessionid'])) {
?>
<div style="position: absolute; top: 170px; background-color: white;width: 98%; height:335px; overflow-y: scroll" id="documentlist_div">
<?php
	} else {
?>
<div style="height:290px; overflow-y: scroll" id="documentlist_div">
<?php
	}
?>
<style>
	#documentlist_div {
		border:1px solid grey;
	}
	#documentlist {
		table-layout: fixed;
	}
</style>
	<div>
	<table cellpadding=12 class='grid list' id="documentlist" maxrows=18 width=100% cellspacing=0 cellpadding=0>
		<thead>
			<tr>
				<td width='20px'></td>
<?php
	if (! isset($_GET['id']) && ! isset($_GET['sessionid'])) {
?>
<?php
	}
?>
				<td width='30%'>Name</td>
				<td width='32%'>File Name</td>
				<td width='7%' align='right'>Size</td>
				<td width='8%'>Created</td>
				<td width='15%'>Created By</td>
				<td width='8%'>Expires</td>
			</tr>
		</thead>
		<?php
			if (isset($_GET['id'])) {
				$table = $_GET['table'];
				$id = $_GET['id'];
				$key = $_GET['key'];
				$qry = "SELECT A.*, 
						DATE_FORMAT(A.createddate, '%d/%m/%Y') AS createddate,
						DATE_FORMAT(A.expirydate, '%d/%m/%Y') AS expirydate2,
						DATE_FORMAT(A.lastmodifieddate, '%d/%m/%Y') AS lastmodifieddate, 
						B.firstname, B.lastname 
						FROM {$_SESSION['DB_PREFIX']}documents A 
						INNER JOIN {$_SESSION['DB_PREFIX']}members B 
						ON B.member_id = A.createdby 
						INNER JOIN {$_SESSION['DB_PREFIX']}$table C 
						ON C.documentid = A.id 
						WHERE C.$key = $id
						ORDER BY A.id";
						
			} else if (isset($_GET['sessionid'])) {
				$sessionid = $_GET['sessionid'];
				$qry = "SELECT A.*, 
						DATE_FORMAT(A.createddate, '%d/%m/%Y') AS createddate, 
						DATE_FORMAT(A.expirydate, '%d/%m/%Y') AS expirydate2,
						DATE_FORMAT(A.lastmodifieddate, '%d/%m/%Y') AS lastmodifieddate, 
						B.firstname, B.lastname 
						FROM {$_SESSION['DB_PREFIX']}documents A 
						INNER JOIN {$_SESSION['DB_PREFIX']}members B 
						ON B.member_id = A.createdby 
						WHERE A.sessionid = '$sessionid' 
						ORDER BY A.id";
						
			} else {
				$qry = "SELECT A.*, 
						DATE_FORMAT(A.createddate, '%d/%m/%Y') AS createddate, 
						DATE_FORMAT(A.expirydate, '%d/%m/%Y') AS expirydate2,
						DATE_FORMAT(A.lastmodifieddate, '%d/%m/%Y') AS lastmodifieddate, 
						B.firstname, B.lastname 
						FROM {$_SESSION['DB_PREFIX']}documents A 
						INNER JOIN {$_SESSION['DB_PREFIX']}members B 
						ON B.member_id = A.createdby 
						$where 
						ORDER BY A.id";
			}

			$result = mysql_query($qry);
			
			if (! $result) logError("Error: " . mysql_error());
			
			//Check whether the query was successful or not
			if ($result) {
				while (($member = mysql_fetch_assoc($result))) {
					$expired = false;
					
					if ($member['roleid'] != null) { 
						if (! isUserInRole($member['roleid']) && $member['metacreateduserid'] != getLoggedOnMemberID()) { 
							continue;
						}
					}
					
					if ($member['expirydate'] != null) {
						if (strtotime($member['expirydate']) < strtotime(date("Y-m-d"))) {
							$expired = true;
						}
					}
					
					if ($expired) {
						echo "<tr class='expireddocument'>\n";
						
					} else {
						echo "<tr>\n";
					}
					
					if (isUserInRole("ADMIN")) {
						echo "<td width='20px' title='Delete' onclick='deleteDocument(" . $member['id'] . ")'><img src='images/delete.png' /></td>\n";
						
					} else {
						echo "<td width='20px'>&nbsp;</td>\n";
					}
					
					if ($expired) {
						echo "<td>" . $member['name'] . "</td>\n";
						
					} else {
						if ($member['name'] == null || trim($member['name']) == "") {
							echo "<td><a target='_new' href='viewdocuments.php?id=" . $member['id'] . "'>" . $member['filename'] . "</a></td>\n";
	
						} else {
							echo "<td><a target='_new' href='viewdocuments.php?id=" . $member['id'] . "'>" . $member['name'] . "</a></td>\n";
						}
					}
					
					echo "<td>" . $member['filename'] . "</td>\n";
					
					$size = $member['size'];
					
					if ($size > 1073741824) {
						$size = floor($size / 1073741824) . " gb";
						
					} else if ($size > 1048576) {
						$size = floor($size / 1048576) . " mb";
						
					} else if ($size > 1024) {
						$size = floor($size / 1024) . " kb";
						
					} else {
						$size .= " bytes";
					}
					
					echo "<td align='right'>" . $size . "</td>\n";
					echo "<td>" . $member['createddate'] . "</td>\n";
					echo "<td>" . $member['firstname'] . " " . $member['lastname'] . "</td>\n";
					echo "<td>" . $member['expirydate2'] . "</td>\n";
					echo "</tr>\n";
				}
				
			} else {
			    logError($qry . " - " . mysql_error());
			}
		?>
	</table>
	</div>
</div>
<script>
	var selectedDocumentID;
	
	function deleteDocumentFromDialog() {
		call("delete", {pk1: selectedDocumentID});
	}
		
	function search() {
		call("search", {pk1: $("#search").val()});
	}
	
	function deleteDocument(id) {
		selectedDocumentID = id;
		$("#confirmdialog").dialog("open");
	}
	
	function validate() {
		if ($("#title").val() == "") {
			pwAlert("Please enter a title");
			$("#title").focus();
			return false;
		}
		
		if ($("#document").val() == "") {
			pwAlert("Please enter a file");
			$("#document").focus();
			return false;
		}
		
		return true;
	}
		
	$(document).ready(function() {
			$("#document").change(
					function() {
						var val = $(this).val();

						if ($("#title").val() == "") {
							$("#title").val(val.substring(val.lastIndexOf('/') + 1).substring(val.lastIndexOf('\\') + 1));
						}
					}
				);
			
			$("#confirmdialog .confirmdialogbody").html("You are about to remove this document.<br>Are you sure ?");
<?php 
			if (isset($_SESSION['ERRMSG_ARR'])) {
?>
				pwAlert("<?php echo escape_notes($_SESSION['ERRMSG_ARR']); ?>");
<?php
				unset($_SESSION['ERRMSG_ARR']);
			}
?>
		});
</script>
<?php
	if (isset($_GET['id']) || isset($_GET['sessionid']))  {
		include("system-embeddedfooter.php"); 
		
	} else {
		include("system-footer.php"); 
	}
?>
