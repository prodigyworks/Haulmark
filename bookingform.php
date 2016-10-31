<style>
    .bookingnumber_div {
    	position: absolute;
    	font-size:17px;
    	top: 10px;
    	width:400px;
    	height:20px;
    	left: 520px;
    	text-align:right;
    }
    .address {
		box-shadow: 5px 5px 5px #888888;
    	padding:5px;
    	text-align: left;
    	border:1px solid grey;
    	border-radius: 13px;
    	font-size:12px;
    	width:400px;
    	height:120px;
    	background-color: white;
    }
</style>
<div class="bookingnumber_div">
</div>

<table width="100%" cellpadding="0" cellspacing="4" class="entryformclass">
	<tbody>
		<tr valign="center">
			<td>Customer</td>
			<td>
				<?php createCombo("customerid", "id", "name", "{$_SESSION['DB_PREFIX']}customer", "", true); ?>
			</td>
			<td rowspan=8 align='right'>
				<div class="address">
					<div>
					</div>
				</div>
			</td>
		</tr>
		<tr valign="center">
			<td>Status</td>
			<td>
				<?php createCombo("statusid", "id", "name", "{$_SESSION['DB_PREFIX']}bookingstatus", "WHERE id != 8", true, false, array(), true, "sequence"); ?>
			</td>
		</tr>
		<tr valign="center">
			<td>Logged By</td>
			<td>
				<?php createUserCombo("memberid"); ?>
			</td>
		</tr>
		<tr valign="center">
			<td>Vehicle Type</td>
			<td>
				<?php createCombo("vehicletypeid", "id", "name", "{$_SESSION['DB_PREFIX']}vehicletype", "", true, false, array(), true, "code"); ?>
			</td>
		</tr>
		<tr valign="center">
			<td>Vehicle</td>
			<td>
				<?php createCombo("vehicleid", "id", "registration", "{$_SESSION['DB_PREFIX']}vehicle", "WHERE active = 'Y'",  false); ?>
			</td>
		</tr>
		<tr class="agencyvehiclerow">
			<td>Registration</td>
			<td>
				<input type="text" style="width:120px" id="agencyvehicleregistration" name="agencyvehicleregistration"><div class="bubble" title="Required field"></div>
			</td>
		</tr>
		<tr valign="center">
			<td>Driver / Agency</td>
			<td>
				<?php createCombo("driverid", "id", "name", "{$_SESSION['DB_PREFIX']}driver", "", false, false, array(), true, "agencydriver, name"); ?>
				<input type="hidden" id="agencydriver" name="agencydriver" />
				<input type="hidden" id="bookingid" name="bookingid" />
				<input type="hidden" id="originalstatusid" name="originalstatusid" />
				<input type="hidden" id="nominalledgercodeid" name="nominalledgercodeid" />
				<label id="driverphonenumber"></label>
			</td>
		</tr>
		<tr valign="center">
			<td>Trailer</td>
			<td>
				<?php createCombo("trailerid", "id", "registration", "{$_SESSION['DB_PREFIX']}trailer", "", false); ?>
			</td>
		</tr>
		<tr class="drivernamerow">
			<td>Driver Name</td>
			<td>
				<input type="text" style="width:220px" id="drivername" name="drivername"><div class="bubble" title="Required field"></div>
			</td>
		</tr>
		<tr class="drivernamerow">
			<td>Driver Phone</td>
			<td>
				<input type="tel" style="width:220px" id="driverphone" name="driverphone"><div class="bubble" title="Required field"></div>
			</td>
		</tr>
		<tr valign="center">
			<td>Work Type</td>
			<td>
				<?php createCombo("worktypeid", "id", "name", "{$_SESSION['DB_PREFIX']}worktype"); ?>
			</td>
		</tr>
		<tr valign="center">
			<td>Order Number</td>
			<td colspan=2>
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
			<td colspan=2>
				<table style="table-layout:fixed" width='700px'>
					<tr>
						<td style="width:310px"><b>Destination</b></td>
						<td style="width:80px"><b>Arrival</b></td>
						<td style="width:50px"><b>Time</b></td>
						<td style="width:80px"><b>Depart</b></td>
						<td style="width:50px"><b>Time</b></td>
						<td style="width:200px"><b>Booking Ref</b></td>
						<td style="width:100px"><b>Phone</b></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr valign="center">
			<td>Collection / Delivery</td>
			<td colspan=2>
				<div id="tolocationdiv" class="bookingjourneys">
					<div>
						<input required="true" type="text" style="width:300px" id="fromplace" name="fromplace" placeholder="Enter a location" onchange="calculateTimeNode(this, 1)"   autocomplete="off">&nbsp;
						<div style='display:inline-block; min-width:131px'>&nbsp;</div>
						<input class="datepicker bookingdateclass" required="true" type="text" index='0' id="startdatetime"  onchange="calculateTimeNode(this, 1)" name="startdatetime" ><div class="bubble" title="Required field"></div>
						<input class="timepicker bookingtimeclass" required="true" type="text" index='0' id="startdatetime_time" onchange="calculateTimeNode(this, 1)"   name="startdatetime_time"><div class="bubble" title="Required field"></div>
						<input type="text" style="width:200px" id="fromplace_ref" name="fromplace_ref">
						<input type="tel" style="width:80px" id="fromplace_phone" name="fromplace_phone">
						&nbsp;<img src="images/add.png" class='pointimage' onclick="addPointBetweenNodes()"></img>
					</div>
				</div>
			</td>
		</tr>
		<tr valign="center">
			<td>Return To</td>
			<td colspan=2>
				<div class="bookingjourneys">
					<input required="true" type="text" style="width:300px" id="toplace" name="toplace" placeholder="Enter a location" onchange="calculateTimeNode(this, 1)"  autocomplete="off">&nbsp;
					<input class="datepicker bookingdateclass" required="true" type="text" id="enddatetime" name="enddatetime" onchange="calculateTimeNode(this, 1)"  ><div class="bubble" title="Required field"></div>
					<input class="timepicker bookingtimeclass" required="true" type="text" id="enddatetime_time" name="enddatetime_time"><div class="bubble" title="Required field"></div>
					<div style='display:inline-block; min-width:131px'>&nbsp;</div>
					<input type="text" style="width:200px" id="toplace_ref" name="toplace_ref">
					<input type="tel" style="width:80px" id="toplace_phone" name="toplace_phone">
				</div>
				
			</td>
		</tr>
		<tr valign="center">
			<td>Distance (Miles)</td>
			<td colspan=2>
				<input required="true" type="text" style="width:72px" id="miles" name="miles"><div class="bubble" title="Required field"></div>
			</td>
		</tr>
		<tr valign="center">
			<td>Duration</td>
			<td>
				<input required="true" type="number" style="width:72px" id="duration" name="duration"><div class="bubble" title="Required field"></div>
			</td colspan=2>
		</tr>
		<tr valign="center">
			<td>Pallets</td>
			<td>
				<input type="number" style="width:72px" id="pallets" name="pallets"><div class="bubble" title="Required field"></div>
			</td colspan=2>
		</tr>
		<tr valign="center">
			<td>Total Weight</td>
			<td colspan=2>
				<input required="true" type="text" style="width:72px" id="weight" name="weight"> (Kg)
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
			<td colspan=2>
				<input required="true" type="text" style="width:72px" id="rate" name="rate"><div class="bubble" title="Required field"></div>
			</td>
		</tr>
		<tr valign="center">
			<td>Charge</td>
			<td colspan=2>
				<table>
					<tr>
						<td>
							<input required="true" type="text" style="width:72px" id="charge" name="charge" /><div class="bubble" title="Required field"></div>
						</td>
						<td width="200px">
							<input type="checkbox" id="fixedprice" name="fixedprice" >&nbsp;Fixed Price</input>
						</td>
						<td>
							<span class="bookingbutton" id="btnPrevPrices">Check Prices</span>&nbsp;
						</td>
						<td>
							<a href="javascript: showRateCard()" target="rateCardIframe" class="bookingbutton" id="btnRateCard">Rate Card</span>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr valign="center">
			<td>Notes</td>
			<td colspan=2>
				<textarea class="tinyMCE" id="notes" name="notes"></textarea>
			</td>
		</tr>
	</tbody>
</table>
