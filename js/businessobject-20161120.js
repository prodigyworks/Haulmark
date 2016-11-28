function businessobjecttojson(classname, methodname, args) {
	returndata = null;
	
	$.ajax({
		url: "ui/businessobjecttojson.php",
		dataType: 'json',
		async: false,
		data: {
			args: args,
			classname: classname,
			methodname: methodname
		},
		type: "POST",
		error: function(jqXHR, textStatus, errorThrown) {
			if (error) {
				error(jqXHR, textStatus, errorThrown);
			} else {
//				alert("ERROR :" + errorThrown);
//				$("#footer").html(errorThrown);
			}
		},
		success: function(data) {
			returndata = data;
		}
	});
	
	return returndata;
}