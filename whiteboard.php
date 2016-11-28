<?php
	include("system-embeddedheader.php");
	include("tinymce.php");
?>
<style>
	iframe {
		font-size:2px;
	}
</style>
<table width='100%' height='100%' border=1 >
	<tr valign="top" height='50%'>
		<td width='60%' height='100%'>
			<iframe width='100%' height='100%' src='https://www.haulageplanner.co.uk/booking.php'></iframe>
		</td>
		<td width='40%' style="padding:5px">
			<div id="chatcontainer">
				<span>Date : </span>
				<input id="displaydate" type="text" class="datepicker" value="<?php echo date("d/m/Y"); ?>" />
				<br>
				<br>
				<hr />
				<div class='livechat'>
					<div class="status" id="newstatus"></div>
					<div class="status" id="completedstatus"></div>
					<div class="status" id="cancelledstatus"></div>
				</div>
			</div>
		</td>
	</tr>
	<tr valign="top" height='50%'>
		<td colspan=2>
			<iframe width='100%' height='100%' src='https://webfleet.telematics.tomtom.com/swf/index.html?clientid=__ttt_cli_3fe44b12fca02b0a&locale=en_GB'></iframe>
		</td>
	</tr>
</table>
<?php
	include("chatfeed.php");
?>
<script>
	$(document).ready(function() {
			showChatRelay();

			$("#displaydate").change(
					function() {
						clearChat();

						showChatRelay();
					}
				);
		});
	
</script>

<?php
	include("system-embeddedfooter.php");
?>
