<?php
include ("SimpleImage.php");

function getImageData($name, $maxHeight = 1000, $maxWidth = 1000) {
	$imageid = 0;
	 
	// make sure it's a genuine file upload
	if (is_uploaded_file($_FILES[$name]['tmp_name'])) {
	  // replace any spaces in original filename with underscores
	  $filename = str_replace(' ', '_', $_FILES[$name]['name']);
	  // get the MIME type 
	  $mimetype = $_FILES[$name]['type'];
	  
	  if ($mimetype == 'image/pjpeg') {
	  	  $mimetype= 'image/jpeg';
	  }
	  
	  // create an array of permitted MIME types
	  
	  $permitted = array('image/gif', 'image/jpeg', 'image/png', 'image/x-png');
	
	 // upload if file is OK
	 if (in_array($mimetype, $permitted)) {
	 	
	   switch ($_FILES[$name]['error']) {
	     case 0:
	       // get the width and height
	       $size = getimagesize($_FILES[$name]['tmp_name']);
	       $width = $size[0];
	       $height = $size[1];
	       
	       if ($width > $maxWidth || $height > $maxHeight) {
		       $image = new SimpleImage();
		       $image->load($_FILES[$name]['tmp_name']);
		       
		       if (($width - $maxWidth) > ($height - $maxHeight)) {
			       $image->resizeToWidth($maxWidth);
			       
		       } else {
			       $image->resizeToHeight($maxHeight);
		       }
		       
			   ob_start();
			   $image->output();
			   $binimage = ob_get_clean();
	       		  
	       } else {
		       $binimage = file_get_contents($_FILES[$name]['tmp_name']);
	       }
	       
	       $image = mysql_real_escape_string($binimage);
	       $filename = mysql_escape_string($_FILES[$name]['name']);
	       $description = mysql_escape_string($_POST['description']) ;
	       
			$result = mysql_query("INSERT INTO {$_SESSION['DB_PREFIX']}images " .
					"(description, name, mimetype, image, imgwidth, imgheight, metacreateddate, metacreateduserid, metamodifieddate, metamodifieduserid) " .
					"VALUES " .
					"('$description', '$filename', '$mimetype', '$image', $width, $height, NOW(), " . getLoggedOnMemberID() . ", NOW(), " .  getLoggedOnMemberID() . ")");
					
			if (! $result) {
				throw new Exception("Cannot persist image data ['$filename']:" . mysql_error());
			} 
			
    		$imageid = mysql_insert_id();
		   
          break;
        case 3:
        case 6:
        case 7:
        case 8:
			throw new Exception("Error uploading $filename. Please try again.");

          break;
        case 4:
			throw new Exception("You didn't select a file to be uploaded.");
      }
	 } else {
			throw new Exception("$filename is not an image.");
	 }
	}
	
	return $imageid;
}


function getFileData($name, $sessionid = null, $maxHeight = 1000, $maxWidth = 1000, $expirydate = null, $roleid = null) {
	$imageid = 0;
	$memberid = getLoggedOnMemberID();
	
	// make sure it's a genuine file upload
	if (is_uploaded_file($_FILES[$name]['tmp_name'])) {
		$filename = str_replace(' ', '_', $_FILES[$name]['name']);
		$mimetype = $_FILES[$name]['type'];
		$images = array('image/gif', 'image/jpeg', 'image/jpg', 'image/png', 'image/x-png');
	
		// upload if file is OK
		if ($_FILES[$name]['size'] > 0) {
			switch ($_FILES[$name]['error']) {
				case 0:
			       	$size = $_FILES[$name]['size'];
					
					if (in_array($mimetype, $images)) {
						// get the width and height
				       	$newsize = getimagesize($_FILES[$name]['tmp_name']);
				       	$width = $newsize[0];
				       	$height = $newsize[1];
				       
				       	if ($width > $maxWidth || $height > $maxHeight) {
					    	$image = new SimpleImage();
					       	$image->load($_FILES[$name]['tmp_name']);
					       
					       	if (($width - $maxWidth) > ($height - $maxHeight)) {
						       	$height = $image->resizeToWidth($maxWidth);
						       	$width = $maxWidth;
						       	
					       	} else {
						       	$width = $image->resizeToHeight($maxHeight);
						       	$height = $maxHeight;
					       	}
					       
					       	ob_start();
						   	$image->output();
						   	$size = ob_get_length();
						   	$binimage = ob_get_clean();
						   	
				       	} else {
							$binimage = file_get_contents($_FILES[$name]['tmp_name']);
				       	}
				       
					} else {
						$binimage = file_get_contents($_FILES[$name]['tmp_name']);
					}

					$image = mysql_real_escape_string($binimage);
			       	$filename = mysql_escape_string($_FILES[$name]['name']);
			       	
			       	if ($sessionid) {
				       	$insert_sessionid = "'$sessionid'";
				       	
			       	} else {
			       		$insert_sessionid = "null";
			       	}
			       	
			       	if ($sessionid) {
				       	$insert_sessionid = "'$sessionid'";
				       	
			       	} else {
			       		$insert_sessionid = "null";
			       	}
			       	
			       	if ($expirydate) {
			       		$insert_expirydate = "'" . convertStringToDate($expirydate) .  "'";
			       		
			       	} else {
			       		$insert_expirydate = "null";
			       	}
			       	
			       	if ($roleid) {
			       		$insert_roleid = "'$roleid'";
			       		
			       	} else {
			       		$insert_roleid = "null";
			       	}
			       	
					$result = mysql_query(
							"INSERT INTO {$_SESSION['DB_PREFIX']}documents 
							 (
							 	name, filename, mimetype, sessionid,
							 	expirydate, roleid,
							 	image, size, createdby, createddate, 
							 	metacreateddate, metacreateduserid, 
							 	metamodifieddate, metamodifieduserid
							 ) 
							 VALUES 
							 (
							 	'$filename', '$filename', '$mimetype', $insert_sessionid,
							 	$insert_expirydate, $insert_roleid,
							 	'$image', '$size', $memberid, NOW(), 
							 	NOW(), $memberid, 
							 	NOW(), $memberid
							 )"
						);
						
					if (! $result) {   
					    logError("Cannot persist document data ['$filename']:" . mysql_error(), false);
						throw new Exception("Cannot persist document data ['$filename']:" . mysql_error());
					} 
					
		    		$imageid = mysql_insert_id();
		         	break;
		         	
		        case 3:
		        case 6:
		        case 7:
		        case 8:
		        	logError("Error uploading $filename. Please try again.", false);
					throw new Exception("Error uploading $filename. Please try again.");
		          	break;
		          	
		        case 4:
		        	logError("You didn't select a file to be uploaded.", false);
		        	throw new Exception("You didn't select a file to be uploaded.");
	      }
	      
	    } else {
		    logError("$filename is either too big or not an image.", false);
	    	throw new Exception("$filename is either too big or not an image.");
	    }
	}
	
	return $imageid;
}
?>
