<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class QuotesHandler extends VTEventHandler {

    function handleEvent($eventName, $entityData) {
        global $log, $adb;

        if ($eventName == 'vtiger.entity.aftersave') {
            $moduleName = $entityData->getModuleName();
            if ($moduleName == 'Quotes') {

                /* uni_cnfsecrm - mise à jour numéro de devis */
                $quoteid = $entityData->getId();
                $datedevis = $entityData->get('createdtime');
                $quote_no = $entityData->get('quote_no');

                $datedevis = strtotime($datedevis);
                $datedevis = date("Ymd", $datedevis);

                $numero_devis = $quote_no . '-' . $datedevis; //VA
                $sql = "UPDATE vtiger_quotescf SET cf_919=? WHERE quoteid=?";
                $adb->pquery($sql, array($numero_devis, $quoteid));
            }
        }
    }

}
