function setupEvents() {
	$("#charge").change(
		function() {
			$("#fixedprice").attr("checked", true);
		}
	);
	$("#customerid").change(checkBookingStatus);
	$("#trailerid").change(checkBookingStatus);
	$("#vehicleid").change(checkBookingStatus);
	$("#driverid").change(checkBookingStatus);
	$("#vehicletypeid").change(checkBookingStatus);

}

function convertDate(str) {
	return str.substring(6, 10) + "-" + str.substring(3, 5) + "-" + str.substring(0, 2);
}

function validateForm(id) {
	var sql;
	var isvalid = true;
	var ucstartdate = ($("#startdatetime").val()) + " " + $("#startdatetime_time").val();
	var ucenddate = ($("#enddatetime").val()) + " " + $("#enddatetime_time").val();
	var startdate = convertDate($("#startdatetime").val()) + " " + $("#startdatetime_time").val();
	var enddate = convertDate($("#enddatetime").val()) + " " + $("#enddatetime_time").val();

	if ($("#vehicleid").val() != "0") {
		if (id) {
			sql = "SELECT A.startdatetime, A.enddatetime FROM hallmark_booking A " +
				  "WHERE A.id != '" + id + "' " +
				  "AND   A.statusid IN (4, 5, 6, 7, 8) " +
				  "AND   A.vehicleid = '" + $("#vehicleid").val() + "' " +
				  "AND ((A.startdatetime >= '" + enddate + "' AND A.startdatetime < '" + enddate + "') " +
				  "OR   (A.enddatetime > '" + enddate + "' AND A.enddatetime < '" + enddate + "') " +
				  "OR   (A.startdatetime <= '" + enddate + "' AND A.enddatetime >= '" + enddate + "')) ";
					  
		} else {
			sql = "SELECT A.startdatetime, A.enddatetime FROM hallmark_booking A " +
				  "WHERE A.vehicleid = '" + $("#vehicleid").val() + "' " +
				  "AND   A.statusid IN (4, 5, 6, 7, 8) " +
				  "AND ((A.startdatetime >= '" + startdate + "' AND A.startdatetime < '" + enddate + "') " +
				  "OR   (A.enddatetime > '" + startdate + "' AND A.enddatetime < '" + enddate + "') " +
				  "OR   (A.startdatetime <= '" + startdate + "' AND A.enddatetime >= '" + enddate + "')) ";
		}

		callAjax(
				"finddata.php", 
				{ 
					sql: sql
				},
				function(data) {
					if (data.length > 0) {
						pwAlert("Warning: Vehicle already occupied between '" + ucstartdate + "' and '" + ucenddate + "'");
						isvalid = false;
					}
				},
				false
			);
			
		if (! isvalid) {
			return false;
		}
	}
	
	if ($("#driverid").val() != "0") {
		if (id) {
			sql = "SELECT A.startdatetime, A.enddatetime FROM hallmark_booking A " +
				  "WHERE A.id != '" + id + "' " +
				  "AND   A.statusid IN (4, 5, 6, 7, 8) " +
				  "AND   A.driverid = '" + $("#driverid").val() + "' " +
				  "AND ((A.startdatetime >= '" + startdate + "' AND A.startdatetime < '" + enddate + "') " +
				  "OR   (A.enddatetime > '" + startdate + "' AND A.enddatetime < '" + enddate + "') " +
				  "OR   (A.startdatetime <= '" + startdate + "' AND A.enddatetime >= '" + enddate + "')) ";
					  
		} else {
			sql = "SELECT A.startdatetime, A.enddatetime FROM hallmark_booking A " +
				  "WHERE A.driverid = '" + $("#driverid").val() + "' " +
				  "AND   A.statusid IN (4, 5, 6, 7, 8) " +
				  "AND ((A.startdatetime >= '" + startdate + "' AND A.startdatetime < '" + enddate + "') " +
				  "OR   (A.enddatetime > '" + startdate + "' AND A.enddatetime < '" + enddate + "') " +
				  "OR   (A.startdatetime <= '" + startdate + "' AND A.enddatetime >= '" + enddate + "')) ";
		}
		
		callAjax(
				"finddata.php", 
				{ 
					sql: sql
				},
				function(data) {
					if (data.length > 0) {
						pwAlert("Warning: Driver already occupied between '" + ucstartdate + "' and '" + ucenddate + "'");
						isvalid = false;
					}
				},
				false
			);
			
		if (! isvalid) {
			return false;
		}
	}
	
	if ($("#trailerid").val() != "0" &&  $("#trailerid option[value=" +  $("#trailerid").val() + "]"). text() != "N/A") {
		if (id) {
			sql = "SELECT A.startdatetime, A.enddatetime FROM hallmark_booking A " +
				  "WHERE A.id != '" + id + "' " +
				  "AND   A.statusid IN (4, 5, 6, 7, 8) " +
				  "AND   A.trailerid = '" + $("#trailerid").val() + "' " +
				  "AND ((A.startdatetime >= '" + startdate + "' AND A.startdatetime < '" + enddate + "') " +
				  "OR   (A.enddatetime > '" + startdate + "' AND A.enddatetime < '" + enddate + "') " +
				  "OR   (A.startdatetime <= '" + startdate + "' AND A.enddatetime >= '" + enddate + "')) ";
					  
		} else {
			sql = "SELECT A.startdatetime, A.enddatetime FROM hallmark_booking A " +
				  "WHERE A.trailerid = '" + $("#trailerid").val() + "' " +
				  "AND   A.statusid IN (4, 5, 6, 7, 8) " +
				  "AND ((A.startdatetime >= '" + startdate + "' AND A.startdatetime < '" + enddate + "') " +
				  "OR   (A.enddatetime > '" + startdate + "' AND A.enddatetime < '" + enddate + "') " +
				  "OR   (A.startdatetime <= '" + startdate + "' AND A.enddatetime >= '" + enddate + "')) ";
		}
		
		callAjax(
				"finddata.php", 
				{ 
					sql: sql
				},
				function(data) {
					if (data.length > 0) {
						pwAlert("Warning: Trailer already occupied between '" + ucstartdate + "' and '" + ucenddate + "'");
						isvalid = false;
					}
				},
				false
			);
			
		if (! isvalid) {
			return false;
		}
	}
	
	var bookingindex = 0;
	var prevtime = 0;
	var prevdate = 0;
	
	$(".bookingjourneys").each(
			function() {
				var date = $(this).find(".bookingdateclass").val();
				var time = $(this).find(".bookingtimeclass").val();
				
				if (bookingindex++ > 0) {
					var olddate = new Date(Date.parse(convertDate(prevdate) + " " + prevtime));
					var newdate = new Date(Date.parse(convertDate(date) + " " + time));
					
					if (newdate < olddate) {
						pwAlert("Date / Time must be in chronological order.");
						isvalid = false;
					}
				}
				
				prevdate = date;
				prevtime = time;
			}
		);
	
	if (! isvalid) {
		return false;
	}
		
	return true;
}

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
    		types: ['(cities)'],
    		componentRestrictions: {country: ["uk"]}   
    	};
	var html = "<div id='container_" + counter + "' class='pointcontainer bookingjourneys' index='" + counter + "' style='padding-top:3px'>\n" +
	   		   "	<input class='point' id='point_" + counter  + "' index='" + counter + "' required='true' type='text' style='width:300px' name='point_" + counter + "'>&nbsp;\n" +
			   "	<div class='bubble' title='Required field'></div>\n" +
			   "	<input class='datepicker bookingdateclass' required='true' index='" + counter + "' type='text' id='pointdate_" + counter +  "' name='pointdate_" + counter + "'>\n" +
			   "	<div class='bubble' title='Required field'></div>\n" +
			   "	<input class='timepicker bookingtimeclass' required='true' index='" + counter + "' type='text' id='pointtime_" + counter + "' name='pointtime_" + counter + "'>\n" +
			   "	<div class='bubble' title='Required field'></div>\n" +
	   		   "    <input type='text' class='reference' style='width:200px' id='point_" + counter + "_ref' name='point_" + counter + "_ref'>\n" +
			   "	<div class='bubble' title='Required field'></div>\n" +
			   "	<input type='tel' class='phone' style='width:80px' id='point_" + counter + "_phone' name='point_" + counter + "_phone'>\n" +
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
			$("#vehicletypeid").val() != 0 &&
			$("#vehicleid").val() != 0) {
			
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
						$("#pointtime_" + i).val(node.departuretime.replace(/^\s+|\s+$/g, ''));
					}
				}
			},
			false
		);
}

