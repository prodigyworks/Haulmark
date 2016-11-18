<?php
	require_once("system-header.php");
?>
<form id="reportform" class="reportform" name="reportform" method="POST" action="invoicereportsummary.php" target="_new">
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
					<OPTION value="Y">Paid</OPTION>
					<OPTION value="N">Outstanding</OPTION>
				</SELECT>
			</td>
		</tr>
		<tr>
			<td>
				Court / Client
			</td>
			<td>
				<?php createCombo("courtid", "id", "name", "{$_SESSION['DB_PREFIX']}courts"); ?>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
			<td>
				<a class="link2" href="javascript: runreport();"><em><b>Run Report</b></em></a>
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
