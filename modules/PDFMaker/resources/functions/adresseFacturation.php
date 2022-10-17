<?php

if (!function_exists('pdfmakerGetAdresseFacturation')) {

    function pdfmakerGetAdresseFacturation($entityid) {
        global $adb;
        $info_client = array();
        $query = "SELECT accountname,bill_street,bill_city,bill_code,phone,email1
            FROM vtiger_account
            INNER JOIN vtiger_accountbillads on vtiger_accountbillads.accountaddressid = vtiger_account.accountid
            INNER JOIN vtiger_accountscf on vtiger_accountscf.accountid = vtiger_account.accountid 
            INNER JOIN vtiger_invoice on vtiger_invoice.accountid = vtiger_account.accountid
            WHERE vtiger_invoice.invoiceid = ?";
        $result = $adb->pquery($query, array($entityid));
        $num_rows = $adb->num_rows($result);
        if ($num_rows) {
            $accountname = $adb->query_result($result, 0, 'accountname');
            $bill_street = $adb->query_result($result, 0, 'bill_street');
            $bill_city = $adb->query_result($result, 0, 'bill_city');
            $bill_code = $adb->query_result($result, 0, 'bill_code');
            $phone = $adb->query_result($result, 0, 'phone');
            $email = $adb->query_result($result, 0, 'email1');
            $adresscompl = $adb->query_result($result, 0, 'ship_street');

            $accountname = ucwords(strtolower((formatString($accountname))));
            $adresse = ucwords(strtolower((formatString($bill_street))));
            $adresscompl = formatString($adresscompl);
            $ville = strtoupper(formatString($bill_city));
            $cp = $bill_code;
            $phone = $phone;
            $email = $email;

            $app = $accountname . '<br/>' .
                    $adresse . '<br/>';
            if (trim($adresscompl) != "") {
                $app .= $adresscompl . '<br/>';
            }
            $app .= $cp . " " . $ville . '<br/>';
        }

        return $app;
    }

}
