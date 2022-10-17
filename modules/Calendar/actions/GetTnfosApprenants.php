<?php

class Calendar_GetTnfosApprenants_Action extends Vtiger_Action_Controller {

    function process(Vtiger_Request $request) {
        global $adb;
        $monfichier = fopen('debug_movadom_getinfos.txt', 'a+');
        fputs($monfichier, "\n" . ' test01 ');
        fclose($monfichier);
        $recordId = $request->get('record');
        $response = new Vtiger_Response();

        $idList = $request->get('idlist');
        $idactivity = $request->get('idactivity');

        $sessionQuery = 'SELECT servicecategory
                FROM vtiger_service 
                INNER JOIN vtiger_activity on vtiger_activity.formation = vtiger_service.serviceid
                WHERE vtiger_activity.activityid = ?';
        $sessionParams = array($idactivity);
        $sessionResult = $adb->pquery($sessionQuery, $sessionParams);

        $categorieformation = $adb->query_result($sessionResult, 0, 'servicecategory');

        if (!$idList) {
            $recordId = $request->get('record');
            $idList = array($recordId);
        }

        foreach ($idList as $id) {
            $recordModel = Vtiger_Record_Model::getInstanceById($id);

            $nomclient[$id] = $this->resolveReferenceLabel($recordModel->get('account_id'), 'Accounts');
            $numclient[$id] = getSingleFieldValue("vtiger_account", "account_no", "accountid", $recordModel->get('account_id'));
            $lastname[$id] = decode_html($recordModel->get('lastname'));
            $firstname[$id] = decode_html($recordModel->get('firstname'));
            $telephone[$id] = decode_html($recordModel->get('phone'));
            $email[$id] = decode_html($recordModel->get('email'));
            $accountid[$id] = $recordModel->get('account_id');

            $sessionAppQuery = 'SELECT resultat,ticket_examen,ticket_examen_test, inscrit,etat,type_tokens,type_tokens_test           
        FROM vtiger_sessionsapprenantsrel
        INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_sessionsapprenantsrel.apprenantid        
        WHERE id=? and apprenantid = ?';
            $sessionAppParams = array($idactivity, $id);
            $sessionAppResult = $adb->pquery($sessionAppQuery, $sessionAppParams);

            $resultat = $adb->query_result($sessionAppResult, 0, 'resultat');
            $ticket_examen = $adb->query_result($sessionAppResult, 0, 'ticket_examen');
            $ticket_examen_test = $adb->query_result($sessionAppResult, 0, 'ticket_examen_test');
            $inscrit = $adb->query_result($sessionAppResult, 0, 'inscrit');
            $etat = $adb->query_result($sessionAppResult, 0, 'etat');
            $type_tokens = $adb->query_result($sessionAppResult, 0, 'type_tokens');
            $type_tokens_test = $adb->query_result($sessionAppResult, 0, 'type_tokens_test');

            $queryStatutFacture = 'SELECT invoiceid,invoicestatus FROM vtiger_invoice WHERE session = ? AND accountid = ?';
            $paramsStatutFacture = array($idactivity, $recordModel->get('account_id'));
            $resultStatutFacture = $adb->pquery($queryStatutFacture, $paramsStatutFacture);
            $statutFacture = $adb->query_result($resultStatutFacture, 0, 'invoicestatus');
            $factureid = $adb->query_result($resultStatutFacture, 0, 'invoiceid');

            if ($factureid > 0) {
                if ($statutFacture == 'Paid') {
                    $statutFacture = 'PAYE';
                } else {
                    $statutFacture = 'NON PAYE';
                }
            } else {
                $statutFacture = 'NON FACTURE';
            }
        }

        foreach ($idList as $id) {
            $resultData = array(
                'id' => $id,
                'name' => $firstname[$id] . " " . $lastname[$id],
                'telephone' => $telephone[$id],
                'email' => $email[$id],
                'numclient' => $numclient[$id],
                'nomclient' => $nomclient[$id]
            );

            $info[] = array($id => $resultData);
        }
        $html = '<table class="table table-bordered lineItemsTable" style="margin-top:15px">
            <thead>
           <tr>';
        if ($categorieformation == 'HABILITATIONS' || $categorieformation == 'AIPR') {
            $colspan = '17';
        } else {
            $colspan = '13';
        }

        $html .= '<th colspan="' . $colspan . '">Liste des Apprenants</th>                 
            </tr>  
            <tr>
                <th class="lineItemBlockHeader">Nom apprenant</th>
                <th class="lineItemBlockHeader">Numéro client</th>
                <th class="lineItemBlockHeader">Nom client</th>
                <th class="lineItemBlockHeader">Téléphone</th>
                <th class="lineItemBlockHeader">Email</th>
                <th class="lineItemBlockHeader">Resultat</th>
                <th class="lineItemBlockHeader">Ticket Examen</th>
                <th class="lineItemBlockHeader">Ticket Examen Test</th>                
                <th class="lineItemBlockHeader">Statut facture</th>
                <th class="lineItemBlockHeader">Inscrit</th>

                <th class="lineItemBlockHeader">Export Attestation</th>                
                <th class="lineItemBlockHeader">Envoi Attestation</th>';

        $html .= '<th class="lineItemBlockHeader">Envoi informations Apprenants</th>';
        if ($categorieformation == 'HABILITATIONS' || $categorieformation == 'AIPR')
            $html .= '    
                <th class="lineItemBlockHeader">Export Avis</th>';

//                <th class="lineItemBlockHeader">Envoi Avis</th>
        $html .= '<th class="lineItemBlockHeader">Envoi Avis & Attestation</th>
                ';

        if ($categorieformation == 'AIPR')
            $html .= '<th class="lineItemBlockHeader">Affecter Token QCM</th>
                <th class="lineItemBlockHeader">Affecter Token Test</th>';

        $html .= '</tr>
            
            </thead>
            <tbody>
                    <tr>
                        <td><a target="blank" href="index.php?module=Contacts&view=Edit&record=' . $id . '">' . $firstname[$id] . " " . $lastname[$id] . '</a></td>
                        <td><a target="blank" href="index.php?module=Accounts&view=Detail&record=' . $accountid[$id] . '">' . $numclient[$id] . '</a></td>
                        <td><a target="blank" href="index.php?module=Accounts&view=Detail&record=' . $accountid[$id] . '">' . $numclient[$id] . '</a></td>
                        <td>' . $telephone[$id] . '</td>
                        <td>' . $email[$id] . '</td>
                        <td>';
        if ($resultat == 'avis_favorable')
            $html .= 'Avis favorable';
        elseif ($resultat == 'avis_defavorable')
            $html .= 'Avis defavorable';
        else
            $html .= 'Autre';
        $html .= '</td>
                        <td>' . $ticket_examen . '</td>
                        <td>' . $ticket_examen_test . '</td>                        
                        <td>' . $statutFacture . '</td>
                        <td>';
        if ($inscrit == '0')
            $html .= 'Non inscrit';
        else
            $html .= 'Inscrit';
        $html .= '</td>
                        <td style="width: 5%;"><a href="index.php?module=Events&action=ExportPDF&record=' . $idactivity . '&appr=' . $id . '&app=SALES&doc=attestation"><i class="fa fa-file-pdf-o exportattes cursorPointer" aria-hidden="true" title="Export Attestation"></i></a></td>                        
                        <td style="width: 5%;"><a href="javascript:Events_Detail_Js.sendEmailPDFClickHandler(\'module=Events&view=SendEmail&mode=composeMailData&record=' . $idactivity . '&appr=' . $id . '&email=' . $email[$id] . '&doc=attestation\')"><i class="fa fa-envelope-square envoiattes cursorPointer" aria-hidden="true" title="Envoi Attestation"></i></a></td>';
        if ($categorieformation == 'HABILITATIONS' || $categorieformation == 'AIPR')
            $html .= '
                        <td style="width: 5%;"><a href="index.php?module=Events&action=ExportPDF&record=' . $idactivity . '&appr=' . $id . '&app=SALES&doc=avis"><i class="fa fa-file-pdf-o exportattes cursorPointer" aria-hidden="true" title="Export Avis"></i></a></td>';

//<td style="width: 5%;"><a href="javascript:Events_Detail_Js.sendEmailPDFClickHandler(\'module=Events&view=SendEmail&mode=composeMailData&record=' . $idactivity . '&appr=' . $id . '&email=' . $email[$id] . '&doc=avis\')"><i class="fa fa-envelope-square envoiattes cursorPointer" aria-hidden="true" title="Envoi Avis"></i></a></td>                    	                        
        $html .= '<td style="width: 5%;"><a href="javascript:Events_Detail_Js.sendEmailPDFClickHandler(\'module=Events&view=SendEmail&mode=composeMailData&record=' . $idactivity . '&appr=' . $id . '&email=' . $email[$id] . '&doc=avisetattestation\')"><i class="fa fa-envelope-square envoiattes cursorPointer" aria-hidden="true" title="Envoi Avis & Attestation"></i></a></td>';

        if ($categorieformation == 'AIPR') {
            $html .= '<td>
                            <select onchange="Calendar_Detail_Js.setTokenApprenant(' . $id . ', ' . $idactivity . ');" name="tokens" id="tokens' . $id . '">
                                <option ';
            if ($type_tokens == 'aucun' || $type_tokens == '')
                $html .= 'selected="selected"';
            $html .= 'value="aucun">Aucun</option> 
                                <option ';
            if ($type_tokens == 'concepteur')
                $html .= 'selected="selected"';
            $html .= 'value="concepteur">Concepteur</option>
                                <option ';
            if ($type_tokens == 'encadrant')
                $html .= 'selected="selected"';
            $html .= 'value="encadrant">Encadrant</option>
                                <option ';
            if ($type_tokens == 'operateur')
                $html .= 'selected="selected"';
            $html .= 'value="operateur">Operateur</option>
                            </select>
                        </td>';
        }

        $html .= '<td>
                            <select onchange="Calendar_Detail_Js.setTokenApprenantTest(' . $id . ', ' . $idactivity . ');" name="tokenstest" id="tokenstest' . $id . '">
                                <option ';
        if ($type_tokens_test == 'aucun' || $type_tokens_test == '')
            $html .= 'selected="selected"';
        $html .= 'value="aucun">Aucun</option> 
                                <option ';
        if ($type_tokens_test == 'concepteur')
            $html .= 'selected="selected"';
        $html .= 'value="concepteur">Concepteur</option>
                                <option ';
        if ($type_tokens_test == 'encadrant')
            $html .= 'selected="selected"';
        $html .= 'value="encadrant">Encadrant</option>
                                <option ';
        if ($type_tokens_test == 'operateur')
            $html .= 'selected="selected"';
        $html .= 'value="operateur">Operateur</option>
                            </select>
                        </td>';

        $html .= '<td><a href="javascript:Events_Detail_Js.sendEmailPDFClickHandler(\'module=Events&amp;view=SendEmail&amp;mode=composeMailData&amp;record=' . $idactivity . '&appr=' . $id . '&doc=elearningdoc\')"><i class="fa fa-envelope-square envoielearning cursorPointer" aria-hidden="true" title="Envoi Information E-learning"></i></a></td>';
        $html .= '</tr>                                  
                            </tbody>
        </table>';
        $response->setResult($html);
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
