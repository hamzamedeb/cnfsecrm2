<?php

/* uni_cnfsecrm - modif 101 - FILE */
class Contacts_RappelApprenantRecyclage_Dashboard extends Vtiger_IndexAjax_View {

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
        $filter = $request->get('filterList');
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $data = $moduleModel->getApprenantRecyclage($filter);
        $listViewUrl = $moduleModel->getListViewUrl();
        $widget = Vtiger_Widget_Model::getInstance($linkId, $currentUser->getId());
        $viewer->assign('WIDGET', $widget);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('DATA', $data);
        //Include special script and css needed for this widget
        $viewer->assign('STYLES', $this->getHeaderCss($request));
        $viewer->assign('CURRENTUSER', $currentUser);
        $content = $request->get('content');
        if (!empty($content)) {
            $viewer->view('dashboards/RappelApprenantRecyclageContents.tpl', $moduleName);
        } else {
            $viewer->view('dashboards/RappelApprenantRecyclage.tpl', $moduleName);
        }
    }
}
