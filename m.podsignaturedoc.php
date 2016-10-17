<?php 
	require_once("system-mobileheader.php"); 
?>
	<style>
		#poddiv {
			margin-top: 5px;
		}
		
		label {
			font-size:17px;
			font-weight: bold;
		}
	</style>
	<center>
		<div class="upabit">
			<a href="m.podsignature.php">
				<img alt="" src="images/back.png" height=30 />
			</a>
		</div>
	</center>
	<center id="poddiv">
		<h4>
<?php 
	$legid = $_GET['id'];
	$sql = "SELECT 
			AA.place, A.id, A.pallets, 
			B.name
			FROM {$_SESSION['DB_PREFIX']}bookingleg AA
			INNER JOIN {$_SESSION['DB_PREFIX']}booking A
			ON A.id = AA.bookingid
			INNER JOIN {$_SESSION['DB_PREFIX']}customer B
			ON B.id = A.customerid
			WHERE AA.id = $legid
			ORDER BY AA.id";
	
	$result = mysql_query($sql);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			echo date("d/m/Y H:i");
			echo "<br><br>" . $member['name'];
			echo "<br>" . $member['place'];
			
			$pallets = $member['pallets'];
		}
		
	} else {
		logError("$sql - " . mysql_error());
	}
?>
		</h4>
		<form id="podform" onsubmit="return validate()" enctype="multipart/form-data" method="POST" action="m.podsignaturedocsave.php">
			<input type="hidden" id="bookingid" name="bookingid" value="<?php echo $legid; ?>" />
			<br>
			<label>Pallets</label>
			<br>
			<input type="text" id="pallets" name="pallets" size=4 value="<?php echo $pallets; ?>" />
			<br>
			<br>
			<label>Are goods delivered in good condition</label>
			<br>
			<input type="checkbox" id="chkdamage" checked />
			<div id="damageddiv" class="modal">
				<br>
				<label>Damage</label>
				<br>
				<textarea id="damagedtext" name="damagedtext" cols=60 rows=5></textarea>
				<br>
				<label>Photo</label>
				<br>
				<input type="file" id="damagedimageid" name="damagedimageid" size=40  />
			</div>
<?php 
	require_once('signature.php');

	addSignatureForm();
?>
			<br>
			<br>
			<input type="submit" />
		</form>
	</center>
	<script>
		$(document).ready(
				function() {
					$("#chkdamage").click(
							function() {
								if (! $(this).attr("checked")) {
									$("#damageddiv").show();
									
								} else {
									$("#damageddiv").hide();
								}
							}
						);
					
			      	$('.sigPad').signaturePad(
			      			{
			      				validateFields: false
			      			}
						);
				}
			);
		function validate() {
			
			return true;
		}
	</script>
<?php 
	require_once("system-mobilefooter.php"); 
?>
