<?php

if (!function_exists('pdfmakerGetFormuleQty')) {

    function pdfmakerGetFormuleQty($entityid) {
        global $adb;
        $query = "SELECT id,nbrjours,nbrheures 
                FROM vtiger_inventoryproductrel                 
                WHERE vtiger_inventoryproductrel.id = ?";
        $result = $adb->pquery($query, array($entityid));
        $num_rows_apprenants = $adb->num_rows($result);
        $app = '';
        if ($num_rows_apprenants) {
            for ($i = 0; $i < $num_rows_apprenants; $i++) {
                $nbrjours = $adb->query_result($result, $i, 'nbrjours');
                $nbrheures = $adb->query_result($result, $i, 'nbrheures');
                $naturecalcul = $adb->query_result($result, $i, 'naturecalcul');
                $listprice = ucwords(strtolower(($adb->query_result($result, $i, 'listprice'))));
                switch ($naturecalcul) {
                    case 'jour':
                        $prix_formation = $listprice * $nbrjours;
                        $quantite_affiche = $nbrjours;
                        break;

                    case 'heure':
                        $prix_formation = $listprice * $nbrheures;
                        $quantite_affiche = $nbrheures;
                        break;

                    case 'forfait':
                        $prix_formation = $listprice;
                        $quantite_affiche = 1;
                        break;
                    default :
                        $prix_formation = $listprice;
                        $quantite_affiche = 1;
                        break;
                }
            }
        }

        return $quantite_affiche;
    }

}