function getJourneyTime(startTime, startDate, nextDate, elapsedTime) {
    var pointmin2 = Math.round( elapsedTime / 60 ) % 60;
    var pointhr2 = Math.floor( elapsedTime / 3600 );
	var prevhr = startTime.replace(/^\s+|\s+$/g, '').substr(0, 2);
	var prevmin = startTime.replace(/^\s+|\s+$/g, '').substr(3, 5);
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
	$(".agencyvehiclerow").hide();

	callAjax(
			"finddata.php", 
			{ 
				sql: "SELECT A.vehicletypeid, A.usualtrailerid, A.usualdriverid, A.registration " +
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
					
					if (($("#trailerid").val() == "0" || $("#trailerid option[value=" +  $("#trailerid").val() + "]"). text() == "N/A")) {
						$("#trailerid").val(node.usualtrailerid);
					}
					
					if (($("#driverid").val() == "0" || $("#driverid option[value=" +  $("#driverid").val() + "]"). text() == "N/A")) {
						$("#driverid").val(node.usualdriverid);
					}

					if (node.registration.substr(0, 1) == "Z") {
						$(".agencyvehiclerow").show();
						$(".agencyvehiclerow input").attr("required", true);

					} else {
						$(".agencyvehiclerow").hide();
						$(".agencyvehiclerow").hide();
						$(".agencyvehiclerow input").attr("required", false);
						$("#agencyvehicleregistration").val("");
					}
				}
			
			}
		);
}

