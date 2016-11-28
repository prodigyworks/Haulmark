<?php
	include("system-db.php");
	
	start_db();
	
	$json = array();
	
	if (isset($_POST['message'])) {
		$message = mysql_escape_string($_POST['message']);
		$memberid = getLoggedOnMemberID();
		
		$sql = "INSERT {$_SESSION['DB_PREFIX']}chat 
				(
					createddate, message, status, memberid, 
					metacreateddate, metamodifieddate
				) 
				VALUES 
				(
					NOW(), '$message', 'N', $memberid, 
					NOW(), NOW()
				)";
		$result = mysql_query($sql);
		
		if (! $result) {
			logError($sql . " - " . mysql_error());
		}
	}
	
	$date = convertStringToDate($_POST['date']);
	$where = "WHERE DATE(createddate) =  '$date'";
	
	if (isset($_POST['lastid']) && $_POST['lastid'] != "") {
		$lastdate = $_POST['lastid'];
		$where .= " AND A.metamodifieddate > '$lastdate'";
	}
	
	$where .= " ORDER BY A.status DESC, id DESC";
	
	$qry = "SELECT
			DATE_FORMAT(A.completeddatetime, '%d/%m/%Y %H:%I') AS completeddatetime,
			DATE_FORMAT(A.createddate, '%d/%m/%Y %H:%I') AS createddate,
			A.status, A.message, A.id, A.metamodifieddate,
			B.fullname  
			FROM {$_SESSION['DB_PREFIX']}chat A 
			INNER JOIN {$_SESSION['DB_PREFIX']}members B 
			ON B.member_id = A.memberid 
			$where";

	$result = mysql_query($qry);
	
	//Check whether the query was successful or not
	if($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$line = array(
					"name" => $member['fullname'],
					"date" => $member['createddate'],
					"text" => $member['message'],
					"status" => $member['status'],
					"completeddatetime" => $member['completeddatetime'],
					"id" => $member['id'],
					"timestamp" => $member['metamodifieddate']
				);  
			
			array_push($json, $line);
		}
		
	} else {
		logError($qry . " - " . mysql_error());
	}
	
	echo json_encode($json); 	
?>
