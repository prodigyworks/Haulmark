<?php
require_once("system-db.php");

function PODEmail($id, $emailaddress) {
	start_db();
	
	$sql = "SELECT A.documentid, B.email, B.name, C.filename, C.compressed, C.mimetype, C.image
			FROM {$_SESSION['DB_PREFIX']}customerpod A
			INNER JOIN {$_SESSION['DB_PREFIX']}customer B
			ON B.id = A.customerid
			INNER JOIN {$_SESSION['DB_PREFIX']}documents C
			ON C.id = A.documentid
			WHERE A.id = $id";
	$result = mysql_query($sql);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$name = $member['name'];
			$documentid = $member['documentid'];
			$mimetype = $member['mimetype'];
			$filename = $member['filename'];
			$compressed = $member['compressed'];
			$image = $member['image'];

			if ($emailaddress != null) {
				$email = $emailaddress;
				$fromname = $name;
				$fromemail = $email;

			} else {
				$email = $member['email'];
				$fromname = getSiteConfigData()->companyname;
				$fromemail = getSiteConfigData()->trafficemail;
			}


			if ($compressed == 1) {
				$image = gzuncompress($image);
			}
			
			if ($email == null || $email == "") {
				throw new Exception("Email not specified for customer [$name]");
			}
			
			$pod = $filename;
			$filename = "uploads/$filename";
			
			unlink($filename);
			
			file_put_contents($filename, $image);
			
			try {
				smtpmailer(
						$email,
						$fromemail,
						$fromname,
						"POD : $pod", 
						"Please find the attached POD $pod.", 
						array($filename)
					);
					
			} catch (Exception $e) {
				throw new $e;
			}
		}
		
	} else {
		throw new Exception(mysql_error());
	}
}
?>