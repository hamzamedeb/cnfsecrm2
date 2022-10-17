<?php

class Inventory_GetJournee_Action extends Vtiger_Action_Controller {

    function process(Vtiger_Request $request) {
        global $adb;
        $recordId = $request->get('record');
        $response = new Vtiger_Response();
        //modif

        $journee = array();
        $id = $request->get('record');
        $query = "SELECT date_start,start_matin,end_matin,start_apresmidi,end_apresmidi,duree_formation
                FROM vtiger_sessionsdatesrel 
                WHERE vtiger_sessionsdatesrel.id = ? ";
        $result = $adb->pquery($query, array($id));
        $num_rows_journee = $adb->num_rows($result);
        if ($num_rows_journee) {
            for ($i = 0; $i < $num_rows_journee; $i++) {
                $date_start = $adb->query_result($result, $i, 'date_start');
                $start_matin = $adb->query_result($result, $i, 'start_matin');
                $end_matin = $adb->query_result($result, $i, 'end_matin');
                $start_apresmidi = $adb->query_result($result, $i, 'start_apresmidi');
                $end_apresmidi = $adb->query_result($result, $i, 'end_apresmidi');
                $duree_formation = $adb->query_result($result, $i, 'duree_formation');

                $journee[$i]['date_start'] = $date_start;
                $journee[$i]['start_matin'] = $start_matin;
                $journee[$i]['end_matin'] = $end_matin;
                $journee[$i]['start_apresmidi'] = $start_apresmidi;
                $journee[$i]['end_apresmidi'] = $end_apresmidi;
                $journee[$i]['duree_formation'] = $duree_formation;
            }
            $journee['count'] = $num_rows_journee;
        }
      
//        $monfichier = fopen('debug_data.txt', 'a+');
//        fputs($monfichier, "\n" . ' test01 ' . $journee[0]['end_apresmidi']);
//        fclose($monfichier);
        //fin modif
        
        $response->setResult($journee);
        $response->emit();
    }

    

}
