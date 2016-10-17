<?php
	require_once('system-db.php');
	
	function clearSessionDocuments() {
		start_db();
		$sessionid = session_id();
			
		$qry = "DELETE FROM {$_SESSION['DB_PREFIX']}documents
			    WHERE sessionid = '$sessionid'";
		
		$result = mysql_query($qry);
		
		if (! $result) {
			logError($qry . " = " . mysql_error());
		}
	}

	function uploadDocuments($primaryid, $primaryidname, $tablename) {
		start_db();
		
		$memberid = getLoggedOnMemberID();
		$sessionid = session_id();
		
		if ($primaryid != null) {
			$qry = "SELECT A.id FROM {$_SESSION['DB_PREFIX']}documents A 
				    WHERE A.sessionid = '$sessionid' 
				    AND A.id NOT IN (
				    	SELECT documentid 
				    	FROM {$_SESSION['DB_PREFIX']}$tablename 
				    	WHERE documentid = A.id
				    ) 
				    ORDER BY A.id";
			$result = mysql_query($qry);
			
			if (! $result) {
				logError($qry . " = " . mysql_error());
			}
			
			while (($member = mysql_fetch_assoc($result))) {
				$documentid = $member['id'];
				$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}$tablename 
						(
							$primaryidname, documentid, createddate, 
							metacreateddate, metacreateduserid, 
							metamodifieddate, metamodifieduserid
						) 
						VALUES 
						(
							$primaryid, $documentid, NOW(), 
							NOW(), $memberid, 
							NOW(), $memberid
						)";
						
				$itemresult = mysql_query($qry);
				
				if (! $itemresult) {
					logError($qry . " = " . mysql_error());
				}
			}
			
			$qry = "UPDATE {$_SESSION['DB_PREFIX']}documents  SET 
					sessionid = NULL, 
					metamodifieddate = NOW(), 
					metamodifieduserid = $memberid
				    WHERE sessionid = '$sessionid'";
			$result = mysql_query($qry);
			
			if (! $result) {
				logError($qry . " = " . mysql_error());
			}
		}
		
	}
?>