<?php
	include("system-db.php");
	
	start_db();
	
	if (isMobileUserAgent()) {
		header("location: m.index.php");
		
	} else {
		$roles = ArrayToInClause($_SESSION['ROLES']);
		$sql = "SELECT A.*, B.pagename 
				FROM {$_SESSION['DB_PREFIX']}roles A
				INNER JOIN {$_SESSION['DB_PREFIX']}pages B
				ON B.pageid = A.defaultpageid
				WHERE roleid in ($roles)
				ORDER BY priority
				LIMIT 1";
		$result = mysql_query($sql);
		
		while (($member = mysql_fetch_assoc($result))) {
			$pagename = $member['pagename'];
			
			header("location: $pagename");
			exit(0);
		}
		
		header("location: booking.php");
	}
?>
