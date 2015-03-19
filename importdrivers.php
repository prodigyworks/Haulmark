<?php
	include("system-header.php"); 

	$monthnames = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");

	function convertThisDate($dateString) {
	
		$day = substr($dateString, 0, 2);
		$month = substr($dateString, 3, 3);
		$year = "20" . substr($dateString, 7, 2);
		$monthnumber = "1";

		for ($i = 0; $i < count($monthnames); $i++) {
			if ($monthnames[$i] == $month) {
				$monthnumber = ($i + 1);
			}
			
		}

		if (strlen($monthnumber) == 1) {
			$monthnumber = "0" . $monthnumber;
		}

		return $year . "-" . $monthnumber . "-" . $day;

	}
	
	
	if (isset($_FILES['importfile']) && $_FILES['importfile']['tmp_name'] != "") {
		if ($_FILES["importfile"]["error"] > 0) {
			echo "Error: " . $_FILES["importfile"]["error"] . "<br />";
			
		} else {
		  	echo "Upload: " . $_FILES["importfile"]["name"] . "<br />";
		  	echo "Type: " . $_FILES["importfile"]["type"] . "<br />";
		  	echo "Size: " . ($_FILES["importfile"]["size"] / 1024) . " Kb<br />";
		  	echo "Stored in: " . $_FILES["importfile"]["tmp_name"] . "<br>";
		}
		
		$subcat1 = "";
		$row = 1;
		
		if (($handle = fopen($_FILES['importfile']['tmp_name'], "r")) !== FALSE) {
		    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		        if ($row++ == 1) {
		        	continue;
		        }
		        
		        $num = count($data);
		        $index = 0;
		        
		        $name = mysql_escape_string($data[$index++]);
		        $street = mysql_escape_string($data[$index++]);
		        $town = mysql_escape_string($data[$index++]);
                $city = mysql_escape_string($data[$index++]);
				$county = mysql_escape_string($data[$index++]);
				$addressextra = mysql_escape_string($data[$index++]);
				$telephone = mysql_escape_string($data[$index++]);
				$fax = mysql_escape_string($data[$index++]);
				$code = mysql_escape_string($data[$index++]);
				$postcode = mysql_escape_string($data[$index++]);
				$reference = mysql_escape_string($data[$index++]);
				$hgvlicenceexpire = convertThisDate(mysql_escape_string($data[$index++]));
		        		        
		        if ($data[0] != "") {
		        	echo "<div>Driver: $name</div>";

					if (substr($code, 0, 1) == "A") {
						$agencydriver = "Y";
						
					} else {
						$agencydriver = "N";
					}
		        		
					$qry = "INSERT INTO {$_SESSION['DB_PREFIX']}driver 
							(
							name, street, town,
							city, county, addressextra,
							telephone, fax, code, postcode, reference, 
							hgvlicenceexpire, agencydriver
							)  
							VALUES  
							(
							'$name', '$street', '$town',
							'$city', '$county', '$addressextra',
							'$telephone', '$fax', '$code', '$postcode', '$reference', 
							'$hgvlicenceexpire', '$agencydriver'
							)";
							
					$result = mysql_query($qry);
        	
					if (mysql_errno() != 1062 && mysql_errno() != 0 ) {
						logError(mysql_error() . " : " .  $qry);
					}
				}				
		    }
		    
		    fclose($handle);
			echo "<h1>" . $row . " downloaded</h1>";
		}
	}
	
	if (! isset($_FILES['importfile'])) {
?>	
		
<form class="contentform" method="post" enctype="multipart/form-data">
	<label>Upload driver CSV file </label>
	<input type="file" name="importfile" id="importfile" /> 
	
	<br />
	 	
	<div id="submit" class="show">
		<input type="submit" value="Upload" />
	</div>
</form>
<?php
	}
	
	include("system-footer.php"); 
?>