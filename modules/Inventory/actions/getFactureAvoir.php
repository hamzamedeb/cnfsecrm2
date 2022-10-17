<?php
/* uni_cnfsecrm - v2 - modif 175 - FILE */
class Inventory_getFactureAvoir_Action extends Vtiger_Action_Controller {

    function process(Vtiger_Request $request) {
        $recordId = $request->get('record');
        $num_facture_parent = getSingleFieldValue("vtiger_invoicecf", 'cf_1039', 'invoiceid', $recordId);
        
        if (!empty($num_facture_parent)) {
            $info = ['result' => 0, 'message' => 'facture avoir'];
        } else {
            $invoice_no = getSingleFieldValue("vtiger_invoicecf", 'cf_1033', "invoiceid", $recordId);
            $num_facture_parent = getSingleFieldValue("vtiger_invoicecf", 'invoiceid', "cf_1039", $invoice_no);
            if (!empty($num_facture_parent)) {
                $info = ['result' => 0, 'message' => 'facture a un avoir'];
            } else {
                $info = ['result' => 1, 'message' => 'facture n\' a pas un avoir'];
            }
        }

        $response = new Vtiger_Response();

        $response->setResult($info);
        $response->emit();
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