function loadAddress(customerid) {
	callAjax(
			"finddata.php", 
			{ 
				sql: "SELECT A.street, A.address2, A.town, A.city, A.county, A.postcode, A.telephone " +
					 "FROM hallmark_customer A " +
					 "WHERE A.id = " + customerid
			},
			function(data) {
				if (data.length > 0) {
					var node = data[0];
					var address = node.street;

					if (node.address2 != null && node.address2 != "") address += "<br>" + node.address2;
					if (node.town != null && node.town != "") address += "<br>" + node.town;
					if (node.city != null && node.city != "") address += "<br>" + node.city;
					if (node.county != null && node.county != "") address += "<br>" + node.county;
					if (node.postcode != null && node.postcode != "") address += "<br>" + node.postcode;

					address += "<br><b>Tel:</b>" + node.telephone;

					$(".address").html(address);
				}
			},
			false
		);
}

function customerid_onchange() {
	callAjax(
			"finddata.php", 
			{ 
				sql: "SELECT A.collectionpoint, A.standardratepermile " +
					 "FROM hallmark_customer A " +
					 "WHERE A.id = " + $("#customerid").val()
			},
			function(data) {
				if (data.length > 0) {
					var node = data[0];

					$("#point_1").val(node.collectionpoint).trigger("change");
					$("#customercostpermile").val(node.standardratepermile);
					
					calculateRate2();
				}
			
			},
			false
		);
	
	loadAddress($("#customerid").val());
}

function driverid_onchange() {
	$(".drivernamerow").hide();
	$("#driverphonenumber").text("");

	callAjax(
			"finddata.php", 
			{ 
				sql: "SELECT agencydriver, usualvehicleid, usualtrailerid, fax " + 
					 "FROM hallmark_driver " +
					 "WHERE id = " + $("#driverid").val()
			},
			function(data) {
				if (data.length > 0) {
					var node = data[0];
					
					$("#agencydriver").val(node.agencydriver);
					
					if (node.fax != "") {
						$("#driverphonenumber").text(" (" + node.fax + ")");
					}
					
					if (node.usualvehicleid != null && node.usualvehicleid != 0) {
						if ($("#vehicleid option[value='" +  node.usualvehicleid + "']").length > 0) {
							$("#vehicleid").val(node.usualvehicleid).trigger("change");
						}
					}
					
					if (node.usualtrailerid != null && node.usualtrailerid != 0) {
						if ($("#trailerid option[value='" +  node.usualtrailerid + "']").length > 0) {
							$("#trailerid").val(node.usualtrailerid).trigger("change");
						}
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

function worktypeid_onchange() {
	callAjax(
			"finddata.php", 
			{ 
				sql: "SELECT nominalledgercodeid " + 
					 "FROM hallmark_worktype " +
					 "WHERE id = " + $("#worktypeid").val()
			},
			function(data) {
				if (data.length > 0) {
					var node = data[0];
					
					$("#nominalledgercodeid").val(node.nominalledgercodeid);
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

	if ($("#fixedprice").attr("checked") == false) {
		$("#charge").val(new Number(totalcost * (1 + (defaultprofitmargin / 100))).toFixed(2));
	}
}
