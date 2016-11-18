<?php 
	require_once("system-header.php"); 
?>
<form method="POST" id="searchform" class="reportform" action="autoinvoicingselect.php">
	<h4><?php echo $_SESSION['title']; ?></h4>
	<table>
		<tr>
			<td>Customer</td>
			<td>
				<?php createCombo("customerid", "id", "name", "{$_SESSION['DB_PREFIX']}customer"); ?>
			</td>
		</tr>
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
		<tr>
			<td>Your Reference</td>
			<td>
				<input type="text" id="yourordernumber" name="yourordernumber" size=30 required="true" />
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<a href="javascript: if (verifyStandardForm('#searchform')) submit();" class="link2"><em><b>Search</b></em></a>
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