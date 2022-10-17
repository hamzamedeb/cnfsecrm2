<?php

class Invoice_Module_Model extends Inventory_Module_Model {

    //unicnfsecrm_mod_15
    /* uni_cnfsecrm - modif 103 - DEBUT */
    function getInvoiceByEcheance($echeance, $filtreRappel) {
        global $adb;
        $query = 'SELECT vtiger_invoice.invoiceid,vtiger_invoice.subject,vtiger_invoice.total,vtiger_invoice.balance, vtiger_invoicecf.cf_1033,vtiger_account.accountname 
            FROM vtiger_invoice 
            INNER JOIN vtiger_invoicecf on vtiger_invoicecf.invoiceid = vtiger_invoice.invoiceid 
            INNER JOIN vtiger_account on vtiger_account.accountid = vtiger_invoice.accountid 
            INNER JOIN vtiger_accountscf on vtiger_accountscf.accountid = vtiger_account.accountid 
            where cf_1004 != ? and invoicestatus != ? and cf_1208 != ? and vtiger_invoicecf.cf_1185 = ? and vtiger_invoice.invoicestatus NOT IN (?,?) ';

        if ($echeance == 'Dépassé de 7 jours') {
            $query .= 'AND vtiger_invoicecf.cf_1189 = ?';
        } else if ($echeance == 'Dépassé de 14 jours') {
            $query .= 'AND vtiger_invoicecf.cf_1191 = ?';
        } else if ($echeance == 'Dépassé de 30 jours') {
            $query .= 'AND vtiger_invoicecf.cf_1193 = ?';
        }

        if (($filtreRappel == 1) || ($filtreRappel == '')) {
            $rappel = 0;
        } else if ($filtreRappel == 2) {
            $rappel = 1;
        } else if ($filtreRappel == 3) {
            $echeance = "A ne pas relancer";
            $rappel = 0;
        }
//        /* uni_cnfsecrm - v2 - modif 140 - DEBUT */ else if ($filtreRappel == 4) {
//            $date = date('Y-m-d');
//            $date = date('Y-m-d', strtotime($date . ' -7 day'));
//            //var_dump($date);
//            $query .= ' AND ? >= (SELECT MIN(date_rappel) FROM vtiger_rappel_impayes WHERE vtiger_rappel_impayes.factureid = vtiger_invoice.invoiceid  ) ';
//            $rappel = 1;
//        }

        $query .= ' ORDER BY invoice_no ASC';

//        if ($filtreRappel == 4) {
//            $params = array('Financeur', 'Avoir', 1, $echeance, 'Paid', 'Cancel', $rappel, $date);
//        } else {
        $params = array('Financeur', 'Avoir', 1, $echeance, 'Paid', 'Cancel', $rappel);
//        }
        /* uni_cnfsecrm - v2 - modif 140 - FIN */
        $result = $adb->pquery($query, $params);
        $line_nbr = $adb->num_rows($result);
        $invoice7jours = array();
        for ($i = 0; $i < $line_nbr; $i++) {
            $total = number_format($adb->query_result($result, $i, 'total'), 2, '.', '');
            $balance_facture = number_format($adb->query_result($result, $i, 'balance'), 2, '.', '');

            $invoice7jours[$i]['invoiceid'] = $adb->query_result($result, $i, 'invoiceid');
            $invoice7jours[$i]['subject'] = $adb->query_result($result, $i, 'subject');
            $invoice7jours[$i]['total'] = $total;
            $invoice7jours[$i]['balance'] = $balance_facture;
            $invoice7jours[$i]['numero_facture'] = $adb->query_result($result, $i, 'cf_1033');
            $invoice7jours[$i]['accountname'] = $adb->query_result($result, $i, 'accountname');
        }
        return $invoice7jours;
    }

    /* uni_cnfsecrm - modif 103 - FIN */

    function getNbrFactureNonRelance($echeance) {
        global $adb;
        $query = 'SELECT count(*) 
            FROM vtiger_invoice 
            INNER JOIN vtiger_invoicecf on vtiger_invoicecf.invoiceid = vtiger_invoice.invoiceid 
            INNER JOIN vtiger_account on vtiger_account.accountid = vtiger_invoice.accountid 
            INNER JOIN vtiger_accountscf on vtiger_accountscf.accountid = vtiger_account.accountid 
            where cf_1004 != ? and invoicestatus != ? and cf_1208 != ? and vtiger_invoicecf.cf_1185 = ? and vtiger_invoice.invoicestatus NOT IN (?,?) ';

        if ($echeance == 'Dépassé de 7 jours') {
            $query .= 'AND vtiger_invoicecf.cf_1189 = ?';
        } else if ($echeance == 'Dépassé de 14 jours') {
            $query .= 'AND vtiger_invoicecf.cf_1191 = ?';
        } else if ($echeance == 'Dépassé de 30 jours') {
            $query .= 'AND vtiger_invoicecf.cf_1193 = ?';
        }

        $params = array('Financeur', 'Avoir', 1, $echeance, 'Paid', 'Cancel', 0);
        $result = $adb->pquery($query, $params);
        $nmbrFactureNonRelance = $adb->query_result($result, 'count(*)');
        return $nmbrFactureNonRelance;
    }

