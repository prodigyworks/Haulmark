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

function addPoint() {
	var html = "<div id='container_" + counter + "' class='pointcontainer' index='" + counter + "' style='padding-top:3px'>\n" +
	   		   "	<input class='point' id='point_" + counter  + "' index='" + counter + "' required='true' type='text' style='width:300px' name='point_" + counter + "'>&nbsp;\n" +
			   "	<div class='bubble' title='Required field'></div>\n" +
			   "	<input class='datepicker' required='true' index='" + counter + "' type='text' id='pointdate_" + counter +  "' name='pointdate_" + counter + "'>\n" +
			   "	<div class='bubble' title='Required field'></div>\n" +
			   "	<input class='timepicker' required='true' index='" + counter + "' type='text' id='pointtime_" + counter + "' name='pointtime_" + counter + "'>\n" +
			   "	<div class='bubble' title='Required field'></div>\n" +
	   		   "	<input id='point_" + counter  + "_lng' type='hidden' name='point_" + counter + "_lng'>\n" +
	   		   "	<input id='point_" + counter  + "_lat' type='hidden' name='point_" + counter + "_lat'>\n" +
	   		   "    <input type='text' style='width:200px' id='point_" + counter + "_ref' name='point_" + counter + "_ref'>\n" +
			   "	<input type='text' style='width:80px' id='point_" + counter + "_phone' name='point_" + counter + "_phone'>\n" +
			   "	<img src='images/minus.gif' onclick='removePoint(this)'></img>" +
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

