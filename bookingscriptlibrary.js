function addPointBetweenNodes() {
	var prevTime = $("#startdatetime_time").val();
	var prevDate = $("#startdatetime").val();
	
	$(".pointcontainer:last").each(
			function() {
				prevTime = $(this).find(".timepicker").val();
				prevDate = $(this).find(".datepicker").val();
			}
		);
		
	var index = addPoint();
	
	$("#pointdate_" + index).val(prevDate);
	$("#pointtime_" + index).val(prevTime);
}

function showRateCard() {
	callAjax(
			"finddata.php", 
			{ 
				sql: "SELECT documentid " + 
					 "FROM hallmark_customer " + 
					 "WHERE id = " + $("#customerid").val()
			},
			function(data) {
				if (data.length == 1) {
	      	    	$("#rateCardDialog").dialog("open");
					$("#rateCardIframe").attr("src", "system-documentviewer.php?id=" + data[0].documentid);
				}
			},
			false
		);
}

 function showPreviousPrices() {
	    var legs = [];
	    var legIndex = 0;
	    
	    $(".point").each(
    		function() {
    			legs[legIndex++] = $(this).val();
    		}
    	);
    
	$.ajax({
			url: "findpreviousprices.php",
			dataType: 'html',
			async: false,
			data: { 
				vehicletypeid: $("#vehicletypeid").val(),
				customerid: $("#customerid").val(),
				pallets: $("#pallets").val(),
				legs: legs
			},
			type: "POST",
			error: function(jqXHR, textStatus, errorThrown) {
				pwAlert("ERROR :" + errorThrown);
			},
			success: function(data) {
				$("#prevPriceDiv").html(data);
      	    	$("#prevPriceDialog").dialog("open");
			}
		});
}


function addPoint() {
    var pointoptions = {
    		types: ['(cities)']
    	};
	var html = "<div id='container_" + counter + "' class='pointcontainer' index='" + counter + "' style='padding-top:3px'>\n" +
	   		   "	<input class='point' id='point_" + counter  + "' index='" + counter + "' required='true' type='text' style='width:300px' name='point_" + counter + "'>&nbsp;\n" +
			   "	<div class='bubble' title='Required field'></div>\n" +
			   "	<input class='datepicker' required='true' index='" + counter + "' type='text' id='pointdate_" + counter +  "' name='pointdate_" + counter + "'>\n" +
			   "	<div class='bubble' title='Required field'></div>\n" +
			   "	<input class='timepicker' required='true' index='" + counter + "' type='text' id='pointtime_" + counter + "' name='pointtime_" + counter + "'>\n" +
			   "	<div class='bubble' title='Required field'></div>\n" +
	   		   "    <input type='text' class='reference' style='width:200px' id='point_" + counter + "_ref' name='point_" + counter + "_ref'>\n" +
			   "	<div class='bubble' title='Required field'></div>\n" +
			   "	<input type='text' class='phone' style='width:80px' id='point_" + counter + "_phone' name='point_" + counter + "_phone'>\n" +
			   "	<div class='bubble' title='Required field'></div>\n" +
			   "	<img src='images/minus.gif' class='pointimage' onclick='removePoint(this)'></img>" +
	   		   "	<input id='point_" + counter  + "_lng' type='hidden' name='point_" + counter + "_lng'>\n" +
	   		   "	<input id='point_" + counter  + "_lat' type='hidden' name='point_" + counter + "_lat'>\n" +
			   "</div>";
	$("#tolocationdiv").append(html);
	$("#pointdate_" + counter).datepicker({dateFormat: "dd/mm/yy"});
	$("#pointtime_" + counter).timepicker();
	
    var input = document.getElementById('point_' + counter);
    new google.maps.places.Autocomplete(input, pointoptions);
    var pacContainerInitialized = false; 
    
    $('#point_' + counter).keypress(function() { 
        if (! pacContainerInitialized) { 
               $('.pac-container').css('z-index', '9999'); 
               pacContainerInitialized = true; 
       	} 
	}); 


    $("#customerid").change(checkBookingStatus);
    $("#trailerid").change(checkBookingStatus);
    $("#vehicleid").change(checkBookingStatus);
    $("#driverid").change(checkBookingStatus);
    $("#vehicletypeid").change(checkBookingStatus);
    
	$('#point_' + counter).change(calculatePoint);
	$('#pointdate_' + counter).change(calculateTime);
	$('#pointtime_' + counter).change(calculateTime);

    counter++;
    
    $("#bookingpoints").val($(".pointcontainer").length);
    
    return counter - 1;
}

