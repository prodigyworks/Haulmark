<?php 
	require_once("system-header.php"); 
	require_once("tinymce.php"); 
	?>
<script src='js/jquery.ui.timepicker.js'></script>
<script src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places&region=EU&key=AIzaSyB1DBBtL19Tc4sz0Nl_tmGa014MeHtqjLI" type="text/javascript"></script>
<script src='bookingscriptlibrary-20161018.js' type="text/javascript" charset="utf-8"></script>
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

	$(document).ready(
			function() {
				$("#fromplace, #toplace").change(function() {
					setTimeout(
							function() { 
								getLatLng($(this).attr("id"), $(this).val());
							},
							500
						);
					});

		        var options = {
		        		types: ['(cities)'],
		        		region: "uk",
		        		componentRestrictions: {country: ["uk"]}       
		        	};

			    new google.maps.places.Autocomplete(document.getElementById('toplace'), options);
			    new google.maps.places.Autocomplete(document.getElementById('fromplace'), options);
			}
		);
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
	<table cellspacing=6>
		<tr valign="center">
			<td>
				&nbsp;
			</td>
			<td>
				<table style="table-layout:fixed" width='700px'>
					<tr>
						<td style="width:340px"><b>Destination</b></td>
						<td style="width:117px"><b>Date</b></td>
						<td style="width:76px"><b>Time</b></td>
						<td style="width:232px"><b>Booking Ref</b></td>
						<td style="width:100px"><b>Phone</b></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr valign="center">
			<td>Collection Point</td>
			<td>
				<input type="hidden" id="base_lng" name="base_lng" />
				<input type="hidden" id="base_lat" name="base_lat" />
				<input type="hidden" id="fromplace_lng" name="fromplace_lng" />
				<input type="hidden" id="fromplace_lat" name="fromplace_lat" />
				<input type="hidden" id="toplace_lng" name="toplace_lng" />
				<input type="hidden" id="toplace_lat" name="toplace_lat" />
				<div class="bookingjourneys">
					<div>
						<input required="true" type="text" style="width:300px" id="fromplace" name="fromplace" placeholder="Enter a location" onchange="calculateTimeNode(this, 1)" autocomplete="off">&nbsp;<div class='bubble' title='Required field'></div>&nbsp;
						<input class="datepicker bookingdateclass" required="true" type="text" index='0' id="startdatetime"  onchange="calculateTimeNode(this, 1)" name="startdatetime" ><div class='bubble' title='Required field'></div>&nbsp;
						<input class="timepicker bookingtimeclass" required="true" type="text" index='0' id="startdatetime_time" onchange="calculateTimeNode(this, 1)"   name="startdatetime_time"><div class='bubble' title='Required field'></div>&nbsp;
						<input type="text" style="width:200px" id="fromplace_ref" name="fromplace_ref"><div class='bubble' title='Required field'></div>&nbsp;
						<input type="tel" style="width:80px" id="fromplace_phone" name="fromplace_phone"><div class='bubble' title='Required field'></div>
						&nbsp;<img src="images/add.png" class='pointimage' onclick="addPointBetweenNodes()"></img>
					</div>
				</div>
				<div id="tolocationdiv" class="bookingjourneys">
				</div>
			</td>
		</tr>
		<tr valign="center">
			<td>Delivery To</td>
			<td>
				<div class="bookingjourneys">
					<input required="true" type="text" style="width:300px" id="toplace" name="toplace" placeholder="Enter a location" onchange="calculateTimeNode(this, 1)"  autocomplete="off">&nbsp;<div class='bubble' title='Required field'></div>&nbsp;
					<input class="datepicker bookingdateclass" required="true" type="text" id="enddatetime" name="enddatetime" onchange="calculateTimeNode(this, 1)"  ><div class='bubble' title='Required field'></div>&nbsp;
					<input class="timepicker bookingtimeclass" required="true" type="text" id="enddatetime_time" name="enddatetime_time"><div class='bubble' title='Required field'></div>&nbsp;
					<input type="text" style="width:200px" id="toplace_ref" name="toplace_ref"><div class='bubble' title='Required field'></div>&nbsp;
					<input type="tel" style="width:80px" id="toplace_phone" name="toplace_phone"><div class='bubble' title='Required field'></div>
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
				<a href="javascript: if (verifyStandardForm('#bookform')) submit();" class="link1"><em><b>Confirm</b></em></a>
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

	$(document).ready(
			function() {
				$("#fromplace").val("<?php echo $address; ?>").trigger("change");
			}
		);
</script>
<?php 
	include("system-footer.php"); 
?>