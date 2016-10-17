<?php
	require_once('system-db.php');
	require_once('sqlfunctions.php');
	
	start_db();
	
	unset($_SESSION['ERRMSG_ARR']);
	
	try {
	    $imageid = getFileData(
	    		"document", 
	    		session_id(), 
	    		1000, 
	    		1000, 
	    		$_POST['expirydate'], 
	    		$_POST['roleid']
	    	);
	    
	} catch (Exception $e) {
		$_SESSION['ERRMSG_ARR'] = $e->getMessage();
	  	header("location: " . $_SERVER['HTTP_REFERER']);
	}
    
    if (isset($_GET['documentcallback'])) {
	  	header("location: " 
	  			. $_GET['documentcallback'] 
	  			. "?id=" . $_GET['identifier'] 
	  			. "&refer=" . base64_encode($_SERVER['HTTP_REFERER'])
	  			. "&primaryidname=" . $_GET['primaryidname']
	  			. "&tablename=" . $_GET['tablename']
	  		);
	  	
    } else {
	  	header("location: " . $_SERVER['HTTP_REFERER']);
    }
 ?>