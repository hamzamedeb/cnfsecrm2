<?php
/* * *******************************************************************************
 * The content of this file is subject to the Cashflow 4 You license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 * ****************************************************************************** */

class Cashflow4You_ListPayments_View extends Vtiger_BasicAjax_View {
    
    var $db;
    
    public function process(Vtiger_Request $request) {

        $this->db = PearDatabase::getInstance();
        $viewer = $this->getViewer($request);
        $current_user = Users_Record_Model::getCurrentUserModel();

        if(!isset($_REQUEST['loadPayments']))
                $_REQUEST['loadPayments'] = '';

        $relation_module = $_REQUEST['source_module'];
        if((!isset($relation_module) || $relation_module=='') && isset($_REQUEST['record']) && $_REQUEST['record']!=''){
            $res = $this->db->pquery("SELECT setype FROM vtiger_crmentity WHERE crmid=?",array($_REQUEST['record']));
            $row = $this->db->fetchByAssoc($res);
            $relation_module = $row['setype'];
        }
        switch($relation_module){
            case "Invoice":
                $moduletable = 'vtiger_invoice';
                $moduleid = 'invoiceid';
                $module_no = 'invoice_no';
                $module_am = 'invoice_am';

                $so_moduletable = 'vtiger_salesorder';
                $so_moduleid = 'salesorderid';
                $so_module_no = 'salesorder_no';
                $so_module_am = 'so_am';
                break;
            case "PurchaseOrder":
                $moduletable = 'vtiger_purchaseorder';
                $moduleid = 'purchaseorderid';
                $module_no = 'purchaseorder_no';
                $module_am = 'po_am';
                break;
            case "SalesOrder":
                $moduletable = 'vtiger_salesorder';
                $moduleid = 'salesorderid';
                $module_no = 'salesorder_no';
                $module_am = 'so_am';
                break;
            default:
            	$focus = CRMEntity::getInstance($relation_module);
            	if ($focus) {
					$moduletable = $focus->table_name;
					$moduleid = $focus->table_index;
					$result = $this->db->pquery('SELECT columnname module_no FROM vtiger_field WHERE uitype=4 and tabid=?', array(getTabid($relation_module)));
					if ($result) {
						$row = $this->db->fetchByAssoc($result, 0);
						$module_no = $row['module_no'];
					}
				}
            	break;
        }

        if(isset($moduletable)){
            $go_more = $this->db->getOne("SELECT $moduleid 
                                    FROM $moduletable
                                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=$moduletable.$moduleid
                                    WHERE deleted=0 
                                    AND p_open_amount>=0 
                                    AND $moduletable.$moduleid=".$_REQUEST['record'],0,$moduleid);
        } else {
            $go_more = ' ';
        }
        $viewer->assign('GO_MORE', $go_more);
        $viewer->assign('RECORD', $_REQUEST['record']);
        $viewer->assign('RELATION_MODULE', $relation_module);
        $viewer->assign('LOAD_PAYMENTS', $_REQUEST['loadPayments']);
        $viewer->assign('PAYTYPE', $_REQUEST['paytype']);


        if( $relation_module == "SalesOrder" ) {
          $query = "SELECT vtiger_crmentity.*, its4you_cashflow4you.*, $moduletable.$module_no AS relation_no, $moduletable.total as relation_am,
                    vtiger_currency_info.currency_symbol 
                    FROM its4you_cashflow4you 
                    INNER JOIN vtiger_crmentityrel ON its4you_cashflow4you.cashflow4youid=vtiger_crmentityrel.crmid
                    INNER JOIN $moduletable ON $moduletable.$moduleid=vtiger_crmentityrel.relcrmid
                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=its4you_cashflow4you.cashflow4youid
                    INNER JOIN vtiger_currency_info ON vtiger_currency_info.id = its4you_cashflow4you.currency_id 
                    WHERE vtiger_crmentity.deleted=0
                    AND vtiger_crmentityrel.relcrmid=?
                    ORDER BY paymentdate, cashflow4youid  ASC";
          $result1 = $this->db->pquery($query, Array($_REQUEST['record']));
        } else {
          $query = "SELECT vtiger_crmentity.*, its4you_cashflow4you.*, $moduletable.$module_no AS relation_no, $moduletable.total as relation_am,
                    its4you_cashflow4you_associatedto.partial_amount, vtiger_currency_info.currency_symbol 
                    FROM its4you_cashflow4you 
                    INNER JOIN vtiger_crmentityrel ON its4you_cashflow4you.cashflow4youid=vtiger_crmentityrel.crmid
                    INNER JOIN $moduletable ON $moduletable.$moduleid=vtiger_crmentityrel.relcrmid
                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=its4you_cashflow4you.cashflow4youid
                    INNER JOIN its4you_cashflow4you_associatedto ON its4you_cashflow4you_associatedto.cashflow4youid=its4you_cashflow4you.cashflow4youid  
                    INNER JOIN vtiger_currency_info ON vtiger_currency_info.id = its4you_cashflow4you.currency_id 
                    WHERE vtiger_crmentity.deleted=0
                    AND vtiger_crmentityrel.relcrmid=?
                    AND its4you_cashflow4you_associatedto.cashflow4you_associated_id=?
                    ORDER BY paymentdate, cashflow4youid ASC";
          
          $result1 = $this->db->pquery($query, Array($_REQUEST['record'], $_REQUEST['record']));
        }
        if(isset($_REQUEST['paytype']) && $_REQUEST['paytype']=='loadPayments' ){

        }else{
            $Payments = Array();
            $total = 0;
            $i = 1;
            while($row1 = $this->db->fetchByAssoc($result1)) {
                $Payments[ $row1['cashflow4youid'] ]['paymentstatus'] = $row1['cashflow4you_status'];
                $Payments[ $row1['cashflow4youid'] ]['no'] = $i++;
                if( $row1['paymentdate'] != "") {
                    $Payments[ $row1['cashflow4youid'] ]['paymentdate'] = getValidDisplayDate($row1['paymentdate']);
                } else {
                    $Payments[ $row1['cashflow4youid'] ]['paymentdate'] = "";
                }

                if( $relation_module == "SalesOrder" ) {
                    $amount = $row1['paymentamount'];
                } else {
                    $amount = $row1['partial_amount'];
                }
                $Payments[ $row1['cashflow4youid'] ]['amount'] = CurrencyField::appendCurrencySymbol(CurrencyField::convertToUserFormat($amount, $current_user, true),$row1['currency_symbol']);
                $total += $amount;
            }
            $viewer->assign('PAYMENTS', $Payments);

            
            $select_inv = "SELECT ".$moduletable.".total, vtiger_currency_info.currency_symbol FROM ".$moduletable."
                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=".$moduletable.".".$moduleid."
                    INNER JOIN vtiger_currency_info ON vtiger_currency_info.id = ".$moduletable.".currency_id 
                    WHERE vtiger_crmentity.deleted=0
                    AND ".$moduletable.".".$moduleid."=?";
            $result_inv = $this->db->pquery($select_inv, Array($_REQUEST['record']));
            $grand_total = $this->db->query_result($result_inv, 0, 'total');
            $total_curr = $this->db->query_result($result_inv, 0, 'currency_symbol');

            $viewer->assign('TOTAL', CurrencyField::appendCurrencySymbol(CurrencyField::convertToUserFormat($total, $current_user, true),$total_curr));

            $viewer->assign('TOTAL_CURRENCY', $total_curr);
            $viewer->assign('GRAND_TOTAL', CurrencyField::appendCurrencySymbol(CurrencyField::convertToUserFormat($grand_total, $current_user, true),$total_curr));
            $viewer->assign('TOTAL_BALLANCE', CurrencyField::appendCurrencySymbol(CurrencyField::convertToUserFormat(abs($grand_total) - $total, $current_user, true),$total_curr));
        }
        $viewer->assign('CASHFLOW4YOU_MOD', return_module_language($currentLanguage, "Cashflow4You"));

        if(isset($_REQUEST['mode']) && $_REQUEST['mode']!='edit' && isset($_REQUEST['paytype']) && $_REQUEST['paytype']=='loadPayments'){
            echo "::||@#@||::";
            echo number_format($to_pay, 2, '.', '');
        }

        $viewer->view("Cashflow4YouActions.tpl", 'Cashflow4You');
    }
}