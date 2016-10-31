<?php 
	require_once("system-header.php"); 
	require_once("tinymce.php"); 
	?>
<script src='bookingscriptlibrary-20161018.js' type="text/javascript" charset="utf-8"></script>
<script src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places&region=EU&key=AIzaSyB1DBBtL19Tc4sz0Nl_tmGa014MeHtqjLI" type="text/javascript"></script>
<script src='js/jquery.ui.timepicker.js'></script>
<script type="text/javascript">
	var directionsService = new google.maps.DirectionsService();
    var counter = 1;
	
	function getLatLng(name, address)  {
	    var geocoder = new google.maps.Geocoder();

	    geocoder.geocode(
	    		{ 
	    			address: address,
	    			componentRestrictions: {
	    				country: 'uk'
		    		}
	    		}, 
	    		function( results, status ) {
			        if (status == google.maps.GeocoderStatus.OK ) {
				        $("#" + name + "_lat").val(results[0].geometry.location.lat());
						$("#" + name + "_lng").val(results[0].geometry.location.lng());
						
			        } else {
			        }
			    }
			);            
	}
</script>
<?php 
	$address = "";
	$customerid = getLoggedOnCustomerID();
	$sql = "SELECT * 
			FROM {$_SESSION['DB_PREFIX']}customer
			WHERE id = $customerid";
	
	$result = mysql_query($sql);
	
	if (! $result) {
		logError("$sql - " . mysql_error());
	}
	
	while (($member = mysql_fetch_assoc($result))) {
		$address = $member['postcode'];
	}
?>
<form method="POST" id="bookform" action="customerbookingsave.php" enctype="multipart/form-data">
	<h4><?php echo $_SESSION['title']; ?></h4>
	<div id="map_canvas" class="modal"></div>
	<div class="link2" style="padding:5px"  onclick="addPointBetweenNodesWeb()"><img src="images/add.png" class='pointimage'></img>&nbsp;Add Additional Drop / Collection</div>
	<br>
	<br>
	<table cellspacing=6>
		<tr valign="center">
			<td>
				&nbsp;
			</td>
			<td>
				<table style="table-layout:fixed" width='800px'>
					<tr>
						<td style="width:340px"><b>Destination</b></td>
						<td style="width:117px"><b>Date</b></td>
						<td style="width:76px"><b>Time</b></td>
						<td style="width:232px"><b>Booking Ref</b></td>
						<td style="width:103px"><b>Phone</b></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr valign="center">
			<td valign=top>Collection/Delivery</td>
			<td>
				<input type="hidden" id="base_lng" name="base_lng" />
				<input type="hidden" id="base_lat" name="base_lat" />
				<input type="hidden" id="startdatetime" name="startdatetime" />
				<input type="hidden" id="enddatetime" name="enddatetime" />
						
				<div id="tolocationdiv" class="bookingjourneys">
				</div>
			</td>
		</tr>
		<tr>
			<td>Pallets</td>
			<td>
				<input type="text" id="pallets" name="pallets" required="true" size=2 />
			</td>
		</tr>
		<tr>
			<td>Vehicle Type</td>
			<td>
				<?php createCombo("vehicletypeid", "id", "name", "{$_SESSION['DB_PREFIX']}vehicletype", "", true, false, array(), true, "code"); ?>
			</td>
		</tr>
		<tr>
			<td>PO Attachment</td>
			<td>
				<input type="file" id="po" name="po" style='width:500px' />
			</td>
		</tr>
		<tr>
			<td>Notes</td>
			<td>
				<textarea id="notes" name="notes" class="tinyMCE" rows="5" cols="60"></textarea>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<a href="javascript: if (verifyStandardForm('#bookform')) submit();" class="link1"><em><b>Confirm Booking</b></em></a>
			</td>
		</tr>
	</table>
</form>
<script>
	function submit(e) {
		getLatLng("base", "<?php echo getSiteConfigData()->basepostcode; ?>");
		
		$('#bookform').submit();
		e.preventDefault();
	}

	function addPointBetweenNodesWeb() {
		addPointBetweenNodes(true);
	}

	function initializeMap () {
	}

    function getAverageWaitTime() {
      	return <?php echo getSiteConfigData()->averagewaittime * 60; ?>;
    }		

	$(document).ready(
			function() {
				addPoint(true);
				addPoint(true);

<?php 

?>				
				$("#tolocationdiv .pointimage").hide();
			}
		);
</script>
<?php 
	include("system-footer.php"); 
?>