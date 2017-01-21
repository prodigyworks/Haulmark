<?php 
require_once("system-mobileheader.php");
require_once("businessobjects/BookingClass.php");
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
	$bookingleg = new BookingLegClass();
	$bookingleg->load($_GET['id']);
	
	echo date("d/m/Y H:i");
	echo "<br><br>" . $bookingleg->getBooking()->getCustomer()->getName();
	echo "<br>" . $bookingleg->getPlace();
	
?>
		<form id="podform" onsubmit="return validate()" enctype="multipart/form-data" method="POST" action="m.uploadpoddocsave.php">
			<input type="hidden" id="bookingid" name="bookingid" value="<?php echo $bookingleg->getBookingid(); ?>" />
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
