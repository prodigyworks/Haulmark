<?php
    require_once("system-db.php");
	require('invoicereportlib.php');
	
    start_db();

    $sql = "SELECT A.id
            FROM {$_SESSION['DB_PREFIX']}invoice A
            WHERE (A.downloaded != 'Y' OR A.downloaded IS NULL)
            ORDER BY A.id";
    $result = mysql_query($sql);

    if (! $result) {
        logError($sql . " - " . mysql_error());
    }
    
    $downloaddir = "uploads/invoicedownload";
    
    mkdir($downloaddir);
    
    $files = glob($downloaddir);

    /* Clear down */
	foreach($files as $files){
	  	if(is_file($file)) {
			unlink($file);
	  	}
	}    
	
	$zipname = "$downloaddir/invoices.zip";
	unlink($zipname);
	
	$zip = new ZipArchive();
	
    if ($zip->open($zipname, ZIPARCHIVE::CREATE) !== true) { 
    	return false;
	}
	
    while (($member = mysql_fetch_assoc($result))) {
        $id = $member['id'];
        $file = "$downloaddir/invoice-$id.pdf";
        
        $pdf = new InvoiceReport( 'L', 'mm', 'A4', $id);
		$pdf->Output($file, "F");

		$zip->addFile($file, "invoice-$id.pdf");
    }
	
    $zip->close();
    
	$qry = "UPDATE {$_SESSION['DB_PREFIX']}invoice SET
			downloaded = 'Y'
			WHERE (downloaded != 'Y' OR downloaded IS NULL)";

	$result = mysql_query($qry);

	if (! $result) {
		logError($qry . " - " . mysql_error());
	}

    mysql_query("COMMIT");
    
	$expires = 60*60*24*14;
	header("Pragma: public");
	header("Cache-Control: maxage=".$expires);
	header('Content-Disposition: attachment; filename="' . $zipname . '"');
	header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
  	header("Content-type: application/zip");
  	
	echo file_get_contents($zipname);
?>