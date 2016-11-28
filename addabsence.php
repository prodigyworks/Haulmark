<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	
	$mysql_requesteddate = convertStringToDate($_POST['requesteddate']);
	$mysql_startdate = convertStringToDate($_POST['startdate']);;
	$mysql_enddate = convertStringToDate($_POST['enddate']);
	$memberid = mysql_escape_string($_POST['memberid']);
	$startdate_half = (isset($_POST['startdate_half']) && $_POST['startdate_half'] == "on") ? 0 : 1;
	$enddate_half = (isset($_POST['enddate_half']) && $_POST['enddate_half'] == "on") ? 0 : 1;
	$daystaken = networkdays(strtotime($mysql_startdate), strtotime($mysql_enddate));
	$absentreason = mysql_escape_string($_POST['absentreason']);
	$absencetype = mysql_escape_string($_POST['absencetype']);
	
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
	
	if (isset($_POST['absenceid']) && $_POST['absenceid'] != "") {
		$id = $_POST['absenceid'];
		$qry = "UPDATE {$_SESSION['DB_PREFIX']}absence SET " .
				"requesteddate = '$mysql_requesteddate', " .
				"startdate = '$mysql_startdate', " .
				"startdate_half = $startdate_half, " .
				"enddate = '$mysql_enddate', " .
				"enddate_half = $enddate_half, " .
				"daystaken = $daystaken, " .
				"absentreason = '$absentreason', " .
				"absencetype = '$absencetype', " .
				"memberid = $memberid " .
				"WHERE id = $id";
		$result = mysql_query($qry);
		
		if (! $result) {
			logError($qry . " = " . mysql_error());
		}
		
		sendTeamMessage(
				$memberid,
				"Absence updated", 
				"<h4>An absence has been updated for " 
				. GetUserName($memberid) 
				. "</h4><p>Date range of absence " 
				. $_POST['startdate'] 
				. " - " 
				. $_POST['enddate']
				. "</p><h5>Reason:</h5>"
				. $absentreason
			);

		header("location: " . base64_decode($_GET['callee']));	
		
	} else {
		$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}absence (" .
				"requesteddate, startdate, enddate, memberid, daystaken, startdate_half, enddate_half, absencetype, absentreason" .
				") VALUES (" .
				"'$mysql_requesteddate', '$mysql_startdate', '$mysql_enddate', $memberid, $daystaken, $startdate_half, $enddate_half, '$absencetype', '$absentreason'" .
				")";
		$result = mysql_query($qry);
		$id = mysql_insert_id();
		
		if (! $result) {
			logError($qry . " = " . mysql_error());
		}
		
		sendInternalRoleMessage(
				"Admin1",
				"Absence raised", 
				"<h4>An absence has been raised for " 
				. GetUserName($memberid) 
				. "</h4><p>Date range of absence " 
				. $_POST['startdate'] 
				. " - " 
				. $_POST['enddate']
				. "</p><h5>Reason:</h5>"
				. $absentreason
			);
		
		if ($memberid != getLoggedOnMemberID()) {
			sendInternalUserMessage(
					getLoggedOnMemberID(),
					"Absence raised", 
					"<h4>An absence has been raised for " 
					. GetUserName($memberid) 
					. "</h4><p>Date range of absence " 
					. $_POST['startdate'] 
					. " - " 
					. $_POST['enddate']
					. "</p><h5>Reason:</h5>"
					. $absentreason
				);
		}
		
		sendInternalUserMessage(
				$memberid,
				"Absence raised", 
				"<h4>An absence has been raised for " 
				. GetUserName($memberid) 
				. "</h4><p>Date range of absence " 
				. $_POST['startdate'] 
				. " - " 
				. $_POST['enddate']
				. "</p><h5>Reason:</h5>"
				. $absentreason
			);
			
		$sql = "SELECT COUNT(*) AS absences " .
				"FROM {$_SESSION['DB_PREFIX']}absence A " .
				"WHERE A.memberid = $memberid " .
				"AND A.absencetype != 'Leaver' " .
				"AND DATE_ADD(NOW(), INTERVAL - 2 MONTH) < A.startdate ";
		$result = mysql_query($sql);
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				if ($member['absences'] > 3) {
					sendRoleMessage(
							"MANAGEMENT",
							"Absence trigger",
							GetUserName($memberid) 
							. " has had more than 3 separate absences within the last 2 months. "
							. "A back to work interview is required."
						);
						
					saveNotes($memberid);
				}
			}
			
		} else {
			logError($sql . " - " . mysql_error());
		}

		header("location: absenceconfirm.php");	
	}
	
	function saveNotes($memberid) {
		$oldnotes = "";
		$sql = "SELECT notes " .
				"FROM {$_SESSION['DB_PREFIX']}members A " .
				"WHERE A.member_id = $memberid";
		$result = mysql_query($sql);
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				if ($member['notes'] != null) {
					$oldnotes = $member['notes'];
				}
			}
		}
		
		$notes = mysql_escape_string(
					$oldnotes .
					"<b>" .
					date("F j, Y, H:i a") 
					. " : System Manager" 
					. "</b><br>" 
					. "More than 3 separate absences within the last 2 months. "
					. "A back to work interview is required."
				);
				
		$qry = "UPDATE {$_SESSION['DB_PREFIX']}members SET " .
				"notes = '$notes' " .
				"WHERE member_id = $memberid";
		$result = mysql_query($qry);
		
		if (! $result) {
			logError($qry . "=" . mysql_error());
		}
	}
?>