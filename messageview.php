<?php
	require_once("system-db.php");
	require_once("businessobjects/MessageClass.php");
	
	start_db();
	
	$id = $_GET['id'];
	
	if (isset($_POST['mailcommand'])) {
		if ($_POST['mailcommand'] == "delete") {
			MessageClass::deleteSelectedMessages(array($id));
			
			header("location: messages.php");
			
		} else if ($_POST['mailcommand'] == "reply") {
			MessageClass::markSelectedMessagesAsReplied(array($id));
			
			header("location: messagereply.php?id=$id");
		}
		
	} else {
		MessageClass::markSelectedMessagesAsRead(array($id));
	}
	
	require_once("system-header.php");
	
	include("mailtabstrip.php");
?>
<SCRIPT>
	function deleteMessage() {
		postCommand("delete");
	}

	function replyMessage() {
		postCommand("reply");
	}

	function postCommand(command) {
		$("#mailcommand").val(command);
		$("#frmpost").submit();
	}
	
</SCRIPT>
<div class="viewmessage">
	<form method="POST" id="frmpost" name="frmpost">
		<input type="hidden" name="mailcommand" id="mailcommand" />
<?php
		$message = new MessageClass();
		$message->load($id);
?>
		<input type="hidden" name="subject" id="subject" value="<?php echo $message->getSubject(); ?>"/>
		<input type="hidden" name="from_member_id" id="from_member_id" value="<?php echo $message->getFromMemberID(); ?>"/>
		<input type="hidden" name="message" id="message" value="<?php echo mysql_escape_string($message->getMessage()); ?>"/>
		<table cellspacing=4>
			<tr>
				<td valign=top>
<?php
						if ($message->getFromMember()->getImageid() != null && $message->getFromMember()->getImageid() != 0) {
?>
						<img height=64 src="system-imageviewer.php?id=<?php echo $message->getFromMember()->getImageid(); ?>" />
<?php
						}
?>
				</td>
				<td>
					<h3><?php echo $message->getSubject(); ?></h3>
					<h5>From <span><?php echo $message->getFromMember()->getFullname(); ?></span> to You</h3>
					<p>Sent <?php echo $message->dateToDMY($message->getCreateddate()); ?></p>
				</td>
			</tr>
		</table>
		<hr />
		<ul class="toolbar">
			<li><a title='Reply' href="javascript: replyMessage()"><img height=16 src='images/replied.png' />&nbsp;Reply</a></li>
			<li><a title='Forward' href="messageforward.php?id=<?php echo $id; ?>"><img height=16 src='images/read.png' />&nbsp;Forward</a></li>
			<li><a title='Delete' href="javascript: deleteMessage()"><img height=16 src='images/delete.png' />&nbsp;Delete</a></li>
		</ul>
		<hr />
		<div class="messagecontent">
			<?php echo $message->getMessage(); ?>
		</div>
	</form>
</div>
<?php
	require_once("system-footer.php");
?>
