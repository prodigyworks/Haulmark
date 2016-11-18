<?php
	//Include database connection details
	require_once('system-db.php');
	require_once('sqlfunctions.php');
	
	start_db();
	
	try {
		$imageid = getImageData("image", 100, 100);	
		
	} catch (Exception $e) {
		$errmsg_arr[] = $e->getMessage();
	}
	
	//Array to store validation errors
	$errmsg_arr = array();
	
	//Validation error flag
	$errflag = false;
	
	//Sanitize the POST values
	$fname = clean($_POST['fname']);
	$lname = clean($_POST['lname']);
	$password = clean($_POST['password']);
	$cpassword = clean($_POST['cpassword']);
	$email = clean($_POST['email']);
	$mobile = clean($_POST['mobile']);
	$cemail = clean($_POST['confirmemail']);
	
	if (isset($_POST['customerid'])) {
		$customerid = $_POST['customerid'];
		
	} else {
		$customerid = 0;
	}
	
	if (isset($_POST['supplierid'])) {
		$supplierid = $_POST['supplierid'];
		
	} else {
		$supplierid = 0;
	}
	
	if (isset($_POST['driverid'])) {
		$driverid = $_POST['driverid'];
		
	} else {
		$driverid = 0;
	}
	
	//Input Validations
	if($fname == '') {
		$errmsg_arr[] = 'First name missing';
		$errflag = true;
	}
	if($lname == '') {
		$errmsg_arr[] = 'Last name missing';
		$errflag = true;
	}
	
	if (! isset($_GET['id'])) {
		$login = clean($_POST['login']);
		if($login == '') {
			$errmsg_arr[] = 'Login ID missing';
			$errflag = true;
		}
	}
	
	if($password == '') {
		$errmsg_arr[] = 'Password missing';
		$errflag = true;
	}
	
	if($cpassword == '') {
		$errmsg_arr[] = 'Confirm password missing';
		$errflag = true;
	}
	
	if( strcmp($password, $cpassword) != 0 ) {
		$errmsg_arr[] = 'Passwords do not match';
		$errflag = true;
	}
	
	if( strcmp($email, $cemail) != 0 ) {
		$errmsg_arr[] = 'Email addresses do not match';
		$errflag = true;
	}
	
	$matches = null;
		
	$guid = uniqid();
	$memberid = 0;
	//If there are input validations, redirect back to the registration form
	if($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		header("location: " . $_SERVER['HTTP_REFERER']);
		exit();
	}
	
	if (! isset($_GET['id'])) {
		//Check for duplicate login ID
		if($login != '') {
			$qry = "SELECT * FROM {$_SESSION['DB_PREFIX']}members WHERE login='$login'";
			$result = mysql_query($qry);
			if($result) {
				if(mysql_num_rows($result) > 0) {
					$errmsg_arr[] = 'Login ID already in use';
					$errflag = true;
				}
				@mysql_free_result($result);
			}
		}
		
		//Create INSERT query
		$fullname = "$fname $lname";
		$md5passwd = md5($_POST['password']);
		$currentmemberid = getLoggedOnMemberID();
		$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}members 
				(
					firstname, lastname, login, passwd, fullname, 
					email, mobile, imageid, accepted, guid, status, customerid,
					driverid, supplierid,
					metacreateddate, metacreateduserid, metamodifieddate, metamodifieduserid
				) 
				VALUES
				(
					'$fname','$lname','$login', '$md5passwd', '$fullname', 
					'$email', '$mobile', $imageid, 'Y', '$guid', 'Y', $customerid,
					$driverid, $supplierid,
					NOW(), $currentmemberid, NOW(), $currentmemberid
				)";
		$result = @mysql_query($qry);
		$memberid = mysql_insert_id();
		
		if (! $result) {
			logError("$qry - " . mysql_error());
		}
	
		//Create INSERT query
		$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}userroles
				(
						memberid, roleid, 
						metacreateddate, metacreateduserid, 
						metamodifieddate, metamodifieduserid
				) 
				VALUES
				(
						$memberid, 'PUBLIC', 
						NOW(), $currentmemberid, 
						NOW(), $currentmemberid
				)";
		$result = @mysql_query($qry);
		
		$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}userroles
				(
						memberid, roleid, 
						metacreateddate, metacreateduserid, 
						metamodifieddate, metamodifieduserid
				) 
				VALUES
				(
						$memberid, 'USER', 
						NOW(), $currentmemberid, 
						NOW(), $currentmemberid
				)";
		$result = @mysql_query($qry);
		
		if (isset($_POST['accounttype'])) {
			$accountrole = $_POST['accounttype'];

			$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}userroles
					(
							memberid, roleid, 
							metacreateddate, metacreateduserid, 
							metamodifieddate, metamodifieduserid
					) 
					VALUES
					(
							$memberid, '$accountrole', 
							NOW(), $currentmemberid, 
							NOW(), $currentmemberid
					)";
			$result = @mysql_query($qry);
		}
		
		sendRoleMessage("ADMIN", "User Registration", "User " . $login . " has been registered as a user.<br>Password : " . $_POST['password']);
		sendUserMessage($memberid, "User Registration", "<h3>Welcome $fname $lname.</h3><br>You have been invited to become a member of 'Allegro Haulage Planner Database'.<br>Please click on the <a href='" . getSiteConfigData()->domainurl . "/index.php'>link</a> to activate your account.<br><br><h4>Login details</h4>User ID : $login<br>Password : " . $_POST['password']);
		
		if($result) {
			header("location: system-register-success.php");
	
		} else {
			logError("1 Query failed:" . mysql_error());
		}
		
	} else {
		$memberid = $_GET['id'];
		$md5 = md5($password);
		$me = getLoggedOnMemberID();
		$qry = "UPDATE {$_SESSION['DB_PREFIX']}members SET
				email = '$email', 
				firstname = '$fname', 
				lastname = '$lname', 
				imageid = $imageid, 
				mobile = '$mobile',
				lastaccessdate = NOW(),  
				passwd = '$md5passwd', 
				metamodifieddate = NOW(), 
				metamodifieduserid = $memberid
				WHERE member_id = $memberid";
		$result = mysql_query($qry);

		if (! $result) {
			logError("UPDATE members failed:" . mysql_error());
		}
		
		if (isset($_FILES['imageid'])) {
			$_SESSION['SESS_FIRST_IMAGE'] = $imageid;
		}
		
		$_SESSION['SESS_FIRST_NAME'] = $fname;
		$_SESSION['SESS_LAST_NAME'] = $lname;
		$_SESSION['SESS_IMAGE_ID'] = $imageid;
		
		sendUserMessage($memberid, "User Amendment", "<h3>User amendment.</h3><br>Your details have been amended by the System Administration.<br>Your password has been changed to: <i>$password</i>.");
		
		header("location: system-register-amend.php");
	}
	
	//Check whether the query was successful or not
?>