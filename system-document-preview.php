<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	
	$id = $_POST['id'];
	$name = $_POST['name'];
	$mimetype = "";
	$images = array('image/gif', 'image/jpeg', 'image/jpg', 'image/png', 'image/x-png');
	$sql = "SELECT mimetype 
			FROM {$_SESSION['DB_PREFIX']}documents
			WHERE id = $id";
	
	$result = mysql_query($sql);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$mimetype = $member['mimetype'];
		}
	}
	
	if (! in_array($mimetype, $images)) {
?>
		$("#<?php echo $name; ?>_img").hide();
		$("#<?php echo $name; ?>_frame").show();
		$("#<?php echo $name; ?>_frame_cover").show();
<?php
	} else {
?>
		$("#<?php echo $name; ?>_img").show();
		$("#<?php echo $name; ?>_frame").hide();
		$("#<?php echo $name; ?>_frame_cover").hide();
<?php
	}
?>
