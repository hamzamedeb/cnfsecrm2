<?php

//unicnfsecrm_mod_16
class Invoice_Impayés14Jours_Dashboard extends Vtiger_IndexAjax_View {

    function getSearchParams($assignedto, $activitytype) {
        $listSearchParams = array();
        $conditions = array(array('assigned_user_id', 'e', $assignedto), array("activitytype", "e", $activitytype));
        $listSearchParams[] = $conditions;
        return '&search_params=' . json_encode($listSearchParams);
    }

    public function process(Vtiger_Request $request) {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $linkId = $request->get('linkid');

        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        /* uni_cnfsecrm - modif 103 - DEBUT */
        $filtreRappel = $request->get('filterList');
        $data = $moduleModel->getInvoiceByEcheance('Dépassé de 14 jours', $filtreRappel);
        /* uni_cnfsecrm - modif 103 - FIN */
        $listViewUrl = $moduleModel->getListViewUrl();
        $widget = Vtiger_Widget_Model::getInstance($linkId, $currentUser->getId());
        $viewer->assign('WIDGET', $widget);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('DATA', $data);
        $nbre_relance = $moduleModel->getNbrFactureNonRelance('Dépassé de 14 jours');
        $viewer->assign('NBR_RELANCE', $nbre_relance);
        //Include special script and css needed for this widget
        $viewer->assign('STYLES', $this->getHeaderCss($request));
        $viewer->assign('CURRENTUSER', $currentUser);
        $content = $request->get('content');
        if (!empty($content)) {
            $viewer->view('dashboards/Impayés14JoursContents.tpl', $moduleName);
        } else {
            $viewer->view('dashboards/Impayés14Jours.tpl', $moduleName);
        }
    }

}