    //fin unicnfsecrm_mod_15
    /* uni_cnfsecrm - v2 - modif 146 - DEBUT */
    /* uni_cnfsecrm - v2 - modif 147 - DEBUT */
    function getImpayees45Jours($filtreRappel) {
        $date = date('Y-m-d');
        global $adb;
        $query = 'SELECT vtiger_invoice.invoiceid,vtiger_invoice.subject,vtiger_invoice.total,
                vtiger_invoice.balance,invoicedate, vtiger_invoicecf.cf_1033,vtiger_account.accountname ';
        if ($filtreRappel == 2) {
            $query .= ', (select MIN(DATE_ADD(date_rappel, INTERVAL 14 DAY)) 
                from vtiger_rappel_impayes 
                where vtiger_rappel_impayes.factureid = vtiger_invoice.invoiceid 
                and type_relance = ?) as daterelance ';
        }

        $query .= ' FROM vtiger_invoice 
                INNER JOIN vtiger_invoicecf on vtiger_invoicecf.invoiceid = vtiger_invoice.invoiceid 
                INNER JOIN vtiger_account on vtiger_account.accountid = vtiger_invoice.accountid 
                INNER JOIN vtiger_accountscf on vtiger_accountscf.accountid = vtiger_account.accountid 
                where invoicestatus != ? and cf_1208 = ? 
                and vtiger_invoice.invoicestatus NOT IN (?,?) 
                AND vtiger_invoicecf.cf_1193 = ? ';
        if (($filtreRappel == 1) || $filtreRappel == '') {
            $query .= ' AND DATE_ADD(invoicedate, INTERVAL 45 DAY)  <= ?  AND cf_1185 != ?';
        }
        if ($filtreRappel == 3) {
            $query .= ' AND cf_1185 = ? AND DATE_ADD(invoicedate, INTERVAL 45 DAY)  <= ?';
        }

        $query .= ' ORDER BY invoice_no ASC';


        if (($filtreRappel == 1) || $filtreRappel == '') {
            $params = array('Avoir', 1, 'Paid', 'Cancel', 0, $date, 'A ne pas relancer');
        } else if ($filtreRappel == 2) {
            $params = array('Depasse de 30 jours', 'Avoir', 1, 'Paid', 'Cancel', 1);
        } else if ($filtreRappel == 3) {
            $params = array('Avoir', 1, 'Paid', 'Cancel', 0, 'A ne pas relancer', $date);
        }

        //var_dump($date);
        $result = $adb->pquery($query, $params);
        $line_nbr = $adb->num_rows($result);
        //echo $line_nbr;
        $invoice45jours = array();
        for ($i = 0; $i < $line_nbr; $i++) {
            if ($filtreRappel == 2) {
                if ($adb->query_result($result, $i, 'daterelance') != '' && $adb->query_result($result, $i, 'daterelance') <= $date) {
                    if ($adb->query_result($result, $i, 'invoiceid') != '') {
                        $total = number_format($adb->query_result($result, $i, 'total'), 2, '.', '');
                        $balance_facture = number_format($adb->query_result($result, $i, 'balance'), 2, '.', '');
                        $invoice45jours[$i]['invoiceid'] = $adb->query_result($result, $i, 'invoiceid');
                        $invoice45jours[$i]['subject'] = $adb->query_result($result, $i, 'subject');
                        $invoice45jours[$i]['total'] = $total;
                        $invoice45jours[$i]['balance'] = $balance_facture;
                        $invoice45jours[$i]['numero_facture'] = $adb->query_result($result, $i, 'cf_1033');
                        $invoice45jours[$i]['accountname'] = $adb->query_result($result, $i, 'accountname');
                    }
                }
            } else {
                if ($adb->query_result($result, $i, 'invoiceid') != '') {
                    $total = number_format($adb->query_result($result, $i, 'total'), 2, '.', '');
                    $balance_facture = number_format($adb->query_result($result, $i, 'balance'), 2, '.', '');
                    $invoice45jours[$i]['invoiceid'] = $adb->query_result($result, $i, 'invoiceid');
                    $invoice45jours[$i]['subject'] = $adb->query_result($result, $i, 'subject');
                    $invoice45jours[$i]['total'] = $total;
                    $invoice45jours[$i]['balance'] = $balance_facture;
                    $invoice45jours[$i]['numero_facture'] = $adb->query_result($result, $i, 'cf_1033');
                    $invoice45jours[$i]['accountname'] = $adb->query_result($result, $i, 'accountname');
                }
            }
        }
        //var_dump($invoice7jours);
        $invoice45jours['nbreImpayees45Jours'] = count($invoice45jours);
        return $invoice45jours;
    }
     /* uni_cnfsecrm - v2 - modif 147 - FIN */ 
    /* uni_cnfsecrm - v2 - modif 146 - FIN */
}
