<?php

if (!function_exists('pdfmakerGetPaiementFacture')) {

    function pdfmakerGetPaiementFacture($entityid) {
        global $adb;
        $query = "SELECT its4you_cashflow4you.paymentdate,its4you_cashflow4you.relationid,its4you_cashflow4you.cashflow4you_paymethod
                FROM its4you_cashflow4you 
                WHERE its4you_cashflow4you.relationid = ? ";
        $message = '';
        $result = $adb->pquery($query, array($entityid));
        $num_rows_payments = $adb->num_rows($result);
        if ($num_rows_payments) {
            for ($i = 0; $i < $num_rows_payments; $i++) {
                $date_payments = $adb->query_result($result, $i, 'paymentdate');
                $methode_payments = $adb->query_result($result, $i, 'cashflow4you_paymethod');
                $methode_payments = ($methode_payments != "") ? ' par ' . $methode_payments : "";
                $message .= 'Facture acquittée le ' . $date_payments . $methode_payments . "\n";
            }
        }



        return $message;
    }

}
