<?php

/* * *******************************************************************************
 * The content of this file is subject to the Cashflow4You license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 * ****************************************************************************** */

class Cashflow4You_Cashflow4YouActions_View extends Vtiger_BasicAjax_View {
    
    var $db;
    
    public function process(Vtiger_Request $request) {
        
        $this->db = PearDatabase::getInstance();
        $viewer = $this->getViewer($request);
        $current_user = Users_Record_Model::getCurrentUserModel();
        $utils = new Cashflow4You_Utils_Model();

        $record = $request->get('record');
        $relation_module = $request->get('source_module');

        if(empty($relation_module) && !empty($record)){
            $relation_module = $utils->getModuleById($record);
        }
        
        $focus = CRMEntity::getInstance($relation_module);
        $moduletable = $focus->table_name;
        $moduleid = $focus->table_index;
        $module_no = strtolower($relation_module)."_no";
        $total_fld = 'total';
        if($relation_module == "Potentials") {
           $module_no = "potential_no";
           $total_fld = 'amount';
        } else if( $relation_module == "ITS4YouPreInvoice") {
            $module_no = "preinvoice_no";
        }
                
        if(isset($moduletable)){
            $go_more = $this->db->getOne("SELECT $moduleid 
                                    FROM $moduletable
                                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=$moduletable.$moduleid
                                    WHERE deleted=0 
                                    AND p_open_amount>=0 
                                    AND $moduletable.$moduleid=".$record,0,$moduleid);
        } else {
            $go_more = ' ';
        }
        $viewer->assign('GO_MORE', $go_more);
        $viewer->assign('RECORD', $record);
        $viewer->assign('RELATION_MODULE', $relation_module);
        $viewer->assign('LOAD_PAYMENTS', $request->get('loadPayments'));

        $paytype = $request->get('paytype');
        $viewer->assign('PAYTYPE', $paytype);
        
        if( $relation_module == "Potentials" ) {
            $select_inv = "SELECT ".$moduletable.".".$total_fld." AS total FROM ".$moduletable."
                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=".$moduletable.".".$moduleid."
                    WHERE vtiger_crmentity.deleted=0
                    AND ".$moduletable.".".$moduleid."=?";
            $result_inv = $this->db->pquery($select_inv, Array($record));
            $grand_total = $this->db->query_result($result_inv, 0, 'total');
            $grand_total_show = CurrencyField::convertToUserFormat($grand_total, $current_user, false);

            $select_inv = "SELECT currency_symbol FROM vtiger_currency_info WHERE id=?";
            $result_inv = $this->db->pquery($select_inv, Array($current_user->currency_id));

            $gtotal_curr = $this->db->query_result($result_inv, 0, 'currency_symbol');
            $total_curr = $gtotal_curr;
            $currency_id = 1;
            $currency_rate = 1;
        } else {
            $select_inv = "SELECT ".$moduletable.".".$total_fld." AS total, vtiger_currency_info.currency_symbol, ".$moduletable.".currency_id "
                    . "FROM ".$moduletable."
                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=".$moduletable.".".$moduleid."
                    INNER JOIN vtiger_currency_info ON vtiger_currency_info.id = ".$moduletable.".currency_id 
                    WHERE vtiger_crmentity.deleted=0
                    AND ".$moduletable.".".$moduleid."=?";
            $result_inv = $this->db->pquery($select_inv, Array($record));
            $grand_total = abs($this->db->query_result($result_inv, 0, 'total'));
            $grand_total_show = CurrencyField::convertToUserFormat($grand_total, $current_user, true);
            
            $gtotal_curr = $this->db->query_result($result_inv, 0, 'currency_symbol');

            $total_curr = $gtotal_curr;
            $currency_id = $this->db->query_result($result_inv, 0, 'currency_id');
            $currencyRateAndSymbol = getCurrencySymbolandCRate($currency_id);
            $currency_rate =  $currencyRateAndSymbol['rate'];
        }
            
        if( $relation_module == "SalesOrder" || $relation_module == "ITS4YouPreInvoice") {
          $query = "SELECT vtiger_crmentity.*, its4you_cashflow4you.*, $moduletable.$module_no AS relation_no, $moduletable.$total_fld as relation_am,
                    vtiger_currency_info.currency_symbol 
                    FROM its4you_cashflow4you 
                    INNER JOIN vtiger_crmentityrel ON its4you_cashflow4you.cashflow4youid=vtiger_crmentityrel.crmid
                    INNER JOIN $moduletable ON $moduletable.$moduleid=vtiger_crmentityrel.relcrmid
                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=its4you_cashflow4you.cashflow4youid
                    INNER JOIN vtiger_currency_info ON vtiger_currency_info.id = its4you_cashflow4you.currency_id 
                    WHERE vtiger_crmentity.deleted=0
                    AND vtiger_crmentityrel.relcrmid=?
                    ORDER BY paymentdate, cashflow4youid  ASC";
          $result1 = $this->db->pquery($query, Array($record));
        } else {
          $query = "SELECT vtiger_crmentity.*, its4you_cashflow4you.*, $moduletable.$module_no AS relation_no, $moduletable.$total_fld as relation_am,
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
          
          $result1 = $this->db->pquery($query, Array($record, $record));
        }
        if($paytype!='loadPayments' ){
            $Payments = Array();
            $total = 0;
            $i = 1;
            while($row1 = $this->db->fetchByAssoc($result1)) {
                $setype = $utils->getModuleById($row1['relationid']);
                $Payments[ $row1['cashflow4youid'] ]['paymentstatus'] = $row1['cashflow4you_status'];
                $Payments[ $row1['cashflow4youid'] ]['currency'] = $row1['currency_symbol'];
                $Payments[ $row1['cashflow4youid'] ]['no'] = $i++;
                if( $row1['paymentdate'] != "") {
                    $Payments[ $row1['cashflow4youid'] ]['paymentdate'] = getValidDisplayDate($row1['paymentdate']);
                } else {
                    $Payments[ $row1['cashflow4youid'] ]['paymentdate'] = "";
                }
                if( $relation_module == "SalesOrder" || $relation_module == "ITS4YouPreInvoice" ) {
                    $Payments[ $row1['cashflow4youid'] ]['paymentamount'] = CurrencyField::convertToUserFormat($row1['paymentamount'], $current_user, true);
                    $partial_amount = $row1['paymentamount'];
                } else {
                    $Payments[ $row1['cashflow4youid'] ]['paymentamount'] = CurrencyField::convertToUserFormat($row1['partial_amount'], $current_user, true);
                    if( $relation_module == "Potentials" ) {
                        if( $row1['currency_id'] != $current_user->currency_id ) {
                            $currencyRateAndSymbol = getCurrencySymbolandCRate($row1['currency_id']);
                            if( CurrencyField::getDBCurrencyId() != $row1['currency_id'] ) {
                                $tmp_total = CurrencyField::convertToDollar($row1['partial_amount'], $currencyRateAndSymbol['rate']);
                                $partial_amount = CurrencyField::convertToDollar($tmp_total, $current_user->conv_rate);
                            } else {
                                $tmp_total = CurrencyField::convertFromDollar($row1['partial_amount'], $currencyRateAndSymbol['rate']);
                                $partial_amount = CurrencyField::convertFromDollar($tmp_total, $current_user->conv_rate);
                            }
                        } else {
                            $partial_amount = $row1['partial_amount'];
                        }
                    } else {
                        if( $row1['currency_id'] == $currency_id ) {
                            $partial_amount = $row1['partial_amount'];
                        } else {
                            $rate = $row1['paymentamount']/$row1['payamount_main'];
                            $partial_amount = CurrencyField::convertToDollar($row1['partial_amount'], $rate); 
                            $partial_amount = CurrencyField::convertFromMasterCurrency($partial_amount,$currency_rate);
                        }
                    }
                }
                if( $row1['cashflow4you_paytype'] == "Incoming" ) {
                    $total += abs( $partial_amount );
                } else {
                    $total -= abs( $partial_amount );
                }
            }
            $viewer->assign('PAYMENTS', $Payments);
          
            if( $setype == "PurchaseOrder" || $setype == "CreditNotes4You") {
                $total *= -1;
            }
            $viewer->assign('TOTAL', CurrencyField::convertToUserFormat($total, $current_user, true));

            if( $relation_module == "Potentials") {
                $balance_total_show = CurrencyField::convertToUserFormat($grand_total - $total, $current_user, false);
            } else {
                $balance_total_show = CurrencyField::convertToUserFormat($grand_total - $total, $current_user, true);
            }
            
            $viewer->assign('GTOTAL_CURRENCY', $gtotal_curr);
            $viewer->assign('TOTAL_CURRENCY', $total_curr != "" ? $total_curr : $gtotal_curr);
            $viewer->assign('GRAND_TOTAL', $grand_total_show);
            $viewer->assign('TOTAL_BALLANCE', $balance_total_show);
        }   
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $currentLanguage = $currentUser->get('language');
        $viewer->assign('CASHFLOW4YOU_MOD', return_module_language($currentLanguage, "Cashflow4You"));

        $viewer->view("Cashflow4YouActions.tpl", 'Cashflow4You');
    }
}    