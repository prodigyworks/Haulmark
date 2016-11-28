<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	
	$mysql_requesteddate = convertStringToDate($_POST['requesteddate']);
	$mysql_startdate = convertStringToDate($_POST['startdate']);
	$mysql_enddate = convertStringToDate($_POST['enddate']);
	$memberid = mysql_escape_string($_POST['memberid']);
	$startdate_half = (isset($_POST['startdate_half']) && $_POST['startdate_half'] == "on") ? 0 : 1;
	$enddate_half = (isset($_POST['enddate_half']) && $_POST['enddate_half'] == "on") ? 0 : 1;
	$daystaken = networkdays(strtotime($mysql_startdate), strtotime($mysql_enddate));
	
	$qry = "SELECT * FROM {$_SESSION['DB_PREFIX']}bankholiday A " .
			"WHERE (startdate >= '$mysql_startdate' AND startdate <= '$mysql_enddate') " .
			"OR (enddate >= '$mysql_startdate' AND enddate <= '$mysql_enddate')";

	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
		
	} else {
		while (($member = mysql_fetch_assoc($result))) {
			$daystaken -= networkdays(strtotime($member['startdate']), strtotime($member['enddate']));
		}
	}
	
	if ($daystaken > 0) {
		if ($startdate_half == 1 && date("w", strtotime($mysql_startdate)) > 0 && date("w", strtotime($mysql_startdate)) < 6) {
			$daystaken -= 0.5;
		} 
		
		if ($enddate_half == 1 && date("w", strtotime($mysql_enddate)) > 0 && date("w", strtotime($mysql_enddate)) < 6) {
			$daystaken -= 0.5;
		} 
	}
	
	if (isset($_POST['holidayid']) && $_POST['holidayid'] != "") {
		$id = $_POST['holidayid'];
		$qry = "UPDATE {$_SESSION['DB_PREFIX']}holiday SET " .
				"requesteddate = '$mysql_requesteddate', " .
				"startdate = '$mysql_startdate', " .
				"startdate_half = $startdate_half, " .
				"enddate = '$mysql_enddate', " .
				"enddate_half = $enddate_half, " .
				"daystaken = $daystaken, " .
				"memberid = $memberid " .
				"WHERE id = $id";
		$result = mysql_query($qry);
		
		if (! $result) {
			logError($qry . " = " . mysql_error());
		}
				
		sendInternalRoleMessage(
				"MANAGEMENT",
				"Holiday request updated", 
				"<h4>A holiday requested has been updated for " 
				. GetUserName($memberid) 
				. "</h4><p>Date range of holiday " 
				. $_POST['startdate'] 
				. " - " 
				. $_POST['enddate']
				. "</p>"
			);

		header("location: " . base64_decode($_GET['callee']));	
		
	} else {
		$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}holiday (" .
				"requesteddate, startdate, enddate, memberid, daystaken, startdate_half, enddate_half" .
				") VALUES (" .
				"'$mysql_requesteddate', '$mysql_startdate', '$mysql_enddate', $memberid, $daystaken, $startdate_half, $enddate_half" .
				")";
		$result = mysql_query($qry);
		$id = mysql_insert_id();

		
		if (! $result) {
			logError($qry . " = " . mysql_error());
		}
		
		sendInternalUserMessage(
				$memberid,
				"Holiday request created", 
				"<h4>A holiday request has been created for " 
				. GetUserName($memberid) 
				. "</h4><p>Date range of holiday " 
				. $_POST['startdate'] 
				. " - " 
				. $_POST['enddate']
				. ". This is "
				. $daystaken
				. " days."
				. "</p>"
			);
		
		if (getLoggedOnMemberID() != $memberid) {
			sendInternalUserMessage(
					getLoggedOnMemberID(),
					"Holiday request created", 
					"<h4>A holiday request has been created for " 
					. GetUserName($memberid) 
					. "</h4><p>Date range of holiday " 
					. $_POST['startdate'] 
					. " - " 
					. $_POST['enddate']
					. ". This is "
					. $daystaken
					. " days."
					. "</p>"
				);
		}
		
		sendInternalRoleMessage(
				"HOLIDAY",
				"Holiday request created", 
				"<h4>A holiday request has been created for " 
				. GetUserName($memberid) 
				. "</h4><p>Date range of holiday " 
				. $_POST['startdate'] 
				. " - " 
				. $_POST['enddate']
				. ". This is "
				. $daystaken
				. " days."
				. "</p>"
			);

		header("location: holidayconfirm.php");	
	}
?>5