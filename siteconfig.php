<?php 
	require_once("system-header.php"); 
	require_once("tinymce.php"); 
?>

<!--  Start of content -->
<?php
	if (isset($_POST['domainurl'])) {
		$runscheduledays = mysql_escape_string($_POST['runscheduledays']);
		$domainurl = mysql_escape_string($_POST['domainurl']) ;
		$emailfooter = mysql_escape_string($_POST['emailfooter']);
		$address = mysql_escape_string($_POST['address']);
		$maintelephone = mysql_escape_string($_POST['maintelephone']);
		$defaultprofitmargin = $_POST['defaultprofitmargin'];
		$defaultwagesmargin = $_POST['defaultwagesmargin'];
		$bookingprefix = $_POST['bookingprefix'];
		$trafficofficetelephone1 = mysql_escape_string($_POST['trafficofficetelephone1']);
		$trafficofficetelephone2 = mysql_escape_string($_POST['trafficofficetelephone2']);
		$fax = mysql_escape_string($_POST['fax']);
		$accountsemail = mysql_escape_string($_POST['accountsemail']);
		$trafficemail = mysql_escape_string($_POST['trafficemail']);
		$website = mysql_escape_string($_POST['website']);
		$vatregnumber = mysql_escape_string($_POST['vatregnumber']);
		$timezoneoffset = mysql_escape_string($_POST['timezoneoffset']);
		$vatprefix = mysql_escape_string($_POST['vatprefix']);
		$ssl = mysql_escape_string($_POST['ssl']);
		$companynumber = mysql_escape_string($_POST['companynumber']);
		$currentrhaterms = mysql_escape_string($_POST['currentrhaterms']);
		$deliveryconfirmationmessage = mysql_escape_string($_POST['deliveryconfirmationmessage']);
		$termsandconditions = mysql_escape_string($_POST['termsandconditions']);
		$webbookingconfirmation = mysql_escape_string($_POST['webbookingconfirmation']);
		$financialyearend = convertStringToDate($_POST['financialyearend']);
		$rhamembershipnumber = mysql_escape_string($_POST['rhamembershipnumber']);
		$payereference = mysql_escape_string($_POST['payereference']);
		$bank = mysql_escape_string($_POST['bank']);
		$bankaccountnumber = mysql_escape_string($_POST['bankaccountnumber']);
		$banksortcode = mysql_escape_string($_POST['banksortcode']);
		$defaultworktype = mysql_escape_string($_POST['defaultworktype']);
		$averagewaittime = mysql_escape_string($_POST['averagewaittime']);
		$vatrate = $_POST['vatrate'];
		$basepostcode = $_POST['basepostcode'];
		
		$memberid = getLoggedOnMemberID();
		$qry = "UPDATE {$_SESSION['DB_PREFIX']}siteconfig SET 
				domainurl = '$domainurl', 
				vatrate = $vatrate, 
				address = '$address', 
				maintelephone = '$maintelephone', 
				defaultprofitmargin = '$defaultprofitmargin', 
				defaultwagesmargin = '$defaultwagesmargin', 
				bookingprefix = '$bookingprefix', 
				trafficofficetelephone1 = '$trafficofficetelephone1', 
				trafficofficetelephone2 = '$trafficofficetelephone2', 
				fax = '$fax', 
				accountsemail = '$accountsemail', 
				trafficemail = '$trafficemail', 
				website = '$website', 
				vatregnumber = '$vatregnumber', 
				timezoneoffset = '$timezoneoffset',
				vatprefix = '$vatprefix', 
				sslencryption = '$ssl',
				companynumber = '$companynumber', 
				currentrhaterms = '$currentrhaterms', 
				termsandconditions = '$termsandconditions', 
				deliveryconfirmationmessage = '$deliveryconfirmationmessage',
				webbookingconfirmation = '$webbookingconfirmation', 
				financialyearend = '$financialyearend', 
				rhamembershipnumber = '$rhamembershipnumber', 
				payereference = '$payereference', 
				bank = '$bank', 
				bankaccountnumber = '$bankaccountnumber', 
				banksortcode = '$banksortcode', 
				defaultworktype = '$defaultworktype',  
			 	averagewaittime = '$averagewaittime', 
				basepostcode = '$basepostcode', 
				runscheduledays = '$runscheduledays', 
				emailfooter = '$emailfooter', 
				metamodifieddate = NOW(), 
				metamodifieduserid = $memberid" ;
		$result = mysql_query($qry);
		
	   	if (! $result) {
	   		logError("UPDATE {$_SESSION['DB_PREFIX']}siteconfig:" . $qry . " - " . mysql_error());
	   	}
	   	
	   	unset($_SESSION['SITE_CONFIG']);
	}
	
	$qry = "SELECT *, 
			DATE_FORMAT(lastschedulerun, '%d/%m/%Y') AS lastschedulerun, 
			DATE_FORMAT(financialyearend, '%d/%m/%Y') AS financialyearend 
			FROM {$_SESSION['DB_PREFIX']}siteconfig";
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
?>
<form id="contentForm" name="contentForm" method="post" class="entryform">
	<h4><?php echo $_SESSION['title']; ?></h4>
	<label>Domain URL</label>
	<input required="true" type="url" class="textbox90" id="domainurl" name="domainurl" value="<?php echo $member['domainurl']; ?>" />

	<label>VAT Rate</label>
	<input required="true" type="number" id="vatrate" name="vatrate" value="<?php echo number_format($member['vatrate'], 2); ?>" />

	<label>Run Alert Schedule Cycle (Days)</label>
	<input required="true" type="number" class="textbox20" id="runscheduledays" name="runscheduledays" value="<?php echo $member['runscheduledays']; ?>" />

	<label>Last Schedule Date Run</label>
	<input readonly type="text" class="textbox20" id="lastschedulerun" name="lastschedulerun" value="<?php echo $member['lastschedulerun']; ?>" />
	
	<label>E-mail Footer</label>
	<textarea id="emailfooter" name="emailfooter" rows="15" cols="60" style="height:340px;width: 340px" class="tinyMCE"></textarea>

	<label>Address</label>
	<textarea id="address" name="address" rows="5" cols="60"></textarea>

	<label>Main Telephone</label>
	<input type="text" class="textbox20" id="maintelephone" name="maintelephone" value="<?php echo $member['maintelephone']; ?>" />

	<label>Traffic Office Telephone 1</label>
	<input type="tel" class="textbox20" id="trafficofficetelephone1" name="trafficofficetelephone1" value="<?php echo $member['trafficofficetelephone1']; ?>" />

	<label>Traffic Office Telephone 2</label>
	<input type="tel" class="textbox20" id="trafficofficetelephone2" name="trafficofficetelephone2" value="<?php echo $member['trafficofficetelephone2']; ?>" />

	<label>Fax</label>
	<input type="tel" class="textbox20" id="fax" name="fax" value="<?php echo $member['fax']; ?>" />

	<label>Accounts Email</label>
	<input type="email" class="textbox90" id="accountsemail" name="accountsemail" value="<?php echo $member['accountsemail']; ?>" />

	<label>Traffic email</label>
	<input type="text" class="textbox90" id="trafficemail" name="trafficemail" value="<?php echo $member['trafficemail']; ?>" />

	<label>Web Site</label>
	<input type="url" class="textbox90" id="website" name="website" value="<?php echo $member['website']; ?>" />

	<label>Timezone Offset</label>
	<input type="text" class="textbox20" id="timezoneoffset" name="timezoneoffset" value="<?php echo $member['timezoneoffset']; ?>" />

	<label>VAT Reg Number</label>
	<input type="text" class="textbox20" id="vatregnumber" name="vatregnumber" value="<?php echo $member['vatregnumber']; ?>" />

	<label>VAT Prefix</label>
	<input type="text" cols=2 id="vatprefix" name="vatprefix" value="<?php echo $member['vatprefix']; ?>" />

	<label>SSL</label>
	<SELECT id='ssl' name='ssl'>
		<OPTION value='N'>No</OPTION>
		<OPTION value='Y'>Yes</OPTION>
	</SELECT>

	<label>Company Number</label>
	<input type="number" class="textbox20" id="companynumber" name="companynumber" value="<?php echo $member['companynumber']; ?>" />

	<label>Current RHA Terms</label>
	<textarea id="currentrhaterms" name="currentrhaterms" rows="15" cols="60" style="height:340px;width: 340px" class="tinyMCE"></textarea>

	<label>Terms And Conditions</label>
	<textarea id="termsandconditions" name="termsandconditions" rows="15" cols="60" style="height:340px;width: 340px" class="tinyMCE"></textarea>

	<label>Web Booking Confirmation</label>
	<textarea id="webbookingconfirmation" name="webbookingconfirmation" rows="15" cols="60" style="height:340px;width: 340px" class="tinyMCE"></textarea>

	<label>Booking Confirmation Message</label>
	<textarea id="deliveryconfirmationmessage" name="deliveryconfirmationmessage" rows="15" cols="60" style="height:340px;width: 340px" class="tinyMCE"></textarea>

	<label>Financial Year</label>
	<input type="text" class="datepicker" id="financialyearend" name="financialyearend" value="<?php echo $member['financialyearend']; ?>" />

	<label>RHA Membership Number</label>
	<input type="text" class="textbox20" id="rhamembershipnumber" name="rhamembershipnumber" value="<?php echo $member['rhamembershipnumber']; ?>" />

	<label>PAYE Reference</label>
	<input type="text" class="textbox20" id="payereference" name="payereference" value="<?php echo $member['payereference']; ?>" />

	<label>Bank</label>
	<input type="text" class="textbox90" id="bank" name="bank" value="<?php echo $member['bank']; ?>" />

	<label>Bank Account Number</label>
	<input type="number" class="textbox20" id="bankaccountnumber" name="bankaccountnumber" value="<?php echo $member['bankaccountnumber']; ?>" />

	<label>Bank Sort Code</label>
	<input type="text" class="textbox20" id="banksortcode" name="banksortcode" value="<?php echo $member['banksortcode']; ?>" />

	<label>Base Post Code</label>
	<input type="text" class="textbox20" id="basepostcode" name="basepostcode" value="<?php echo $member['basepostcode']; ?>" />

	<label>Defaut Profit Margin</label>
	<input type="number" class="textbox20" id="defaultprofitmargin" name="defaultprofitmargin" value="<?php echo $member['defaultprofitmargin']; ?>" />

	<label>Defaut Wages Margin</label>
	<input type="number" class="textbox20" id="defaultwagesmargin" name="defaultwagesmargin" value="<?php echo $member['defaultwagesmargin']; ?>" />

	<label>Booking Prefix</label>
	<input type="text" id="bookingprefix" name="bookingprefix" value="<?php echo $member['bookingprefix']; ?>" />

	<label>Average Waiting Time (Minutes)</label>
	<input type="number" class="textbox20" id="averagewaittime" name="averagewaittime" value="<?php echo $member['averagewaittime']; ?>" />

	<label>Default Work Type</label>
	<?php createCombo("defaultworktype", "id", "name", "{$_SESSION['DB_PREFIX']}worktype"); ?>
	
	<br>
	<br>
	<span class="wrapper"><a class='link2' href="javascript:if (verifyStandardForm('#contentForm')) $('#contentForm').submit();"><em><b>Update</b></em></a></span>
</form>
<script type="text/javascript">
	$(document).ready(function() {
			$("#emailfooter").val("<?php echo escape_notes($member['emailfooter']); ?>");
			$("#address").val("<?php echo escape_notes($member['address']); ?>");
			$("#currentrhaterms").val("<?php echo escape_notes($member['currentrhaterms']); ?>");
			$("#termsandconditions").val("<?php echo escape_notes($member['termsandconditions']); ?>");
			$("#webbookingconfirmation").val("<?php echo escape_notes($member['webbookingconfirmation']); ?>");
			$("#deliveryconfirmationmessage").val("<?php echo escape_notes($member['deliveryconfirmationmessage']); ?>");
			$("#defaultworktype").val("<?php echo escape_notes($member['defaultworktype']); ?>");
			$("#ssl").val("<?php echo escape_notes($member['sslencryption']); ?>");
		});
</script>
	<?php
			}
		}
	?>
<!--  End of content -->

<?php include("system-footer.php"); ?>
