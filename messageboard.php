<?php
	include("system-header.php");
	include("tinymce.php");
?>
<style>
	.italic {
		font-style: italic;
		width:800px;
		padding:10px;
		border: 1px solid grey;
		height:300px;
		text-align: left;
		overflow: auto;
	}
</style>
<table width='100%'>
	<tr>
		<td align='center' width='100%'>
			<div>New Message</div> 
			<textarea class="tinyMCE" id="livechatmessage" cols=40 rows=15 name="livechatmessage"></textarea>
			<br>
		</td>
	</tr>
	<tr>
		<td align='center' width='100%'>
			<a id="newmessagebutton" class='link1 nofloat'><em><b>Add Message</b></em></a>
			<br>
		</td>
	</tr>
	<tr>
		<td align='center' width='100%'>
			<input id="displaydate" type="hidden" value="<?php echo date("d/m/Y"); ?>" />
			<div class="italic livechat">
				<div class="status" id="newstatus" />
				<div class="status" id="completedstatus" />
				<div class="status" id="cancelledstatus" />
			</div>
		</td>
	</tr>
</table>
<script>
	$(document).ready(function() {
			$("#newmessagebutton").click(
					function() {
						tinyMCE.triggerSave();
						
						showChat($("#displaydate").val(), tinyMCE.get('livechatmessage').getContent());

						tinyMCE.get('livechatmessage').setContent("");
					}
				);

			showChatRelay();
		});
	
</script>
<?php
	include("chatfeed.php");
	include("system-footer.php");
?>
