<?php
	if (isUserInRole("ADMIN")) {
		header("location: addpod.php");
		
	} else {
		header("location: mypod.php");
	}