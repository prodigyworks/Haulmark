<?php
	require_once("system-db.php");
	require_once("businessobjects/MessageCollectionClass.php");
	require_once("businessobjects/MessageClass.php");
	
	start_db();
	
	if (isset($_POST['mailcommand'])) {
		try {
			if ($_POST['mailcommand'] == "delete") {
				MessageClass::deleteSelectedMessages($_POST['ticked']);
				
			} else if ($_POST['mailcommand'] == "markread") {
				MessageClass::markSelectedMessagesAsRead($_POST['ticked']);
				
			} else if ($_POST['mailcommand'] == "markunread") {
				MessageClass::markSelectedMessagesAsUnread($_POST['ticked']);
			}
			
		} catch (Exception $e) {
			logError($e);
		}
		
		header("location: " . $_SERVER['REQUEST_URI']);
	}
	
	$_SESSION['SESSION_MESSAGE_COUNT'] = MessageClass::getUnreadMessageCountForUser(getLoggedOnMemberID());
	
	require_once("system-header.php");
	
	include("mailtabstrip.php");
?>
<div class="containerform">
	<div class="messageheader">	
		<span>Select </span>
		<a href='javascript: selectAll()'>All</a>, 
		<a href='javascript: selectNone()'>None</a>, 
		<a href='javascript: selectRead()'>Read</a>, 
		<a href='javascript: selectUnread()'>Unread</a> 
		<SELECT id='actions' name="actions">
			<OPTION value="">Actions ...</OPTION>
			<OPTION value="D">Delete</OPTION>
			<OPTION value="R">Mark as read</OPTION>
			<OPTION value="U">Mark as unread</OPTION>
		</SELECT>
	</div>
	<STYLE>
		.status_N TD H4 {
			font-weight: bold;
			color: black ! important;
		}
	</STYLE>
	<SCRIPT>
		function selectUnread() {
			$(".mailtable").each(
					function() {
						if ($(this).attr("status") == "R") {
							$(this).find(".ticked").attr("checked", false);
							
						} else {
							$(this).find(".ticked").attr("checked", true);
						}
					}
				);
		}
		
		function selectRead() {
			$(".mailtable").each(
					function() {
						if ($(this).attr("status") == "N") {
							$(this).find(".ticked").attr("checked", false);
							
						} else {
							$(this).find(".ticked").attr("checked", true);
						}
					}
				);
		}
		
		function selectAll() {
			$(".ticked").attr("checked", true);
		}
		
		function selectNone() {
			$(".ticked").attr("checked", false);
		}
		
		function deleteMessages() {
			postCommand("delete");
		}
		
		function markRead() {
			postCommand("markread");
		}
		
		function markUnread() {
			postCommand("markunread");
		}
		
		function postCommand(command) {
			$("#mailcommand").val(command);
			$("#frmpost").submit();
		}
		
	</SCRIPT>
	<form method="POST" id="frmpost" name="frmpost">
		<input type="hidden" name="mailcommand" id="mailcommand" />
		<table width='100%' cellpadding=4 cellspacing=0>
<?php
			$messages = new MessageCollectionClass();
				
			if (isset($_GET['mode'])) {
				if ($_GET['mode'] == "S") {
					$messages->loadSentMessagesByUser(getLoggedOnMemberID());
				
				} else  if ($_GET['mode'] == "D") {
					$messages->loadArchivedMessagesByUser(getLoggedOnMemberID());
					
				} else  if ($_GET['mode'] == "I") {
					$messages->loadInboxByUser(getLoggedOnMemberID());
				}
				
			} else {
				$messages->loadInboxByUser(getLoggedOnMemberID());
			}
			
			foreach ($messages->getMessages() as $message) {
?>
			<tr class='mailtable status_<?php echo $message->getStatus(); ?>' status='<?php echo $message->getStatus(); ?>'>
				<td width='42px' valign=center>
					<input type="checkbox" id="ticked" name="ticked[]" class="ticked" value="<?php echo $message->getId(); ?>" />
				</td>
				<td  valign=center class='clickable' width='42px'>
<?php
					echo $message->getStatusImageName();
?>
				</td>
				<td class='clickable' valign=center width='42px'>
<?php
					if ($message->getFromMember()->getImageid() != null && $message->getFromMember()->getImageid() != 0) {
?>
					<img height=32 style='max-width:40px' src="system-imageviewer.php?id=<?php echo $message->getFromMember()->getImageid(); ?>" />
<?php
					}
?>
				</td>
				<td class='clickable' valign=top>
<?php
					if (isset($_GET['mode']) && $_GET['mode'] == "S") {
?>
					<h4><?php echo $message->getToMember()->getFullname(); ?></h4>
<?php
					} else {
?>		
					<h4><?php echo $message->getFromMember()->getFullname(); ?></h4>
					
<?php
					}
?>
					<div><?php echo $message->dateToDMY($message->getCreateddate()); ?></div>
				</td>
				<td class='clickable' valign=top nowrap>
					<h4><?php echo $message->getSubject(); ?></h4>
					<div style='height:20px; overflow: hidden'><?php echo $message->getMessage(); ?></div>
				</td>
			</tr>
<?php
			}
?>
		</table>
		<script>
		$(document).ready(
				function() {
					$(".ticked").change(
							function(e) {
								e.stopPropogation();
							}
						);
					
					$(".clickable").click(
							function() {
								navigate("messageview.php?id=" + $(this).parent().find(".ticked").val());
							}
						);
					
					$("#actions").change(
							function() {
								if ($(this).val() == "D") {
									deleteMessages();
								
								} else if ($(this).val() == "R") {
									markRead();
									
								} else if ($(this).val() == "U") {
									markUnread();
								}
							}
						);
				}
			);
		</script>
	</form>
</div>
<?php
	require_once("system-footer.php");
?>
