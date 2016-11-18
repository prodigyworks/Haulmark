<?php 
	require_once("system-header.php"); 
?>
<STYLE>
	.scroller {
		height:300px;
		overflow:auto;
		border:1px solid #CCCCCC;
		padding:2px;
	}
	form {
		padding:5px;
	}
</STYLE>
<SCRIPT>
	$(document).ready(
			function() {
				$(".scroller").css("height", $("body").attr("offsetHeight") - 320);
			}
		);
</SCRIPT>
<form method="POST" id="searchform" action="autoinvoicesave.php">
	<h4><?php echo $_SESSION['title']; ?></h4>
	<input type="hidden" name="yourordernumber" value="<?php echo $_POST['yourordernumber']; ?>" />
	<div class='scroller'>
		<table class='grid list' width='100%' border=0 cellspacing=0>
			<thead>
				<tr>
					<td width='5px'> </td>
					<td width='20%'>Customer</td>
					<td width='10%'>Booking</td>
					<td width='10%'>Date</td>
					<td width='50%'>Summary</td>
					<td width='10%' align=right>Total Cost</td>
				</tr>
			</thead>
<?php 
	$customerid = $_POST['customerid'];
	$startdate = convertStringToDate($_POST['fromdate']);
	$enddate = convertStringToDate($_POST['todate']);
	$sql = "SELECT A.*, B.name, DATE_FORMAT(A.startdatetime, '%d/%m/%Y') AS startdate
			FROM {$_SESSION['DB_PREFIX']}booking A
			INNER JOIN {$_SESSION['DB_PREFIX']}customer B
			ON B.id = A.customerid
			WHERE A.customerid = $customerid
			AND DATE(A.startdatetime) >= '$startdate'
			AND DATE(A.startdatetime) <= '$enddate'
			AND A.id NOT IN (SELECT productid FROM {$_SESSION['DB_PREFIX']}invoiceitem)
			ORDER BY A.id";
	
	$result = mysql_query($sql);
	
	if (! $result) {
		logError("sql - " . mysql_error());
	}
	
	$total = 0;
	
	while (($member = mysql_fetch_assoc($result))) {
		$total += $member['charge'];
?>
			<TR class='booking'>
				<TD><INPUT class='checker' type='checkbox' name='selected[]' checked value="<?php echo $member['id']; ?>" /></TD>
				<TD><?php echo $member['name']; ?></TD>
				<TD><?php echo getBookingReference($member['id']); ?></TD>
				<TD><?php echo $member['startdate']; ?></TD>
				<TD><?php echo $member['legsummary']; ?></TD>
				<TD align=right class='total' total='<?php echo $member['charge']; ?>'>&pound;<?php echo $member['charge']; ?></TD>
			</TR>
<?php		
	}
?>
			<TR>
				<TD style='border-top:1px solid black' colspan=5 align=right><b>Total:</b></TD>
				<TD style='border-top:1px solid black' colspan=5 align=right><b>&pound;<span id='total'><?php echo number_format($total, 2); ?></span></b></TD>
			</TR>
		</table>
	</div>
	<br>
	<span><a href="javascript: if (verifyStandardForm('#searchform')) submit();" class="link2"><em><b>Confirm</b></em></a></span>
	<span><a href="autoinvoicing.php" class="rgap2 link2"><em><b>Back</b></em></a></span>
</form>
<script>
	function submit(e) {
		$('#searchform').submit();
		e.preventDefault();
		
	}

	$(document).ready(
			function() {
				$("input[name='selected[]']").change(
						function() {
							var total = 0;

							$(".booking").each(
									function() {
										if ($(this).find(".checker").attr("checked")) {
											total += parseFloat($(this).find(".total").attr("total"));
										}
									}
								);

							$("#total").html(new Number(total).toLocaleString());
						}
					);
			}
		);
</script>
<?php 
	include("system-footer.php"); 
?>