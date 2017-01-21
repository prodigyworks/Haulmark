<?php
	require_once("system-db.php");
	require_once("businessobjects/UserCollectionClass.php");
	require_once("businessobjects/UserClass.php");
	require_once("businessobjects/MessageClass.php");
	
	start_db();
	
	if (isset($_POST['mailcommand'])) {
		
		foreach($_POST['ticked'] as $chkval) {
			sendInternalUserMessage(
					$chkval, 
					$_POST['subject'], 
					$_POST['composemessagetext'], 
					"", 
					array(), 
					"", 
					getLoggedOnMemberID()
				);
        }
		
		header("location: messages.php");
	}
	
	require_once("system-header.php");
	require_once("tinymce.php");
	
	include("mailtabstrip.php");
?>

<SCRIPT>
	function send() {
		$("#mailcommand").val("X");
		$("#frmpost").submit();
	}
	
</SCRIPT>
<div class="viewmessage containerform">
	<form method="POST" id="frmpost" name="frmpost">
		<input type="hidden" name="mailcommand" id="mailcommand" />
		<table class="composetable" cellspacing=0 cellpadding=0>
			<tr>
				<td valign=top>
					Send To
				</td>
				<td valign=top>
					<div class="userselector">
						<table>
<?php
							$users = new UserCollectionClass();
							$users->loadAvailbleForMessaging();
							
							foreach($users->getUsers() AS $user) {
?>
							<tr>
								<td width='16px'>	
									<input type="checkbox" id="ticked" name="ticked[]" class="ticked" value="<?php echo $user->getMemberid(); ?>" />
								</td>
								<td width='30px'>	
									<?php
										if ($user->getImageid() != null && $user->getImageid() != 0) {
									?>
										<img height=32 style='max-width:30px' src="system-imageviewer.php?id=<?php echo $user->getImageid(); ?>" />
									<?php
										}
									?>
								</td>
								<td>	
<?php 
									if ($user->isCustomerUser()) {
										echo $user->getFullname() . " (<span class='customeruser'>" . $user->getCustomer()->getName() . "</span>)"; 
										
									} else {
										echo $user->getFullname(); 
									}
?>
								</td>
							</tr>	
<?php
							}
						?>
						</table>
					</div>
				</td>
			</tr>
			<tr>
				<td valign=top>
					Subject
				</td>
				<td valign=top>
					<input type="text" name="subject" id="subject" cols=80 style='width:400px' />
				</td>
			</tr>
			<tr>
				<td valign=top>
					Message
				</td>
				<td valign=top>
					<textarea id="composemessagetext" name="composemessagetext" cols=60 rows=5 class="tinyMCE"></textarea>
				</td>
			</tr>
			<tr>
				<td valign=top>
					&nbsp;
				</td>
				<td valign=top>
					<a class='link2 rgap5' href="javascript: send()"><em><b>Send</b></em></a>
					<a class='link2' href='messages.php'><em><b>Cancel</b></em></a>
				</td>
			</tr>
		</table>
		<script>
			$(document).ready(
					function() {
<?php
						if (isset($_GET['id'])) {
							$message = new MessageClass();
							$message->load($_GET['id']);
?>
							$(".ticked[value=<?php echo $message->getFromMemberID(); ?>]").attr("checked", true);
							$("#subject").val("Re: <?php echo mysql_escape_string($message->getSubject()); ?>");
							$("#composemessagetext").val("<br><hr /><?php echo mysql_escape_string($message->getMessage()); ?>");
<?php
						} else if (isset($_GET['forwardid'])) {
							$message = new MessageClass();
							$message->load($_GET['forwardid']);
?>
							$("#subject").val("Fwd: <?php echo mysql_escape_string($message->getSubject()); ?>");
							$("#composemessagetext").val("<br><hr /><?php echo mysql_escape_string($message->getMessage()); ?>");
<?php
						} else {
?>
							$("#subject").val("");
							$("#composemessagetext").val("");
<?php
						}
?>
					}
				);
		</script>
	</form>
</div>
<?php
	require_once("system-footer.php");
?>
