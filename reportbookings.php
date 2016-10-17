<?php
	require_once("system-header.php");
?>
<form id="reportform" class="reportform" name="reportform" method="POST" action="bookingsreportsummary.php" target="_new">
	<h4><?php echo $_SESSION['title']; ?></h4>
	<table>
		<tr>
			<td>
				Date From
			</td>
			<td>
				<input class="datepicker" id="datefrom" name="datefrom" />
			</td>
		</tr>
		<tr>
			<td>
				Date To
			</td>
			<td>
				<input class="datepicker" id="dateto" name="dateto" />
			</td>
		</tr>
		<tr>
			<td>
				Status
			</td>
			<td>
				<SELECT id="status" name="status">
					<OPTION value="">All</OPTION>
					<OPTION value="P">Planned</OPTION>
					<OPTION value="A">Accepted</OPTION>
				</SELECT>
			</td>
		</tr>
		<tr>
			<td>Vehicle</td>
			<td>
				<?php createCombo("vehicleid", "id", "registration", "{$_SESSION['DB_PREFIX']}vehicle", "WHERE active = 'Y'"); ?>
			</td>
		</tr>
		<tr>
			<td>
				Driver
			</td>
			<td>
				<?php createCombo("driverid", "id", "code", "{$_SESSION['DB_PREFIX']}driver"); ?>
			</td>
		</tr>
		<tr>
			<td>
				Order
			</td>
			<td>
				<SELECT id="orderby" name="orderby">
					<OPTION value="V">Vehicle</OPTION>
					<OPTION value="T">Date / Time</OPTION>
					<OPTION value="D">Driver</OPTION>
					<OPTION value="R">Trailer</OPTION>
				</SELECT>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
			<td>
				<a class="link1" href="javascript: runreport();"><em><b>Run Report</b></em></a>
			</td>
		</tr>
	</table>	
</form>
<script>
	function runreport(e) {
		$('#reportform').submit();
		
		try {
			e.preventDefault();
			
		} catch (e) {
			
		}
	}
</script>
<?php
	require_once("system-footer.php");
?>
