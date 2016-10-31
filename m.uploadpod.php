<?php 
	require_once("system-mobileheader.php"); 
?>
<style>
	td {
		padding: 10px;
		box-shadow: 10px 10px 5px #888888;
		border-radius: 25px;
	}
	
	td a {
		text-decoration: none;
		font-weight: bold;
		color: black;
	}
	
	tr td:first-child:hover {
		color: white;
		background-color: red ! important;
	}
	
	tr {
	}
	
	tr:nth-child(even) {
		background: #CCC;
	}
	
	tr:nth-child(odd) {
		background: #EEE;
	}
</style>
<center>
	<div class="upabit">
		<a href="m.index.php">
			<img alt="" src="images/back.png" height=30 />
		</a>
	</div>
</center>
<div class="centerform">
<center>
	<table width='70%' style="text-align:left" cellpadding=10>
<?php 
	$driverid = getLoggedOnDriverID();
	$sql = "SELECT A.place, A.id, B.id AS bookingid, C.name AS customername
			FROM {$_SESSION['DB_PREFIX']}bookingleg A
			INNER JOIN {$_SESSION['DB_PREFIX']}booking B
			ON B.id = A.bookingid
			INNER JOIN {$_SESSION['DB_PREFIX']}customer C
			ON C.id = B.customerid
			WHERE B.driverid = $driverid 
			AND B.statusid IN (3, 4, 5, 6, 7, 8)
			ORDER BY B.id, A.id";
	
	$result = mysql_query($sql);
	
	if($result) {
		while (($member = mysql_fetch_assoc($result))) {
?>
		<tr class="seperator">
			<td onclick="navigate('m.uploadpoddoc.php?id=<?php echo $member['id']; ?>')">
				<?php echo getBookingReference($member['bookingid']); ?>
			</td>
			<td><?php echo $member['customername']; ?></td>
			<td><?php echo $member['place']; ?></td>
		</tr>
<?php		
		}
		
	} else {
		logError("$sql - " . mysql_error());
	}
?>
	</table>
</center>
</div>
<?php 
	include("system-mobilefooter.php"); 
?>