function checkBookingStatus() {
	if ($("#statusid").val() <= 4) {
		if ($("#customerid").val() != 0 &&
			$("#trailerid").val() != 0 &&
			$("#vehicletypeid").val() != 0 &&
			$("#vehicleid").val() != 0 &&
			$("#driverid").val() != 0) {
			
			$("#statusid").val("4");
	
		} else {
			$("#statusid").val("1");
		}
	}
}

function removePoint(node) {
	var parent = $(node).parent();
	var grandparent = parent.parent();

	$("#" + parent.attr("id")).remove();
    $("#bookingpoints").val($(".pointcontainer").length);
}

function calculatePoint() {
	calculateTimeNode(this, 0);
}

function calculateTime() {
	calculateTimeNode(this, 1);
}

function calculateTimeNode(node, addition) {
	node = $(node);

	setTimeout(
			function() { 
				getLatLng(node.attr("id"), node.val());

				var fromIndex = parseInt(node.attr("index"));
				var waypoints = [];
				  
				$(".pointcontainer .point").each(
						function() {
							waypoints.push({
								stopover: true,
								location: $(this).val()
							});
						}
					);
					
				initializeMap($("#fromplace").val(), $("#toplace").val(), waypoints, fromIndex + addition);
				
			},
			1000
		);

}

function loadLegs(id) {
	callAjax(
			"finddata.php", 
			{ 
				sql: "SELECT B.id, A.fromplace, A.fromplace_ref, A.fromplace_phone, A.toplace, A.toplace_ref, A.toplace_phone, B.place, B.place_lng, place_lat, B.reference, B.phone, " +
					 "DATE_FORMAT(B.departuretime, '%d/%m/%Y') AS departuredate, " +
					 "DATE_FORMAT(B.departuretime, '%H:%i') AS departuretime " + 
					 "FROM hallmark_booking A " +
					 "INNER JOIN hallmark_bookingleg B " + 
					 "ON B.bookingid = A.id " +
					 "WHERE A.id = " + id + " " +
					 "ORDER BY B.id"
			},
			function(data) {
				if (data.length > 0) {
					for (var i = 1; i <= data.length; i++) {
						var node = data[i - 1];
						addPoint();
						
						$("#point_" + i).val(node.place);
						$("#point_" + i + "_lat").val(node.place_lat);
						$("#point_" + i + "_lng").val(node.place_lng);
						$("#point_" + i + "_ref").val(node.reference);
						$("#point_" + i + "_phone").val(node.phone);
						$("#pointdate_" + i).val(node.departuredate);
						$("#pointtime_" + i).val(node.departuretime.trim());
					}
				}
			},
			false
		);
}

function getJourneyTime(startTime, startDate, nextDate, elapsedTime) {
    var pointmin2 = Math.round( elapsedTime / 60 ) % 60;
    var pointhr2 = Math.floor( elapsedTime / 3600 );
	var prevhr = startTime.trim().substr(0, 2);
	var prevmin = startTime.trim().substr(3, 5);
	var legtotal = (prevhr * 3600) + (prevmin * 60);
	var timeTaken = elapsedTime + parseInt(legtotal);
    var pointmin = Math.round( timeTaken / 60 ) % 60;
    var pointhr = (Math.floor( timeTaken / 3600 ) % 24);
    var dateadd = (Math.floor( (timeTaken / 3600 ) / 24));
    
    if (dateadd > 0) {
	    var strDate = startDate.split('/');
    	var date = new Date(strDate[2], strDate[1] - 1, strDate[0]);
    	
    	date.setTime(date.getTime() + (dateadd * 24 * 60 * 60 * 1000));
    	
    	$("#" + nextDate).val(
    			padZero(date.getDate()) + "/" + 
    			padZero(date.getMonth() + 1) + "/" + 
    			date.getFullYear()
    		);
    	
    } else {
    	$("#" + nextDate).val(startDate);
    }
    
	return padZero(pointhr) + ":" + padZero(pointmin);
}

function fetchOverHeadRates() {
	callAjax(
			"finddata.php", 
			{ 
				sql: "SELECT A.* " +
					 "FROM hallmark_vehicletype A " + 
					 "WHERE A.id = " + $("#vehicletypeid").val()
			},
			function(data) {
				if (data.length > 0) {
					var node = data[0];
					
					$("#allegrodayrate").val(node.allegrodayrate);
					$("#agencydayrate").val(node.agencydayrate);
					$("#vehiclecostoverhead").val(node.vehiclecostpermile);
					$("#fuelcostoverhead").val(node.fuelcostpermile);
					$("#maintenanceoverhead").val(node.overheadcostpermile);
					
					calculateRate2();
				}
			
			},
			false
		);
}

