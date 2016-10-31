<?php 
	require_once("system-header.php"); 
?>
<form method="POST" id="searchform" class="reportform" action="report-maintenancelib.php" target="_new">
	<h4><?php echo $_SESSION['title']; ?></h4>
	<table>
		<tr>
			<td>From Date</td>
			<td>
				<input type="text" id="fromdate" name="fromdate" class="datepicker" required="true" />
			</td>
		</tr>
		<tr>
			<td>To Date</td>
			<td>
				<input type="text" id="todate" name="todate" class="datepicker" required="true" />
			</td>
		</tr>
		<tr valign='top'>
			<td>
				<table>
					<tr>
						<td><b>Include Reasons (Vehicle)</b></td>
					</tr>
<?php 
		$sql = "SELECT id, name
				FROM {$_SESSION['DB_PREFIX']}vehicleunavailabilityreasons
				ORDER BY name";
		$result = mysql_query($sql);
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
?>
		<tr>
			<td>
			<?php 
				echo $member['name']; 
			?>
			</td>
			<td>
				<input type="checkbox" name="vehiclereason[]" value="<?php echo $member['id']; ?>" checked />
			</td>
		</tr>
<?php				
			}
			
		} else {
			logError("$sql - " . mysql_error());
		}
?>
				</table>
			</td>
			<td>
				<table>
					<tr>
						<td><b>Include Reasons (Trailer)</b></td>
					</tr>
<?php 
		$sql = "SELECT id, name
				FROM {$_SESSION['DB_PREFIX']}trailerunavailabilityreasons
				ORDER BY name";
		$result = mysql_query($sql);
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
?>
		<tr>
			<td>
			<?php 
				echo $member['name']; 
			?>
			</td>
			<td>
				<input type="checkbox" name="trailerreason[]" value="<?php echo $member['id']; ?>" checked />
			</td>
		</tr>
<?php				
			}
			
		} else {
			logError("$sql - " . mysql_error());
		}
?>
				</table>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<a href="javascript: if (verifyStandardForm('#searchform')) submit();" class="link1"><em><b>Run</b></em></a>
			</td>
		</tr>
	</table>
</form>
<script>
	function submit(e) {
		$('#searchform').submit();
		e.preventDefault();
		
	}
</script>
<?php 
	include("system-footer.php"); 
?>