<?php

/* uni_cnfsecrm - v2 - modif 146 - FILE */

class Invoice_Impayes45Jours_Dashboard extends Vtiger_IndexAjax_View {

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
        /* uni_cnfsecrm - v2 - modif 147 - DEBUT */
        $filtreRappel = $request->get('filterList');
        /* uni_cnfsecrm - v2 - modif 147 - FIN */
        $data = $moduleModel->getImpayees45Jours($filtreRappel);
        $listViewUrl = $moduleModel->getListViewUrl();
        $widget = Vtiger_Widget_Model::getInstance($linkId, $currentUser->getId());
        $viewer->assign('WIDGET', $widget);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('DATA', $data);
        //Include special script and css needed for this widget
        $viewer->assign('STYLES', $this->getHeaderCss($request));
        $viewer->assign('CURRENTUSER', $currentUser);
        $content = $request->get('content');
        /** wajprestige */
        if (!empty($content)) {
            $viewer->view('dashboards/Impayes45JoursContents.tpl', $moduleName);
        } else {
            $viewer->view('dashboards/Impayes45Jours.tpl', $moduleName);
        }
    }

}
