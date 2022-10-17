<?php

/* uni_cnfsecrm - v2 - modif 107 - FILE */
class Contacts_ApprenantSansSession_Dashboard extends Vtiger_IndexAjax_View {

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
        $filter = $request->get('filterList');
        $data = $moduleModel->getApprenantSansSession($filter);
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
            $viewer->view('dashboards/ApprenantSansSessionContents.tpl', $moduleName);
        } else {
            $viewer->view('dashboards/ApprenantSansSession.tpl', $moduleName);
        }
    }

}
