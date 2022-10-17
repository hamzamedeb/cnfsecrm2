<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Livraison_GetTaxesBycodeBarre_Action extends Vtiger_Action_Controller {

    function process(Vtiger_Request $request) {
        global $adb;
        $codebarre = $request->get('codebarre');
        $monfichier = fopen('articlesdebug.txt', 'a+');
        fputs($monfichier, "\n" . ' codebarre ' . $codebarre);
        fclose($monfichier);
//        $recordId = getSingleFieldValue("vtiger_articles", "product", "articlesid", $articlesId);
//        $monfichier = fopen('articlesdebug.txt', 'a+');
//        fputs($monfichier, "\n" . ' recordId ' . $recordId);
//        fclose($monfichier);
        $productid = getSingleFieldValue("vtiger_products", "productid", "codebarre", $codebarre);
        $productname = getSingleFieldValue("vtiger_products", "productname", "codebarre", $codebarre);


        $requete_products = "select productid,productname from vtiger_products
    inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_products.productid
    where codebarre = ?";
        $result_products = $adb->pquery($requete_products, array($codebarre));
        $num_rows_products = $adb->num_rows($result_products);

        $response = new Vtiger_Response();
        if ($num_rows_products > 0) {
            $productname = html_entity_decode($productname);
            $monfichier = fopen('articlesdebug.txt', 'a+');
            fputs($monfichier, "\n" . ' productid ' . $productid);
            fputs($monfichier, "\n" . ' productname ' . $productname);
            fclose($monfichier);

            $idList = $request->get('idlist');
            if (!$idList) {
                $idList = array($productid);
            }

            foreach ($idList as $id) {
                $resultData = array(
                    'id' => $id,
                    'nomproduit' => $productname,
                );

                $info[] = array($id => $resultData);
            }
        } else {
            $info[] = array(0 => array(
                    'id' => 0,
                    'nomproduit' => 'PRODUIT_INDISPONIBLE',
            ));
        }
        $response->setResult($info);
        $response->emit();
    }

}