function vehicletypeid_onchange() {
	fetchOverHeadRates();

	$.ajax({
			url: "createvehiclecombo.php",
			dataType: 'html',
			async: false,
			data: { 
				vehicletypeid: $("#vehicletypeid").val()
			},
			type: "POST",
			error: function(jqXHR, textStatus, errorThrown) {
				pwAlert("ERROR :" + errorThrown);
			},
			success: function(data) {
				$("#vehicleid").html(data);
			}
		});
}

function vehicleid_onchange() {
	callAjax(
			"finddata.php", 
			{ 
				sql: "SELECT A.vehicletypeid, A.usualtrailerid " +
					 "FROM hallmark_vehicle A " +
					 "WHERE A.id = " + $("#vehicleid").val()
			},
			function(data) {
				if (data.length > 0) {
					var node = data[0];
					var vehicleid = $("#vehicleid").val();
					
					if ($("#vehicletypeid").val() != node.vehicletypeid) {
						$("#vehicletypeid").val(node.vehicletypeid).trigger("change");
						$("#vehicleid").val(vehicleid);
					}
					
					if ($("#trailerid").val() == "0") {
						$("#trailerid").val(node.usualtrailerid);
					}
				}
			
			}
		);
}

function customerid_onchange() {
	callAjax(
			"finddata.php", 
			{ 
				sql: "SELECT A.collectionpoint, A.standardratepermile FROM hallmark_customer A " +
					 "WHERE A.id = " + $("#customerid").val()
			},
			function(data) {
				if (data.length > 0) {
					var node = data[0];
					
					$("#point_1").val(node.collectionpoint).trigger("change");
					$("#customercostpermile").val(node.standardratepermile);
					
					calculateRate2();
				}
			
			}
		);
}

function driverid_onchange() {
	$(".drivernamerow").hide();

	callAjax(
			"finddata.php", 
			{ 
				sql: "SELECT agencydriver, usualvehicleid, usualtrailerid FROM hallmark_driver WHERE id = " + $("#driverid").val()
			},
			function(data) {
				if (data.length > 0) {
					var node = data[0];
					
					$("#agencydriver").val(node.agencydriver);
					
					if (node.usualvehicleid != null && node.usualvehicleid != 0) {
						$("#vehicleid").val(node.usualvehicleid).trigger("change");
					}
					
					if (node.usualtrailerid != null && node.usualtrailerid != 0) {
						$("#trailerid").val(node.usualtrailerid).trigger("change");
					}
					
					calculateRate2();
					
					if (node.agencydriver == "Y") {
						$(".drivernamerow").show();
						$(".drivernamerow input").attr("required", true);
	
					} else {
						$(".drivernamerow").hide();
						$(".drivernamerow").hide();
						$(".drivernamerow input").attr("required", false);
						$("#drivername").val("");
					}				
				}
			},
			false
		);
}

function calculateRate(defaultwagesmargin, defaultprofitmargin) {
	var duration = $("#duration").val();
	var dayrate;
	
	if ($("#agencydriver").val() == "Y") {
		dayrate = parseFloat($("#agencydayrate").val());
		
	} else {
		dayrate = parseFloat($("#allegrodayrate").val());
	}
	
	var wages = (duration * dayrate) * (1 + (defaultwagesmargin / 100));
	var miles = parseFloat($("#miles").val());
	var totalcost = 
			wages + 
			((parseFloat($("#vehiclecostoverhead").val()) + 
			  parseFloat($("#fuelcostoverhead").val()) + 
			  parseFloat($("#maintenanceoverhead").val())) * 
			  miles
			);
	
	$("#wages").val(wages);

	if ($("#customercostpermile").val() != "0.00" && $("#customercostpermile").val() != "") {
		totalcost = parseFloat($("#customercostpermile").val()) * miles;
		
		if (isNaN(totalcost)) {
			totalcost = 0;
		}
		
	} else {
		if (isNaN(totalcost)) {
			totalcost = 0;
		}
	}
	
	$("#rate").val(new Number(totalcost).toFixed(2));
	$("#charge").val(new Number(totalcost * (1 + (defaultprofitmargin / 100))).toFixed(2));
}
