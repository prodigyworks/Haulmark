<?php 
	require_once("system-header.php"); 
	require_once("tinymce.php"); 
?>
<script src='bookingscriptlibrary-20161116.js' type="text/javascript" charset="utf-8"></script>
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
				<input type="hidden" id="customerid" name="customerid" value="<?php echo getLoggedOnCustomerID(); ?>" />
				<input type="hidden" id="duration" name="duration" />
				<input type="hidden" id="miles" name="miles" />
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
				<a href="javascript: submitbooking();" class="link2"><em><b>Confirm Booking</b></em></a>
			</td>
		</tr>
	</table>
</form>
<script>
	var map = null;
    var directionsService;
    var directionsDisplay; 
	var METERS_TO_MILES = 0.000621371;
	var startDateTime = null;
	var endDateTime = null;

	function submitbooking	(e) {
	    if (verifyStandardForm('#bookform')) {
		    $('#bookform').submit();
	    }

		e.preventDefault();
	}
	
    function calculateRate2() {
      	calculateRate(
      			<?php echo getSiteConfigData()->defaultwagesmargin; ?>, 
      			<?php echo getSiteConfigData()->defaultprofitmargin; ?>
      		);
      }

	function addPointBetweenNodesWeb() {
		addPointBetweenNodes(true);
	}

	function initializeMap () {
		var waypoints = [];

		$(".point").each(
				function() {
					waypoints.push({
						stopover: true,
						location: $(this).val()
					});
				}
			);

		getDuration(
				"<?php echo getSiteConfigData()->basepostcode; ?>", 
				"<?php echo getSiteConfigData()->basepostcode; ?>", 
				waypoints,
				function(duration, distance) {
				    var strDate = $("#pointarrivaldate_1").val().split('/');
				    var strTime = $("#pointarrivaltime_1").val().split(':');
				    startDateTime = new Date(strDate[2], strDate[1] - 1, strDate[0], strTime[0], strTime[1]);
				    startDateTime = new Date(startDateTime.getTime() - (duration * 1000));
					
					$("#startdatetime").val(
							startDateTime.getFullYear() + "-" +
							padZero(startDateTime.getMonth() + 1) + "-" + 
							padZero(startDateTime.getDate()) + " " +
							padZero(startDateTime.getHours()) + ":" + 
							padZero(startDateTime.getMinutes())
						);
					
				    strDate = $("#pointarrivaldate_" + (counter - 1)).val().split('/');
				    strTime = $("#pointarrivaltime_" + (counter - 1)).val().split(':');
				    endDateTime = new Date(strDate[2], strDate[1] - 1, strDate[0], strTime[0], strTime[1]);
					endDateTime = new Date(endDateTime.getTime() + (duration * 1000));
					
					$("#enddatetime").val(
							endDateTime.getFullYear() + "-" +
							padZero(endDateTime.getMonth() + 1) + "-" + 
							padZero(endDateTime.getDate()) + " " +
							padZero(endDateTime.getHours()) + ":" + 
							padZero(endDateTime.getMinutes())
						);

					
					$('#miles').val((Math.round( distance * METERS_TO_MILES * 10 ) / 10));	
					$('#duration').val(getTotalDurationBetweenDates(startDateTime, endDateTime));
				}
		);
	}

    function getAverageWaitTime() {
      	return <?php echo getSiteConfigData()->averagewaittime * 60; ?>;
    }		
    
    function getDuration(start, end, waypoints, callback) {
        var duration = 0;
        var distance = 0;
        
    	if (map == null) {
    	    directionsService = new google.maps.DirectionsService();
    	    directionsDisplay = new google.maps.DirectionsRenderer(); 
    	    
    	    var mapOptions = { mapTypeId: google.maps.MapTypeId.ROADMAP, disableDefaultUI: true }
    	    map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
    	    var bounds = new google.maps.LatLngBounds();
    	    
    	    directionsDisplay.setMap(map);
    	    google.maps.event.addListenerOnce(map, 'idle', function () { });
    	}
    	
      	var request = { 
      			origin: start, 
      			destination: end, 
      			provideRouteAlternatives: false,
      			travelMode: google.maps.DirectionsTravelMode.DRIVING,
      			waypoints: waypoints,
    			drivingOptions: {
    				    departureTime: new Date(),
    				    trafficModel: 'pessimistic'
    				},
    			unitSystem: google.maps.UnitSystem.IMPERIAL									
      		};
      
      	directionsService.route(request, function(response, status) {
        		if (status == google.maps.DirectionsStatus.NOT_FOUND) {
//        			pwAlert("Location not found, please provide more detail, e.g. Post code");
        			
        		} else if (status == google.maps.DirectionsStatus.OK) {
            		directionsDisplay.setDirections(response);
    				google.maps.event.trigger(map, "resize");

    			    var legs = response.routes[0].legs;
    				
    			    for(var i=0; i < legs.length; ++i) {
        			    duration += legs[i].duration.value;
        			    distance += legs[i].distance.value;
			        }

    			    duration /= 0.9;

    			    callback(duration, distance);
        		}
      		});

  		return duration;
    }

	$(document).ready(
			function() {
				$("#vehicletypeid").change(initializeMap);
				
				getLatLng("base", "<?php echo getSiteConfigData()->basepostcode; ?>");
				
				addPoint(true);
				addPoint(true);

				customerid_onchange();
<?php 

?>				
				$("#tolocationdiv .pointimage").hide();
			}
		);
</script>
<?php 
	include("system-footer.php"); 
?>