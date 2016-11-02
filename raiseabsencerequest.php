<?php
	require_once("system-header.php");
	require_once("tinymce.php");
?>

<!--  Start of content -->
	<form id="manualeditform" action="addabsence.php" method="POST">
		<table width='100%' style='table-layout:fixed' cellspacing=5>
			<tr>
				<td width='200px'>Staff Member</td>
				<td>
					<?php 
						if (isUserInRole("ADMIN")) {
							createCombo("memberid", "member_id", "fullname", "{$_SESSION['DB_PREFIX']}members", "WHERE member_id IN (SELECT memberid FROM {$_SESSION['DB_PREFIX']}userroles WHERE roleid IN ('ALLEGRO', 'DRIVER'))"); 
							
						} else {
							createCombo("memberid", "member_id", "fullname", "{$_SESSION['DB_PREFIX']}members", "WHERE member_id = " . getLoggedOnMemberID(), true, false, array(), false); 
						}
					?>
				</td>
			</tr>
			<tr>
				<td width='200px'>Requested Date</td>
				<td><input class="datepicker" type="text" id="requesteddate" name="requesteddate" /></td>
			</tr>
			<tr>
				<td width='200px'>First Day Of Absence</td>
				<td>
					<input class="datepicker" type="text" id="startdate" name="startdate" onchange="calculateDuration()" />
					<input type="checkbox" id="startdate_half" name="startdate_half"  onclick="calculateDuration()" checked>&nbsp;Full day</input>
				</td>
			</tr>
			<tr>
				<td width='200px'>Last Day Of Absence</td>
				<td>
					<input class="datepicker" type="text" id="enddate" name="enddate"  onchange="calculateDuration()" />
					<input type="checkbox" id="enddate_half" name="enddate_half"  onclick="calculateDuration()" checked>&nbsp;Full day</input>
				</td>
			</tr>
			<tr>
				<td width='200px'>Duration</td>
				<td>
					<input type="text" readonly id="daystaken" name="daystaken" size=4 />
				</td>
			</tr>
			<tr>
				<td width='200px'>Absence Type</td>
				<td>
					<SELECT id='absencetype' name='absencetype'>
						<OPTION value='Unauthorised'>Unauthorised</OPTION>
						<OPTION value='Authorised'>Authorised</OPTION>
						<OPTION value='Sick'>Sick</OPTION>
						<OPTION value='Family Matter'>Family Matter</OPTION>
						<OPTION value='Not In'>Not In</OPTION>
						<OPTION value='Leaver'>Leaver</OPTION>
					</SELECT>
				</td>
			</tr>
			<tr>
				<td width='200px'>Reason</td>
				<td><textarea class="tinyMCE" style='width:700px; height: 200px;' id="absentreason" name="absentreason"></textarea></td>
			</tr>
			<tr>
				<td width='200px'>&nbsp;</td>
				<td><a class='link1' href='javascript: $("#manualeditform").submit();'><em><b>Confirm</b></em></a></td>
			</tr>
		</table>
	</form>
	<script>
		$(document).ready(
				function() {
					$("#memberid").val("<?php echo getLoggedOnMemberID(); ?>").trigger("change");
					
					var myDate = new Date(); 
					var prettyDate =
							padZero(myDate.getDate()) + '/' +         
						    padZero((myDate.getMonth() + 1)) + '/' + 
							myDate.getFullYear(); 
							 
					$("#requesteddate").val(prettyDate);
					$("#startdate").val(prettyDate);
					$("#enddate").val(prettyDate);
					
					calculateDuration();
				}
			);
			
		function calculateDuration() {
			var startDateStr = $("#startdate").val();
			var endDateStr = $("#enddate").val();
			
			var startDate = new Date(startDateStr.substring(6, 10), (parseFloat(startDateStr.substring(3, 5)) - 1), startDateStr.substring(0, 2));
			var endDate = new Date(endDateStr.substring(6, 10), (parseFloat(endDateStr.substring(3, 5)) - 1), endDateStr.substring(0, 2));
			var days = workingDaysBetweenDates(startDate, endDate);
			
			callAjax(
					"findbankholidays.php", 
					{ 
						startdate: startDateStr,
						enddate: endDateStr
					},
					function(data) {
						if (data.length > 0) {
							for (var i = 0; i < data.length; i++) {
								var node = data[i];
								
								var bankStartDate = new Date(node.startdate.substring(6, 10), (parseFloat(node.startdate.substring(3, 5)) - 1), node.startdate.substring(0, 2));
								var bankEndDate = new Date(node.enddate.substring(6, 10), (parseFloat(node.enddate.substring(3, 5)) - 1), node.enddate.substring(0, 2));
								var xdays = workingDaysBetweenDates(bankStartDate, bankEndDate);
								
								days -= xdays;
							}
						}
					},
					false
				);
			
			if (days > 0) {
				if ($("#startdate_half").attr("checked") == false) {
					if (startDate.getDay() > 0 && startDate.getDay() < 6) {
						days -= 0.5;
					}
				}
				
				if ($("#enddate_half").attr("checked") == false) {
					if (endDate.getDay() > 0 && endDate.getDay() < 6) {
						days -= 0.5;
					}
				}
			}
			
			$("#daystaken").val(days);
		}
	</script>
<!--  End of content -->
<?php 
	require_once("system-footer.php"); 
?>
		