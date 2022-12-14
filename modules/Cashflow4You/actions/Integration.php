<?php
/* * *******************************************************************************
 * The content of this file is subject to the Cashflow 4 You license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 * ****************************************************************************** */

class Cashflow4You_Integration_Action extends Vtiger_Save_Action {

    var $db;
    var $Modules = ['Invoice' => 'vtiger_invoice', 'SalesOrder' => 'vtiger_salesorder', 'PurchaseOrder' => 'vtiger_purchaseorder',
        'Potentials' => 'vtiger_potential', 'ITS4YouPreInvoice' => 'its4you_preinvoice', 'CreditNotes4You' => 'vtiger_creditnotes4you'];

    var $Modules2 = ['Vendors' => 'vtiger_vendor', 'Contacts' => 'vtiger_contactdetails', 'Accounts' => 'vtiger_account',];


	public function checkPermission(Vtiger_Request $request) {
		$layout = Vtiger_Viewer::getDefaultLayoutName();
		
		if ('v7' === $layout) {
			$currentUser = Users_Record_Model::getCurrentUserModel();
	
			if (!$currentUser->isAdminUser()) {
				throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
			}

		} else {
			parent::checkPermission($request);
		}
	}
	
    function __construct() {
        $this->db = PearDatabase::getInstance();
        parent::__construct();
    }

    function process(Vtiger_Request $request) {
        $mode = $request->get('mode');
        foreach ($this->Modules AS $module_name => $module_table) {
            $chx_module = $request->get('chx_' . $module_name) == "on" ? 1 : 0;
            if ($chx_module != $request->get('module_' . $module_name)) {
                if ($chx_module == 1) {
                    $this->CFintegrate($module_name);
                } else if ($chx_module == 0) {
                    $this->CFdisintegrate($module_name);
                }
            }
        }

        $sql = "SELECT name FROM `vtiger_tab` "
            . "INNER JOIN vtiger_links ON vtiger_links.tabid=vtiger_tab.tabid "
            . "WHERE `linkurl` LIKE '%cashflow4you%' AND linktype != 'HEADERSCRIPT'";
        $result = $this->db->pquery($sql, []);
        $module = $this->db->num_rows($result);
        foreach ($this->Modules2 AS $module_name => $module_table) {
            if ($module > 0 && $this->checkCFIntegration($module_name) == 0) {
                $this->CFintegrate($module_name);
            } else if ($module <= 0) {
                $this->CFdisintegrate($module_name);
            }
        }
        header('Location: index.php?module=Cashflow4You&view=Integration');
    }

    function CFintegrate($module) {

        $instance_nameCashflow = Vtiger_Module::getInstance('Cashflow4You');
        $Cashflow4YouLabel = 'Cashflow4You';
        $r_actions = [];

        if ($module != "Contacts") {
            $this->insertRelationModule($module);
        }
        if ($module != "Vendors" && $module != "Contacts" && $module != "Accounts") {
            $this->showCashflowInformation($this->Modules[$module]);
        }

        $link_module = Vtiger_Module::getInstance($module);
        if ($module == "Invoice" || $module == "SalesOrder" || $module == "PurchaseOrder") {
            $link_module->setRelatedList($instance_nameCashflow, $Cashflow4YouLabel, $r_actions, 'get_related_list');
        } else {
            $link_module->setRelatedList($instance_nameCashflow, $Cashflow4YouLabel, $r_actions, 'get_dependents_list');
        }

        if ($module != "Vendors" && $module != "Contacts" && $module != "Accounts") {
            $rec = "RECORD";
            $mod = "MODULE";
            $inv_module = Vtiger_Module::getInstance($module);
            $inv_module->addLink("DETAILVIEW", "Add Payment", "index.php?module=Cashflow4You&view=Edit&relationid=$$rec$&sourceModule=$$mod$&relationOperation=1&sourceRecord=$$rec$", "");
            
            $inv_module->addLink("DETAILVIEW", "Payments", "javascript:Cashflow4You_Actions_Js.showPayments(this);");
        }
        if ($module == "Invoice") {
            $inv_module->addLink("LISTVIEWBASIC", "Create Payment", 'javascript:Cashflow4You_Actions_Js.CreatePayment("index.php?module=Cashflow4You&view=CreatePaymentActionAjax&mode=showCreatePaymentForm");');
        }
    }

