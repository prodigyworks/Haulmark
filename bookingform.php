<style>
.entryform .bubble {
	display: none;
}
</style>
<table width="100%" cellpadding="0" cellspacing="4" class="entryformclass">
	<tbody>
		<tr valign="center">
			<td>Customer</td>
			<td>
				<?php createCombo("customerid", "id", "name", "{$_SESSION['DB_PREFIX']}customer", "", true); ?>
			</td>
		</tr>
		<tr valign="center">
			<td>Status</td>
			<td>
				<?php createCombo("statusid", "id", "name", "{$_SESSION['DB_PREFIX']}bookingstatus"); ?>
			</td>
		</tr>
		<tr valign="center">
			<td>Logged By</td>
			<td>
				<?php createUserCombo("memberid"); ?>
			</td>
		</tr>
		<tr valign="center">
			<td>Driver / Agency</td>
			<td>
				<?php createCombo("driverid", "id", "name", "{$_SESSION['DB_PREFIX']}driver", "", false); ?>
				<input type="hidden" id="agencydriver" name="agencydriver">
			</td>
		</tr>
		<tr valign="center">
			<td>Vehicle</td>
			<td>
				<?php createCombo("vehicleid", "id", "registration", "{$_SESSION['DB_PREFIX']}vehicle", "", false); ?>
			</td>
		</tr>
		<tr valign="center">
			<td>Vehicle Type</td>
			<td>
				<?php createCombo("vehicletypeid", "id", "name", "{$_SESSION['DB_PREFIX']}vehicletype", "", false); ?>
			</td>
		</tr>
		<tr valign="center">
			<td>Trailer</td>
			<td>
				<?php createCombo("trailerid", "id", "registration", "{$_SESSION['DB_PREFIX']}trailer", "", false); ?>
			</td>
		</tr>
		<tr id="drivernamerow">
			<td>Driver Name</td>
			<td>
				<input type="text" style="width:220px" id="drivername" name="drivername"><div class="bubble" title="Required field"></div>
			</td>
		</tr>
		<tr id="driverstorerow">
			<td>Store</td>
			<td>
				<input type="text" style="width:220px" id="storename" name="storename"><div class="bubble" title="Required field"></div>
			</td>
		</tr>
		<tr valign="center">
			<td>Work Type</td>
			<td>
				<?php createCombo("worktypeid", "id", "name", "{$_SESSION['DB_PREFIX']}worktype"); ?>
			</td>
		</tr>
		<tr valign="center">
			<td>Load Type</td>
			<td>
				<?php createCombo("loadtypeid", "id", "name", "{$_SESSION['DB_PREFIX']}loadtype", "", true); ?>
			</td>
		</tr>
		<tr valign="center">
			<td>Order Number</td>
			<td>
				<input required="true" type="text" style="width:120px" id="ordernumber" name="ordernumber"><div class="bubble" title="Required field"></div>
			</td>
		</tr>
		<tr valign="center">
			<td>Order Number 2</td>
			<td>
				<input type="text" style="width:120px" id="ordernumber2" name="ordernumber2"><div class="bubble" title="Required field"></div>
			</td>
		</tr>
		<tr valign="center">
			<td>
				&nbsp;
			</td>
			<td>
				<table style="table-layout:fixed" width='700px'>
					<tr>
						<td style="width:310px"><b>Destination</b></td>
						<td style="width:80px"><b>Date</b></td>
						<td style="width:50px"><b>Time</b></td>
						<td style="width:200px"><b>Ref</b></td>
						<td style="width:100px"><b>Phone</b></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr valign="center">
			<td>Collection Point</td>
			<td>
				<div id="tolocationdiv">
					<div>
						<input required="true" type="text" style="width:300px" id="fromplace" name="fromplace" placeholder="Enter a location" onchange="calculateTimeNode(this, 1)"   autocomplete="off">&nbsp;
						<input class="datepicker" required="true" type="text" index='0' id="startdatetime"  onchange="calculateTimeNode(this, 1)" name="startdatetime" ><div class="bubble" title="Required field"></div>
						<input class="timepicker" required="true" type="text" index='0' id="startdatetime_time" onchange="calculateTimeNode(this, 1)"   name="startdatetime_time"><div class="bubble" title="Required field"></div>
						<input type="text" style="width:200px" id="fromplace_ref" name="fromplace_ref">
						<input type="text" style="width:80px" id="fromplace_phone" name="fromplace_phone">
						&nbsp;<img src="images/add.png" onclick="addPointBetweenNodes()"></img>
					</div>
				</div>
			</td>
		</tr>
		<tr valign="center">
			<td>Return To</td>
			<td>
				<input required="true" type="text" style="width:300px" id="toplace" name="toplace" placeholder="Enter a location" onchange="calculateTimeNode(this, 1)"  autocomplete="off">&nbsp;
				<input class="datepicker" required="true" type="text" id="enddatetime" name="enddatetime" onchange="calculateTimeNode(this, 1)"  ><div class="bubble" title="Required field"></div>
				<input class="timepicker" required="true" type="text" id="enddatetime_time" name="enddatetime_time"><div class="bubble" title="Required field"></div>
				<input type="text" style="width:200px" id="toplace_ref" name="toplace_ref">
				<input type="text" style="width:80px" id="toplace_phone" name="toplace_phone">
				
			</td>
		</tr>
		<tr valign="center">
			<td>Distance (Miles)</td>
			<td>
				<input required="true" type="text" style="width:72px" id="miles" name="miles"><div class="bubble" title="Required field"></div>
			</td>
		</tr>
		<tr valign="center">
			<td>Duration</td>
			<td>
				<input required="true" type="text" style="width:72px" id="duration" name="duration"><div class="bubble" title="Required field"></div>
			</td>
		</tr>
		<tr valign="center">
			<td>Weight</td>
			<td>
				<input required="true" type="text" style="width:72px" id="weight" name="weight">
				<input type="hidden" id="vehiclecostoverhead" name="vehiclecostoverhead">
				<input type="hidden" id="allegrodayrate" name="allegrodayrate">
				<input type="hidden" id="agencydayrate" name="agencydayrate">
				<input type="hidden" id="wages" name="wages">
				<input type="hidden" id="fuelcostoverhead" name="fuelcostoverhead">
				<input type="hidden" id="maintenanceoverhead" name="maintenanceoverhead">
				<input type="hidden" id="profitmargin" name="profitmargin">
				<input type="hidden" id="customercostpermile" name="customercostpermile">
				<input type="hidden" id="fromplace_lat" name="fromplace_lat">
				<input type="hidden" id="fromplace_lng" name="fromplace_lng">
				<input type="hidden" id="toplace_lat" name="toplace_lat">
				<input type="hidden" id="toplace_lng" name="toplace_lng">
				<input type="hidden" id="bookingpoints" name="bookingpoints">
			</td>
		</tr>
		<tr valign="center">
			<td>Rate</td>
			<td>
				<input required="true" type="text" style="width:72px" id="rate" name="rate"><div class="bubble" title="Required field"></div>
			</td>
		</tr>
		<tr valign="center">
			<td>Charge</td>
			<td>
				<input required="true" type="text" style="width:72px" id="charge" name="charge"><div class="bubble" title="Required field"></div>
				<button>Check previous prices</button>
			</td>
		</tr>
		<tr valign="center">
			<td>Notes</td>
			<td>
				<textarea class="tinyMCE" id="notes" name="notes"></textarea>
			</td>
		</tr>
	</tbody>
</table>
