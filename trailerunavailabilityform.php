<?php 
	require_once("system-db.php");
?>
<table width="100%" cellpadding="0" cellspacing="4"
	class="entryformclass">
	<tbody>
		<tr>
			<td>Trailer <span class="requiredmarker">*</span></td>
			<td>
				<?php createCombo("trailerid", "id", "registration", "{$_SESSION['DB_PREFIX']}trailer", "WHERE active = 'Y'"); ?>
			</td>
		</tr>
<?php 
	if (isUserInRole("ADMIN")) {
?>
		<tr>
			<td>Supplier <span class="requiredmarker">*</span></td>
			<td>
				<?php createCombo("supplierid", "id", "name", "{$_SESSION['DB_PREFIX']}supplier"); ?>
			</td>
		</tr>
<?php 
	}
?>
		<tr>
			<td>Start Date / Time <span class="requiredmarker">*</span></td>
			<td>
				<input class="datepicker" required="true" type="text" id="startdate" name="startdate"> 
				<input class="timepicker" required="true" type="text" id="startdate_time" name="startdate_time">
			</td>
		</tr>
		<tr>
			<td>End Date / Time <span class="requiredmarker">*</span></td>
			<td>
				<input class="datepicker" required="true" type="text" id="enddate" name="enddate"> 
				<input class="timepicker" required="true" type="text" id="enddate_time" name="enddate_time">
			</td>
		</tr>
		<tr>
			<td>Status</td>
			<td>
				<select id="status" name="status">
					<option value=""></option>
					<option value="S">Scheduled</option>
					<option value="I">In Progress</option>
					<option value="A">Awaiting Order Number</option>
					<option value="C">Complete</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Order Number</td>
			<td>
				<input type="text" id="ordernumber" name="ordernumber" size=20 /> 
			</td>
		</tr>
		<tr>
			<td>Invoice Number</td>
			<td>
				<input type="text" id="invoicenumber" name="invoicenumber" size=20 /> 
			</td>
		</tr>
		<tr>
			<td>Work Carried Out</td>
			<td>
				<textarea style="width: 600px" rows="6" cols="80" id="workcarriedout" name="workcarriedout"></textarea>
			</td>
		</tr>
		<tr>
			<td>Total Cost</td>
			<td>
				<input type="number" pattern="[0-9]+([,\.][0-9]+)?" title="The number input must start with a number and use either comma or a dot as a decimal character." style="width: 72px" id="totalcost" name="totalcost"> 
			</td>
		</tr>
		<tr>
			<td>Reason <span class="requiredmarker">*</span></td>
			<td>
				<?php createCombo("reasonid", "id", "name", "{$_SESSION['DB_PREFIX']}trailerunavailabilityreasons"); ?>
			</td>
		</tr>
		<tr class="hidden defectrow">
			<td>Defect Number <span class="requiredmarker">*</span></td>
			<td>
				<input type="text" id="defectnumber" name="defectnumber" size=7 /> 
			</td>
		</tr>
	</tbody>
</table>
<script>
	$(document).ready(
			function() {
				$("#reasonid").change(
						function() {
							callAjax(
									"finddata.php",
									{
										sql: "SELECT defectnumberrequired " +
											 "FROM <?php echo $_SESSION['DB_PREFIX'];?>trailerunavailabilityreasons  " +
											 "WHERE id = " + $("#reasonid").val()
									},
									function(data) {
										$(".defectrow").hide();
										$("#defectnumber").attr("required", false);
										
										if (data.length == 1) {
											if (data[0].defectnumberrequired == "Y") {
												$(".defectrow").show();
												$("#defectnumber").attr("required", true);
												
											} else {
												$("#defectnumber").val("");
											}
										}
									},
									false
								);
						}
					);
			}
		);
</script>