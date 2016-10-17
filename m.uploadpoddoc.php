<?php 
	require_once("system-mobileheader.php"); 
?>
	<style>
		#podform {
			margin-top: 50px;
		}
		
		label {
			font-size:17px;
			font-weight: bold;
		}
	</style>
	<center>
		<div class="upabit">
			<a href="m.uploadpod.php">
				<img alt="" src="images/back.png" height=30 />
			</a>
		</div>
<?php 
	$bookingid = $_GET['id'];
	$sql = "SELECT 
			AA.place, AA.bookingid, A.id, A.pallets, 
			B.name
			FROM {$_SESSION['DB_PREFIX']}bookingleg AA
			INNER JOIN {$_SESSION['DB_PREFIX']}booking A
			ON A.id = AA.bookingid
			INNER JOIN {$_SESSION['DB_PREFIX']}customer B
			ON B.id = A.customerid
			WHERE AA.id = $bookingid";
	
	$result = mysql_query($sql);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			echo date("d/m/Y H:i");
			echo "<br><br>" . $member['name'];
			echo "<br>" . $member['place'];
			
			$bookingid = $member['bookingid'];
		}
		
	} else {
		logError("$sql - " . mysql_error());
	}
?>
		<form id="podform" onsubmit="return validate()" enctype="multipart/form-data" method="POST" action="m.uploadpoddocsave.php">
			<input type="hidden" id="bookingid" name="bookingid" value="<?php echo $bookingid; ?>" />
			<label>Reference (Optional)</label>
			<br>
			<input type="text" id="reference" name="reference" style="width:50%" />
			<br>
			<br>
			<br>
			<br>
			<label>Take Picture</label>
			<br>
			<input type="file" id="imagefile" name="imagefile" style="width:90%" />
			<br>
			<br>
			<input type="submit" />
		</form>
	</center>
	<script>
		function validate() {
			
			return true;
		}
	</script>
<?php 
	require_once("system-mobilefooter.php"); 
?>
