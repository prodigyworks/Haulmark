<?php
	require_once("system-header.php");
?>
<form id="reportform" class="reportform" name="reportform" method="POST" action="upncsv.php" target="_new">
	<table>
		<tr>
			<td>
				Date
			</td>
			<td>
				<input class="datepicker" id="upndate" name="upndate" />
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
			<td>
				<a class="link1" href="javascript: runreport();"><em><b>Download From UPN</b></em></a>
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
