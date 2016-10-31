<?php
    require_once("system-db.php");

    start_db();

    $sql = "SELECT DATE_FORMAT(A.orderdate, '%Y%m%d') AS orderdate2, A.*, 
    		C.name, 
    		B.accountcode, 
    		D.linetotal, D.vat, D.id AS itemid, D.productid
            FROM {$_SESSION['DB_PREFIX']}invoice A
            INNER JOIN {$_SESSION['DB_PREFIX']}customer B
            ON B.id = A.customerid
            LEFT OUTER JOIN {$_SESSION['DB_PREFIX']}taxcode C
            ON C.id = B.taxcodeid
            INNER JOIN {$_SESSION['DB_PREFIX']}invoiceitem D
            ON D.invoiceid = A.id
            WHERE (A.exported != 'Y' OR A.exported IS NULL)
            ORDER BY A.id";
    $result = mysql_query($sql);

    if (! $result) {
        logError($sql . " - " . mysql_error());
    }

    $array = array();
    $headings = array(
            'Type',
            'Account Code',
            'Sage Ref',
            'Invoice Date',
            'ID',
            'Description',
            'Total',
            'Tax Code',
            'VAT',
        );

    while (($member = mysql_fetch_assoc($result))) {
        $id = $member['itemid'];
        $accountcode = $member['accountcode'];
        $charge = $member['linetotal'];
        $taxcode = $member['name'];
        $bookingid = $member['productid'];
        $invoicedate = $member['orderdate2'];
        $sagecustomerref = $member['sagecustomerref'];
        $itemid = $member['itemid'];
        $description = "SALES TRANSPORT - " . getBookingReference($bookingid);
        $vat = $member['vat'];

        array_push(
                $array,
                array(
                        'SI',
                        $accountcode,
                        $sagecustomerref,
                        $invoicedate,
                        $id,
                        $description,
                        $charge,
                        $taxcode,
                        $vat
                    )
            );
    }
    
	$qry = "UPDATE {$_SESSION['DB_PREFIX']}invoice SET
			exported = 'Y'
			WHERE (exported != 'Y' OR exported IS NULL)";

	$result = mysql_query($qry);

	if (! $result) {
		logError($qry . " - " . mysql_error());
	}

    mysql_query("COMMIT");

    // Open the output stream
    $fh = fopen('php://output', 'w');

    // Start output buffering (to capture stream contents)
    ob_start();

    fputcsv($fh, $headings);

    // Loop over the * to export
    if (! empty($array)) {
        foreach ($array as $item) {
            fputcsv($fh, $item);
        }
    }

    // Get the contents of the output buffer
    $string = ob_get_clean();

    $filename = 'csv_' . date('Ymd') .'_' . date('His');

    // Output CSV-specific headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private", false);
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"$filename.csv\";" );
    header("Content-Transfer-Encoding: binary");

    exit($string);
?>