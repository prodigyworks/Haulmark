<?php include("system-header.php"); ?>

<!--  Start of content -->
<?php
if( isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) >0 ) {
?>
<div id="errorwindow">
	<?php showErrors(); ?>
</div>
<?php
}
?>
	<form id="loginForm" enctype="multipart/form-data" name="loginForm" class="entryform" method="post" action="system-register-exec.php">
	<h4><?php echo $_SESSION['title']; ?></h4>
	  <table border="0" align="left">
	    <tr>
	      <td>First Name </td>
	      <td><input required="true" name="fname" type="text" class="textfield" id="fname" /></td>
	    </tr>
	    <tr>
	      <td>Last Name </td>
	      <td><input required="true" name="lname" type="text" class="textfield" id="lname" /></td>
	    </tr>
	    <tr>
	      <td>Account Type </td>
	      <td>
	      	<SELECT id="accounttype" name="accounttype" onchange="accounttype_onchange()">
	      		<OPTION value="ALLEGRO">Employee</OPTION>
	      		<OPTION value="CUSTOMER">Customer</OPTION>
	      		<OPTION value="DRIVER">Driver</OPTION>
	      		<OPTION value="MAINTENANCE">Maintenance</OPTION>
	      	</SELECT>
	      </td>
	    </tr>
	    <tr id="customertype" class="hidden usertype">
	      <td>Customer</td>
	      <td>
	      	<?php createCombo("customerid", "id", "name", "{$_SESSION['DB_PREFIX']}customer", "", false)?>
	      </td>
	    </tr>
	    <tr id="suppliertype" class="hidden usertype">
	      <td>Supplier</td>
	      <td>
	      	<?php createCombo("supplierid", "id", "name", "{$_SESSION['DB_PREFIX']}supplier", "", false)?>
	      </td>
	    </tr>
	    <tr id="drivertype" class="hidden usertype">
	      <td>Driver</td>
	      <td>
	      	<?php createCombo("driverid", "id", "name", "{$_SESSION['DB_PREFIX']}driver", "", false)?>
	      </td>
	    </tr>
	    <tr>
	      <td>Login</td>
	      <td><input required="true" name="login" type="text" class="textfield logintext" id="login" /></td>
	    </tr>
	    <tr>
	      <td>Email</td>
	      <td><input required="true" name="email" type="email" class="textfield60" id="email" /></td>
	    </tr>
	    <tr>
	      <td>Confirm Email</td>
	      <td><input required="true" name="confirmemail" type="email" class="textfield60" id="confirmemail" /></td>
	    </tr>
	    <tr>
	      <td>Image</td>
	      <td><input name="image" type="file" class="textfield60" id="image" /></td>
	    </tr>
	    <tr>
	    	<td colspan="2">
	    		<br />
	    		<h4>Security</h4>
	    		<hr />
	    	</td>
	    </tr>
	    <tr>
	      <td>Password</td>
	      <td>
	      	<input required="true" name="password" type="password" class="textfield pwd" id="password" />
	      </td>
	    </tr>
	    <tr>
	      <td>Confirm Password </td>
	      <td><input required="true" name="cpassword" type="password" class="textfield" id="cpassword" /></td>
	    </tr>
	    <tr>
	      <td>&nbsp;</td>
	      <td>
	  		<span class="wrapper"><a class='link1' href="javascript:if (verify()) $('#loginForm').submit();"><em><b>Submit</b></em></a></span>
	      </td>
	    </tr>
	  </table>
	  <input type="hidden" id="description" name="description" value="Profile image" />
	</form>
<script>
	$(document).ready(function() {
		$(".pwd").blur(verifypassword);
		$(".logintext").blur(checkLogin);
		$("#email").blur(checkEmail);
		$("#cpassword").blur(verifycpassword);
		$("#confirmemail").blur(verifycemail);
		$("#fname").focus();
		
	});

	function accounttype_onchange() {
		$(".usertype").val("0");
		$(".usertype").hide();
		
		if ($("#accounttype").val() == "DRIVER") {
			$("#drivertype").show();

		} else if ($("#accounttype").val() == "MAINTENANCE") {
			$("#suppliertype").show();

		} else if ($("#accounttype").val() == "CUSTOMER") {
			$("#customertype").show();
		}
	}
				
	function verify() {
		var isValid = verifyStandardForm('#loginForm');
		
		if (! verifypassword()) {
			isValid = false;
		}
		
		if (! verifycpassword()) {
			isValid = false;
		}
		
		if (! checkLogin()) {
			isValid = false;
		}
		
		if (! checkEmail()) {
			isValid = false;
		}
		
		if (! verifycemail()) {
			isValid = false;
		}
		
		return isValid;
	}
	
	function verifypassword() {
		var node = $(".pwd");
		var str = $(node).val();
		
		return true;
	}
	
	function verifycpassword() {
		var node = $("#cpassword");
		var str = $(node).val();
		
		if ($(node).val() == "") {
			return false;
		}
		
		if( str == $(".pwd").val()) {
			$(node).removeClass("invalid");
			$(node).next().css("visibility", "hidden");
			$(node).next().attr("title", "Required field.");
			
			return true;
			
		} else {
			$(node).addClass("invalid");
			$(node).next().css("visibility", "visible");
			$(node).next().attr("title", "Passwords do not match.");
			
			return false;
		}
	}
	
	function checkLogin() {
		var node = $(".logintext");
		var returnValue = true;
		
		if ($(node).val() == "") {
			return false;
		}
		
		callAjax(
				"finduser.php", 
				{ 
					login: $(".logintext").val()
				},
				function(data) {
					if (data.length > 1) {
						$(node).addClass("invalid");
						$(node).next().css("visibility", "visible");
						$(node).next().attr("title", "Login is already in use.");
						
						returnValue = false;
						
					} else {
						$(node).removeClass("invalid");
						$(node).next().css("visibility", "hidden");
						$(node).next().attr("title", "Required field.");
					}
				},
				false
			);
			
		return returnValue;
	}
	
	function checkEmail() {
		var node = $("#email");
		var returnValue = true;
		
		if ($(node).val() == "") {
			return false;
		}
		
		callAjax(
				"findemail.php", 
				{ 
					email: $("#email").val()
				},
				function(data) {
					if (data.length > 1) {
						$(node).addClass("invalid");
						$(node).next().css("visibility", "visible");
						$(node).next().attr("title", "Email address is already in use by user " + data[0].login + "(" +  data[0].firstname  + " " + data[o].lastname + ").");
						
						returnValue = false;
						
					} else {
						$(node).removeClass("invalid");
						$(node).next().css("visibility", "hidden");
						$(node).next().attr("title", "Required field.");
					}
				},
				false
			);
			
		return returnValue;
	}
	
	function verifycemail() {
		var node = $("#confirmemail");
		var str = $(node).val();
		
		if ($(node).val() == "") {
			return false;
		}
		
		if( str == $("#email").val()) {
			$(node).removeClass("invalid");
			$(node).next().css("visibility", "hidden");
			$(node).next().attr("title", "Required field.");
			
			return true;
			
		} else {
			$(node).addClass("invalid");
			$(node).next().css("visibility", "visible");
			$(node).next().attr("title", "Email addresses do not match.");
			
			return false;
		}
	}
</script>
<!--  End of content -->

<?php include("system-footer.php"); ?>