    function CFdisintegrate($module) {
        $instance_nameCashflow = Vtiger_Module::getInstance("Cashflow4You");
        $Cashflow4YouLabel = "Cashflow4You";

        if ($module != "Contacts") {
            $this->deleteRelationModule($module);
        }
        if ($module != "Vendors" && $module != "Contacts" && $module != "Accounts") {
            $this->hideCashflowInformation($this->Modules[$module]);
        }

        $link_module = Vtiger_Module::getInstance($module);
        if ($module == "Invoice" || $module == "SalesOrder" || $module == "PurchaseOrder") {
            $link_module->unsetRelatedList($instance_nameCashflow, $Cashflow4YouLabel, "get_related_list");
        } else {
            $link_module->unsetRelatedList($instance_nameCashflow, $Cashflow4YouLabel, "get_dependents_list");
        }

        if ($module != "Vendors" && $module != "Contacts" && $module != "Accounts") {
            $rec = "RECORD";
            $mod = "MODULE";
            $inv_module = Vtiger_Module::getInstance($module);
            $inv_module->deleteLink("DETAILVIEW", "Add Payment", "index.php?module=Cashflow4You&view=Edit&relationid=$$rec$&sourceModule=$$mod$&relationOperation=1&sourceRecord=$$rec$");
            
            $inv_module->deleteLink("DETAILVIEW", "Payments", "javascript:Cashflow4You_Actions_Js.showPayments(this);");
        }
        if ($module == "Invoice") {
            $inv_module->deleteLink("LISTVIEWMASSACTION", "Create Payment", 'javascript:Cashflow4You_Actions_Js.CreatePayment("index.php?module=Cashflow4You&view=CreatePaymentActionAjax&mode=showCreatePaymentForm");');
        }
    }

    function insertRelationModule($module) {
        if ($module == "Invoice" || $module == "SalesOrder" || $module == "PurchaseOrder" || $module == "Potentials" || $module == "ITS4YouPreInvoice" || $module == "CreditNotes4You") {
            $sql = "SELECT fieldid FROM  vtiger_field 
                WHERE tablename = 'its4you_cashflow4you' AND columnname = 'relationid'";
        } else {
            $sql = "SELECT fieldid FROM  vtiger_field 
                WHERE tablename = 'its4you_cashflow4you' AND columnname = 'relatedto'";
        }
        $return = $this->db->pquery($sql, []);
        $fieldid = $this->db->query_result($return, 0, 'fieldid');

        $sql = "INSERT INTO vtiger_fieldmodulerel ( fieldid, module, relmodule ) VALUES (?,'Cashflow4You',?)";
        $this->db->pquery($sql, [$fieldid, $module]);
    }

    function deleteRelationModule($module) {
        if ($module == "Invoice" || $module == "SalesOrder" || $module == "PurchaseOrder" || $module == "Potentials" || $module == "ITS4YouPreInvoice" || $module == "CreditNotes4You") {
            $sql = "SELECT fieldid FROM  vtiger_field 
                WHERE tablename = 'its4you_cashflow4you' AND columnname = 'relationid'";
        } else {
            $sql = "SELECT fieldid FROM  vtiger_field 
                WHERE tablename = 'its4you_cashflow4you' AND columnname = 'relatedto'";
        }
        $return = $this->db->pquery($sql, []);
        $fieldid = $this->db->query_result($return, 0, 'fieldid');

        $sql = "DELETE FROM vtiger_fieldmodulerel 
              WHERE fieldid = ? AND module = 'Cashflow4You' AND relmodule=?";
        $this->db->pquery($sql, [$fieldid, $module]);
    }

    function showCashflowInformation($module_table) {
        $sql = "UPDATE vtiger_field SET displaytype = '2' WHERE tablename = ? AND ( columnname='p_paid_amount' OR columnname='p_open_amount')";
        $this->db->pquery($sql, [$module_table]);
    }

    function hideCashflowInformation($module_table) {
        $sql = "UPDATE vtiger_field SET displaytype = '3' WHERE tablename = ? AND ( columnname='p_paid_amount' OR columnname='p_open_amount')";
        $this->db->pquery($sql, [$module_table]);
    }

    function checkCFIntegration($module) {
        $Module_instance = Vtiger_Module::getInstance($module);
        $module_id = $Module_instance->getId();
        $instance_nameCashflow = Vtiger_Module::getInstance('Cashflow4You');
        $cashflow_id = $instance_nameCashflow->getId();

        $sql = "SELECT relation_id FROM `vtiger_relatedlists` WHERE `related_tabid` = ? AND tabid=?";
        $result = $this->db->pquery($sql, array($cashflow_id, $module_id));

        return $this->db->num_rows($result);
    }

}