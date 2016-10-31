<?php
set_time_limit (100000);

require_once("system-db.php");

start_db();

backup_tables();

/* backup the db OR just a table */
function backup_tables($tables = '*') {
	//get all of the tables
	if($tables == '*') {
		$tables = array();
		$result = mysql_query('SHOW TABLES');
		
		while($row = mysql_fetch_row($result)) {
			$tables[] = $row[0];
		}
		
	} else {
		$tables = is_array($tables) ? $tables : explode(',',$tables);
	}
	
	//cycle through
	foreach($tables as $table) {
		echo "<div>Backing up table : $table</div>";
		$result = mysql_query('SELECT * FROM '.$table);
		$num_fields = mysql_num_fields($result);
		
		$return.= 'DROP TABLE '.$table.';';
		$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
		
		if (! $row2) {
			$return .= "ERROR:" . mysql_error() . "\n\n";
		}
		$return.= "\n\n".$row2[1].";\n\n";
		
		for ($i = 0; $i < $num_fields; $i++) {
			while($row = mysql_fetch_row($result)) {
				$return.= 'INSERT INTO '.$table.' VALUES(';
				
				for($j=0; $j<$num_fields; $j++) {
					$type  = mysql_field_type($result, $j);
					
					if ($type == "blob") {
						$row[$j] = bin2hex($row[$j]);
						
						if (isset($row[$j])) { 
							$return.= '0x'.$row[$j]; 
							
						} else { 
							$return.= '""'; 
						}
						
					} else {
						$row[$j] = mysql_escape_string($row[$j]);
						
						if (isset($row[$j])) { 
							$return.= '"'.$row[$j].'"' ; 
						
						} else { 
							$return.= '""'; 
						}
					}

					if ($j<($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			}
		}
		$return.="\n\n\n";
	}
	
	//save file
	$handle = fopen('backup/db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql','w+');
	fwrite($handle,$return);
	fclose($handle);
	
	echo "<h2>Done</h2>";
}
?>