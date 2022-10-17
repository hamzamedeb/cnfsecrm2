<?php
//unicnfsecrm_mod_56
/* uni_cnfsecrm - v2 - modif 142 - DEBUT */
require_once('modules/HistoryTokens/HistoryTokens.php');
/* uni_cnfsecrm - v2 - modif 142 - FIN */
class Calendar_ModifierTokensTest_Action extends Vtiger_Action_Controller {

    function process(Vtiger_Request $request) {
        global $adb;
        $apprenantId = $request->get('recordId');
        $typeTokens = $request->get('typeTokens');
        $idSession = $request->get('idSession');
        $response = new Vtiger_Response();

        $queryDetailApprenant = "SELECT firstname,lastname FROM vtiger_contactdetails WHERE contactid = ? ";
        $result = $adb->pquery($queryDetailApprenant, array($apprenantId));

        $nomApprenant = $adb->query_result($result, 0, 'firstname');
        $prenomApprenant = $adb->query_result($result, 0, 'lastname');

        $queryAncienToken = "SELECT type_tokens_test FROM vtiger_sessionsapprenantsrel WHERE id = ? and apprenantid = ? ";
        $resultAncienToken = $adb->pquery($queryAncienToken, array($idSession, $apprenantId));
        $typeAncienToken = $adb->query_result($resultAncienToken, 0, 'type_tokens_test');        

        if ($typeTokens == 'aucun') {
//            $updateAucun = "UPDATE tokens_" . $typeAncienToken . " SET nomapprenant=?,prenomapprenant=?,sessionid=?,apprenantid=? WHERE apprenantid = ? and sessionid = ?";
//            $paramsAucun = array('', '', '', '', $apprenantId, $idSession);
//            $resultAucun = $adb->pquery($updateAucun, $paramsAucun);
//            
//            $updateRelAucun = "UPDATE vtiger_sessionsapprenantsrel SET type_tokens_test=?,ticket_examen_test=? WHERE id=? and apprenantid=?";
//            $paramsRelAucun = array('', '', $idSession, $apprenantId);
//            $resultRelAucun = $adb->pquery($updateRelAucun, $paramsRelAucun);
        } else {
//            if ($typeAncienToken != '' && $ancienToken != '') {
//                $updateAncienToken = "UPDATE tokens_" . $typeAncienToken . " SET nomapprenant=?,prenomapprenant=?,sessionid=?,apprenantid=? WHERE apprenantid = ? and sessionid = ?";
//                $paramsAncienToken = array('', '', '', '', $apprenantId, $idSession);
//                $resultAncienToken = $adb->pquery($updateAncienToken, $paramsAncienToken);
//            }

            $queryIdTokenVide = "SELECT id,token FROM tokens_" . $typeTokens."_test" . " WHERE nomapprenant = ? or prenomapprenant = ? LIMIT 1 ";
            $result = $adb->pquery($queryIdTokenVide, array("", ""));
            $idTokens = $adb->query_result($result, 0, 'id');
            $token = $adb->query_result($result, 0, 'token');

            $updateToken = "UPDATE tokens_" . $typeTokens."_test" . " SET nomapprenant=?,prenomapprenant=?,sessionid=?,apprenantid=? WHERE id=?";
            $paramsToken = array(html_entity_decode($nomApprenant),html_entity_decode($prenomApprenant) , $idSession, $apprenantId, $idTokens);
            $resultToken = $adb->pquery($updateToken, $paramsToken);

            $updateRel = "UPDATE vtiger_sessionsapprenantsrel SET type_tokens_test=?,ticket_examen_test=? WHERE id=? and apprenantid=?";
            $paramsRel = array($typeTokens, $token, $idSession, $apprenantId);
            $resultRel = $adb->pquery($updateRel, $paramsRel);
            
            /* uni_cnfsecrm - v2 - modif 142 - DEBUT */
            if ($typeTokens == "concepteur") {
                $typeTokens = "Concepteur";
            } else if ($typeTokens == "encadrant") {
                $typeTokens = "Encadrant";
            } else if ($typeTokens == "operateur") {
                $typeTokens = "Operateur";
            }
            $focus = new HistoryTokens();
            $focus->mode = 'create';
            $focus->column_fields['name'] = $token;
            $focus->column_fields['session'] = $idSession;
            $focus->column_fields['apprenant'] = $apprenantId;
            $focus->column_fields['cf_1303'] = $typeTokens;
            $focus->column_fields['cf_1305'] = "TEST";
            $focus->save("HistoryTokens");
            /* uni_cnfsecrm - v2 - modif 142 - FIN */
        }

        $reponse = 'ok';



        $response->setResult($reponse);
        $response->emit();
    }

    function checkPermission(Vtiger_Request $request) {
        return;
    }

    function resolveReferenceLabel($id, $module = false) {
        if (empty($id)) {
            return '';
        }
        if ($module === false) {
            $module = getSalesEntityType($id);
        }
        $label = getEntityName($module, array($id));
        return decode_html($label[$id]);
    }

}
