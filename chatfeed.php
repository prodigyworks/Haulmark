<script>
	var lastID = "";

	function updateStatus(node) {
		var chatid = $(this).attr("chatid");
		var value = $(this).val();

		callAjax(
				"updatechatstatus.php", 
				{ 
					id: chatid,
					value: value
				},
				function(data) {
					showChat($("#displaydate").val());
				},
				false
			);
	}

	function clearChat() {
		lastID = "";
		$(".livechat .status").html("");
	}

	function showChatRelay() {
		showChat($("#displaydate").val());
		
		setTimeout(showChatRelay, 5000);
	}

	function showChat(date, message) {
		$.ajax({
				url: "livechat.php",
				dataType: 'json',
				async: true,
				data: {
					message: message,
					lastid: lastID,
					date: date
				},
				type: "POST",
				error: function(jqXHR, textStatus, errorThrown) {
				},
				success: function(data) {
					var html = "";

					for (var i = 0; i < data.length;  i++) {
						var node = data[i];

						if (i == 0) {
							lastID = node.timestamp;
						}

						var status = "New";
						var clazz = "";

						status = "<SELECT chatid='" + node.id + "' class='chatstatus'>" + 
								 "<OPTION value='N' selected>New</OPTION>" +
								 "<OPTION value='C'>Complete</OPTION>" +
								 "<OPTION value='X'>Cancelled</OPTION>" +
								 "</SELECT>";

						if (node.status == "C") {
							clazz = "complete";
							status = "<SELECT chatid='" + node.id + "' class='chatstatus'>" + 
									 "<OPTION value='N'>New</OPTION>" +
									 "<OPTION value='C' selected>Complete</OPTION>" +
									 "<OPTION value='X'>Cancelled</OPTION>" +
									 "</SELECT>";

						}  else if (node.status == "X") {
							status = "<SELECT chatid='" + node.id + "' class='chatstatus'>" + 
									 "<OPTION value='N'>New</OPTION>" +
									 "<OPTION value='C'>Complete</OPTION>" +
									 "<OPTION value='X' selected>Cancelled</OPTION>" +
									 "</SELECT>";
							clazz = "cancelled";
						}
						
						html  = "<div class='" +  clazz + "'>\n";
						html += "<div class='name'>" + node.name + " : ";
						html += "<span class='date'>" + node.date + "</span>\n";

						if (node.completeddatetime != null) {
							html += "<span class='date'> Completed : " + node.completeddatetime + "</span>\n";
						}

						html += "<span class='status'>" + status + "</span></div>\n";
						html += "<div class='text'>" + node.text + "</div><hr>\n";
						html += "</div>\n";

						if ($("#chat_" + node.id).length) {
							if (status != $("#chat_" + node.id + " .status").text()) {
								if (node.status == "C") {
									$("#chat_" + node.id).appendTo(".livechat #completedstatus");
	
								} else if (node.status == "X") {
									$("#chat_" + node.id).appendTo(".livechat #cancelledstatus");
	
								} else {
									$("#chat_" + node.id).appendTo(".livechat #newstatus");
								}

							} else {
								alert("NIT CHANGED");
							}
							
							$("#chat_" + node.id).html(html);
							
						} else {
							if (node.status == "C") {
								$(".livechat #completedstatus").prepend("<div id='chat_" + node.id + "'>\n" + html + "</div>");

							} else if (node.status == "X") {
								$(".livechat #cancelledstatus").prepend("<div id='chat_" + node.id + "'>\n" + html + "</div>");

							} else {
								$(".livechat #newstatus").prepend("<div id='chat_" + node.id + "'>\n" + html + "</div>");
							}
						}

						$("#chat_" + node.id + " .chatstatus").change(updateStatus);
					}

				}
			});
	}
	
	if (eval("typeof addEventCallback") == "undefined") {
		addEventCallback = function() { };
	}
</script>
