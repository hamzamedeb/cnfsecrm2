<?php
/* * *******************************************************************************
 * The content of this file is subject to the Cashflow4You license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 * ****************************************************************************** */

class Cashflow4You extends CRMEntity {
    private $version_type;
    private $license_key;
    private $version_no;

    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'its4you_cashflow4you';
    var $table_index = 'cashflow4youid';
    var $column_fields = array();

    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = array('its4you_cashflow4youcf', 'cashflow4youid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    var $tab_name = array('vtiger_crmentity', 'its4you_cashflow4you', 'its4you_cashflow4youcf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'its4you_cashflow4you' => 'cashflow4youid',
        'its4you_cashflow4youcf' => 'cashflow4youid');

    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = array(
        // tablename should not have prefix 'vtiger_'
        'Cashflow4You No' => array('cashflow' => 'cashflow4you_no'),
        'Cashflow4You Name' => array('cashflow' => 'cashflow4youname'),
        'Relation' => array('cashflow' => 'relationid'),
        'Paid Amount' => array('cashflow' => 'paymentamount'),
        'Due Date' => array('cashflow' => 'due_date'),
        'Payment Date' => array('cashflow' => 'paymentdate'),
        'Payment Status' => array('cashflow' => 'cashflow4you_status'),
        'Payment Method' => array('cashflow' => 'cashflow4you_paymethod')
    );

    var $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'Cashflow4You No' => 'cashflow4you_no',
        'Cashflow4You Name' => 'cashflow4youname',
        'Relation' => 'relationid',
        'Paid Amount' => 'paymentamount',
        'Due Date' => 'due_date',
        'PaymentDate' => 'paymentdate',
        'Payment Status' => 'cashflow4you_status',
        'Payment Method' => 'cashflow4you_paymethod',
        //'Assigned To' => 'assigned_user_id'
);

    // Make the field link to detail view
    var $list_link_field = 'cashflow4youname';

    // For Popup listview and UI type support
    var $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Cashflow4You No' => array('cashflow4you', 'cashflow4you_no'),
        'Assigned To' => array('vtiger_crmentity', 'assigned_user_id'),
    );

    var $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'Cashflow4You Name' => 'cashflow4youname',
        'Assigned To' => 'assigned_user_id',
    );

    // For Popup window record selection
    var $popup_fields = array('cashflow4youname');

    // For Alphabetical search
    var $def_basicsearch_col = 'cashflow4youname';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'cashflow4youname';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = array('createdtime', 'modifiedtime', 'cashflow4youname');

    var $default_order_by = 'paymentdate';
    var $default_sort_order = 'DESC';

    var $related_tables = array('its4you_cashflow4youcf' => array('cashflow4youid'));

    function __construct() {
        $this->column_fields = getColumnFields('Cashflow4You');
        $this->db = PearDatabase::getInstance();
        $this->log = LoggerManager::getLogger('Cashflow4You');;
    }

    //Getters and Setters
    public function GetVersionType() {
        return $this->version_type;
    }

    public function GetLicenseKey() {
        return $this->license_key;
    }

    function save_module($module) {
        $this->db = PearDatabase::getInstance();
        //$this->column_fields['paymentamount'] = str_replace(' ', '', $this->column_fields['paymentamount']);
        $current_user = Users_Record_Model::getCurrentUserModel();
        $vat_amount = CurrencyField::convertToDBFormat($this->column_fields['vat_amount'], $current_user, true);
        $paymentamount = CurrencyField::convertToDBFormat($this->column_fields['paymentamount'], $current_user, true);

        $without_tax = abs($paymentamount) - abs($vat_amount);
        $payamount_main = $paymentamount;
        if ($this->column_fields["currency_id"] != CurrencyField::getDBCurrencyId()) {
            $currencyRateAndSymbol = getCurrencySymbolandCRate($this->column_fields["currency_id"]);
            $payamount_main = CurrencyField::convertToDollar($payamount_main, $currencyRateAndSymbol["rate"]);
        }

        if ($this->column_fields["paymentdate"] != null && date_create(DateTimeField::convertToDBFormat($this->column_fields["paymentdate"])) <= date_create("now")) {
            $status = ($this->column_fields['cashflow4you_paytype'] == 'Incoming') ? 'Received' : 'Paid';
        } else if ($this->column_fields["paymentdate"] != null && date_create(DateTimeField::convertToDBFormat($this->column_fields["paymentdate"])) > date_create("now")) {
            $status = 'Waiting';
        } else if ($this->column_fields["due_date"] != null && date_create(DateTimeField::convertToDBFormat($this->column_fields["due_date"])) >= date_create("now")) {
            $status = 'Waiting';
        } else if ($this->column_fields["due_date"] != null && date_create(DateTimeField::convertToDBFormat($this->column_fields["due_date"])) < date_create("now")) {
            $status = 'Pending';
        } else {
            $status = 'Created';
        }

        $without_tax = number_format($without_tax, 2, '.', '');
        $this->db->pquery("UPDATE its4you_cashflow4you SET total_without_vat=?, cashflow4you_status=?, payamount_main=? WHERE cashflow4youid=?", array($without_tax, $status, $payamount_main, $this->id));

        $cashflow_utils = new Cashflow4You_Relation_Model();

        // set relation data
        if (isset($this->column_fields['relationid']) && $this->column_fields['relationid'] != '' && $this->column_fields['relationid'] != 0) {
            $entity_type = getSalesEntityType($this->column_fields['relationid']);

            $cashflow_utils->updateRelations($entity_type, $this->id, $this->column_fields['relationid']);

            $select = "SELECT COUNT(*) AS count FROM its4you_cashflow4you_associatedto 
                        INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid=its4you_cashflow4you_associatedto.cashflow4you_associated_id 
                        WHERE vtiger_crmentity.deleted=0 
                        AND its4you_cashflow4you_associatedto.cashflow4youid=?";
            $select_res = $this->db->pquery($select, array($this->id));
            $num = $this->db->query_result($select_res, 0, 'count');

            if ($entity_type != "SalesOrder" && $entity_type != "ITS4YouPreInvoice") {
                if ($num == 0) {
                    $insert = "INSERT INTO its4you_cashflow4you_associatedto ( cashflow4youid, cashflow4you_associated_id, partial_amount )VALUES (?, ?, ?)";
                    $this->db->pquery($insert, array($this->id, $this->column_fields['relationid'], $paymentamount));
                } else {
                    $insert = "UPDATE its4you_cashflow4you_associatedto SET partial_amount = ?, cashflow4you_associated_id = ? 
                                WHERE its4you_cashflow4you_associatedto.cashflow4youid=?";
                    $this->db->pquery($insert, array($paymentamount, $this->column_fields['relationid'], $this->id));
                }
            } else {
                if ($num > 0) {
                    $insert = "UPDATE its4you_cashflow4you_associatedto SET partial_amount = ? 
                                WHERE its4you_cashflow4you_associatedto.cashflow4youid=?";
                    $this->db->pquery($insert, array($paymentamount, $this->id));

                    $select = "SELECT cashflow4you_associated_id  
                                FROM its4you_cashflow4you_associatedto 
                                INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid=its4you_cashflow4you_associatedto.cashflow4you_associated_id 
                                WHERE vtiger_crmentity.deleted=?
                                AND its4you_cashflow4you_associatedto.cashflow4youid=?";
                    $select_res = $this->db->pquery($select, array('0',$this->id));
                    $num = $this->db->num_rows($select_res);

                    for ($i = 0; $i < $num; $i++) {

                        $associated_id = $this->db->query_result($select_res, $i, 'cashflow4you_associated_id');
                        $asoc_entity_type = getSalesEntityType($associated_id);

                        $cashflow_utils->updateSavedRelation($asoc_entity_type, $associated_id, $this->id);
                    }
                }
            }
            $cashflow_utils->updateSavedRelation($entity_type, $this->column_fields['relationid'], $this->id);
        } else if (isset($_REQUEST['idstring']) && $_REQUEST['idstring'] != '' /*&& strpos($_REQUEST['idstring'],';') !== false*/) {
            $Idlist = explode(';', $_REQUEST["idstring"]);

            $cashflow_utils->updateRelations($_REQUEST["sourcemodule"], $_REQUEST["currentid"], $this->column_fields['relationid']);

            foreach ($Idlist AS $invoiceId) {
                $r_paymentamount = CurrencyField::convertToDBFormat($_REQUEST["payment_" . $invoiceId], $current_user,true);
                $i_attr = array($r_paymentamount, $invoiceId);
                if ($_REQUEST['record'] != "") {
                    $insert = "UPDATE its4you_cashflow4you_associatedto SET partial_amount = ? 
                              WHERE  its4you_cashflow4you_associatedto.cashflow4you_associated_id=? AND its4you_cashflow4you_associatedto.cashflow4youid=?";
                    $i_attr[] = $_REQUEST['record'];
                } else {
                    $insert = "INSERT INTO its4you_cashflow4you_associatedto ( partial_amount, cashflow4you_associated_id, cashflow4youid)VALUES (?, ?, ?)";
                    $i_attr[] = $_REQUEST["currentid"];
                }
                $this->db->pquery($insert, $i_attr);
            }

            $cashflow_utils->SavePaymentFromRelation();

        }
        $cashflow_utils->updateRelationsField($this->id);

        if ($_REQUEST["return_module"] == "") {
            $_REQUEST["return_module"] = $_REQUEST["module"];
        }
        if ($_REQUEST["return_id"] == "") {
            $_REQUEST["return_id"] = $_REQUEST["currentid"];
        }
    }

    /**
     * Handle saving related module information.
     * NOTE: This function has been added to CRMEntity (base class).
     * You can override the behavior by re-defining it here.
     */
    function save_related_module($module, $crmid, $with_module, $with_crmid) {
        if (!in_array($with_module, [''])) {
            parent::save_related_module($module, $crmid, $with_module, $with_crmid);

            return;
        }
        /**
         * $_REQUEST['action']=='Save' when choosing ADD from Related list.
         * Do nothing on the payment's entity when creating a related new child using ADD in relatedlist
         * by doing nothing we do not insert any line in the crmentity's table when
         * we are relating a module to this module
         */
        if ($_REQUEST['action'] != 'updateRelations') {
            return;
        }
        $_REQUEST['submode'] = 'no_html_conversion';
        //update the child elements' column value for uitype10
        $destinationModule = vtlib_purify($_REQUEST['destination_module']);
        if (!is_array($with_crmid)) {
            $with_crmid = [$with_crmid];
        }
        foreach ($with_crmid as $relcrmid) {
            $child = CRMEntity::getInstance($destinationModule);
            $child->retrieve_entity_info($relcrmid, $destinationModule);
            $child->mode = 'edit';
            $child->column_fields['cashflow4youid'] = $crmid;
            $child->save($destinationModule, $relcrmid);
        }
    }

    function get_invoice($id, $cur_tab_id, $rel_tab_id, $actions = false) {
        global $log, $singlepane_view, $currentModule, $current_user;
        $log->debug("Entering get_invoice(" . $id . ") method ...");
        $this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
        require_once("modules/$related_module/$related_module.php");
        $other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
        $singular_modname = vtlib_toSingular($related_module);

        $parenttab = getParentTab();

        if ($singlepane_view == 'true') {
            $returnset = '&return_module=' . $this_module . '&return_action=DetailView&return_id=' . $id;
        } else {
            $returnset = '&return_module=' . $this_module . '&return_action=CallRelatedList&return_id=' . $id;
        }

        $button = '';

        if ($actions) {
            if (is_string($actions)) {
                $actions = explode(',', strtoupper($actions));
            }
            if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
                $button .= "<input title='" . getTranslatedString('LBL_SELECT') . " " . getTranslatedString($related_module) . "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='" . getTranslatedString('LBL_SELECT') . " " . getTranslatedString($related_module) . "'>&nbsp;";
            }
            if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
                $button .= "<input title='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString($singular_modname) . "' class='crmbutton small create'" .
                    " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
                    " value='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString($singular_modname) . "'>&nbsp;";
            }
        }

        $query = "SELECT vtiger_crmentity.*, vtiger_invoice.*, its4you_cashflow4you.cashflow4youname, 
                CASE WHEN (vtiger_users.user_name not like '') 
                THEN vtiger_users.user_name 
                ELSE vtiger_groups.groupname 
                END
                AS user_name FROM vtiger_invoice 
                LEFT JOIN vtiger_invoicecf ON vtiger_invoicecf.invoiceid = vtiger_invoice.invoiceid
			          LEFT JOIN vtiger_invoicebillads ON vtiger_invoicebillads.invoicebilladdressid = vtiger_invoice.invoiceid
			          LEFT JOIN vtiger_invoiceshipads ON vtiger_invoiceshipads.invoiceshipaddressid = vtiger_invoice.invoiceid
                INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_invoice.invoiceid 
                INNER JOIN vtiger_crmentityrel ON vtiger_crmentityrel.relcrmid=vtiger_crmentity.crmid 
                INNER JOIN its4you_cashflow4you ON vtiger_crmentityrel.crmid=its4you_cashflow4you.cashflow4youid
                LEFT JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid
                LEFT JOIN vtiger_groups ON vtiger_groups.groupid=vtiger_crmentity.smownerid 
                WHERE vtiger_crmentity.deleted=0 AND its4you_cashflow4you.cashflow4youid=" . $id;
        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        if ($return_value == null) {
            $return_value = array();
        }
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_invoice method ...");

        return $return_value;

    }

    // ITS4YOU-CR SlOl 7/26/2011 
    function get_purchase_orders($id, $cur_tab_id, $rel_tab_id, $actions = false) {
        global $log, $singlepane_view, $currentModule, $current_user;
        $log->debug("Entering get_purchase_orders(" . $id . ") method ...");
        $this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
        require_once("modules/$related_module/$related_module.php");
        $other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
        $singular_modname = vtlib_toSingular($related_module);

        $parenttab = getParentTab();

        if ($singlepane_view == 'true') {
            $returnset = '&return_module=' . $this_module . '&return_action=DetailView&return_id=' . $id;
        } else {
            $returnset = '&return_module=' . $this_module . '&return_action=CallRelatedList&return_id=' . $id;
        }

        $button = '';

        if ($actions) {
            if (is_string($actions)) {
                $actions = explode(',', strtoupper($actions));
            }
            if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
                $button .= "<input title='" . getTranslatedString('LBL_SELECT') . " " . getTranslatedString($related_module) . "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='" . getTranslatedString('LBL_SELECT') . " " . getTranslatedString($related_module) . "'>&nbsp;";
            }
            if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
                $button .= "<input title='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString($singular_modname) . "' class='crmbutton small create'" .
                    " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
                    " value='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString($singular_modname) . "'>&nbsp;";
            }
        }

        $query = "select vtiger_crmentity.*, vtiger_purchaseorder.*, its4you_cashflow4you.cashflow4youname, 
                  case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name
                  from vtiger_purchaseorder 
                  LEFT JOIN vtiger_purchaseordercf ON vtiger_purchaseordercf.purchaseorderid = vtiger_invoice.invoiceid
                  inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_purchaseorder.purchaseorderid 
                  inner join vtiger_crmentityrel on vtiger_crmentityrel.crmid=vtiger_crmentity.crmid 
                  inner join its4you_cashflow4you on vtiger_crmentityrel.relcrmid=its4you_cashflow4you.cashflow4youid 
                  left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
                  left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid 
                  where vtiger_crmentity.deleted=0 and its4you_cashflow4you.cashflow4youid=" . $id;

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        if ($return_value == null) {
            $return_value = array();
        }
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_purchase_orders method ...");

        return $return_value;
    }

    function get_sales_orders($id, $cur_tab_id, $rel_tab_id, $actions = false) {
        global $log, $singlepane_view, $currentModule, $current_user;
        $log->debug("Entering get_sales_orders(" . $id . ") method ...");
        $this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
        require_once("modules/$related_module/$related_module.php");
        $other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
        $singular_modname = vtlib_toSingular($related_module);

        $parenttab = getParentTab();

        if ($singlepane_view == 'true') {
            $returnset = '&return_module=' . $this_module . '&return_action=DetailView&return_id=' . $id;
        } else {
            $returnset = '&return_module=' . $this_module . '&return_action=CallRelatedList&return_id=' . $id;
        }

        $button = '';

        if ($actions) {
            if (is_string($actions)) {
                $actions = explode(',', strtoupper($actions));
            }
            if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
                $button .= "<input title='" . getTranslatedString('LBL_SELECT') . " " . getTranslatedString($related_module) . "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='" . getTranslatedString('LBL_SELECT') . " " . getTranslatedString($related_module) . "'>&nbsp;";
            }
            if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
                $button .= "<input title='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString($singular_modname) . "' class='crmbutton small create'" .
                    " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
                    " value='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString($singular_modname) . "'>&nbsp;";
            }
        }

        $query = "select vtiger_crmentity.*, vtiger_salesorder.*, its4you_cashflow4you.cashflow4youname, 
                      case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name
                      from vtiger_salesorder 
                      LEFT JOIN vtiger_salesordercf ON vtiger_salesordercf.salesorderid = vtiger_invoice.invoiceid
                      inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_salesorder.salesorderid 
                      inner join vtiger_crmentityrel on vtiger_crmentityrel.crmid=vtiger_crmentity.crmid 
                      inner join its4you_cashflow4you on vtiger_crmentityrel.relcrmid= its4you_cashflow4you.cashflow4youid 
                      left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
                      left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid 
                      where vtiger_crmentity.deleted=0 and its4you_cashflow4you.cashflow4youid=" . $id;

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        if ($return_value == null) {
            $return_value = array();
        }
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_sales_orders method ...");

        return $return_value;
    }

    function getListQuery($module, $where = '') {
        $query = "SELECT vtiger_crmentity.*, $this->table_name.*";

        // Select Custom Field Table Columns if present
        if (!empty($this->customFieldTable)) {
            $query .= ", " . $this->customFieldTable[0] . ".* ";
        }

        $query .= " FROM $this->table_name";

        $query .= "	INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $this->table_name.$this->table_index";

        // Consider custom table join as well.
        if (!empty($this->customFieldTable)) {
            $query .= " INNER JOIN " . $this->customFieldTable[0] . " ON " . $this->customFieldTable[0] . '.' . $this->customFieldTable[1] .
                " = $this->table_name.$this->table_index";
        }
        $query .= " LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid";
        $query .= " LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";

        $linkedModulesQuery = $this->db->pquery("SELECT distinct fieldname, columnname, relmodule FROM vtiger_field" .
            " INNER JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid" .
            " WHERE uitype='10' AND vtiger_fieldmodulerel.module=?", [$module]);
        $linkedFieldsCount = $this->db->num_rows($linkedModulesQuery);

        for ($i = 0; $i < $linkedFieldsCount; $i++) {
            $related_module = $this->db->query_result($linkedModulesQuery, $i, 'relmodule');
            $fieldname = $this->db->query_result($linkedModulesQuery, $i, 'fieldname');
            $columnname = $this->db->query_result($linkedModulesQuery, $i, 'columnname');

            $other = CRMEntity::getInstance($related_module);
            vtlib_setup_modulevars($related_module, $other);

            $query .= " LEFT JOIN $other->table_name ON $other->table_name.$other->table_index =" .
                "$this->table_name.$columnname";
        }

        global $current_user;
        $query .= $this->getNonAdminAccessControlQuery($module, $current_user);
        $query .= "WHERE vtiger_crmentity.deleted = 0 " . $where;

        return $query;
    }

    /**
     * Invoked when special actions are performed on the module.
     *
     * @param String Module name
     * @param String Event Type
     */
    function vtlib_handler($moduleName, $eventType) {
        $Cashflow4You = new Cashflow4You_Module_Model();

        if ($eventType == 'module.postinstall') {
            // TODO Handle actions after this module is installed.
            // add block, custom fields and widgets into module Invoice
            //$this->addCashflowInformation();
            $this->setNumbering();
            $this->setPicklistDependency();

            static::enableModTracker('Cashflow4You');

            $this->db->pquery("UPDATE vtiger_cashflow4you_paytype SET `presence`=? WHERE cashflow4you_paytype=? OR cashflow4you_paytype=?", array('0','Incoming','Outgoing'));

            $this->db->pquery("INSERT INTO `vtiger_fieldmodulerel` (`fieldid` ,`module` ,`relmodule` ,`status` ,`sequence`)VALUES (
                                  (SELECT fieldid FROM vtiger_field WHERE columnname = ? AND tablename = ?), ?, ?, NULL , NULL)", array('related_to','vtiger_modcomments','ModComments','Cashflow4You'));

        } else if ($eventType == 'module.disabled') {
            $Cashflow4You->disableEventHandler();
        } else if ($eventType == 'module.enabled') {
            $Cashflow4You->enableEventHandler();
        } else if ($eventType == 'module.preuninstall') {
            $Cashflow4You->delEventHandler();
            $this->DeleteDB();
        } else if ($eventType == 'module.postupdate') {
            // cast kodu z post install
            // zopakuje sa este aj pri post update ak by sa to nahodov nevykonalo pri instalacii
            //$this->addCashflowInformation();
            $this->setNumbering();
            $this->setPicklistDependency();

            $this->db->pquery("UPDATE vtiger_cashflow4you_paytype SET `presence`=? WHERE cashflow4you_paytype=? OR cashflow4you_paytype=?", array('0','Incoming','Outgoing'));

            $inv_module = Vtiger_Module::getInstance('Invoice');
            $result = $this->db->pquery("SELECT version FROM vtiger_tab WHERE name = ?", array('Cashflow4You'));
            $version = $this->db->query_result($result, 0, "version");
            $version = substr($version, strpos($version, '.') + 1) * 1;

            $tabid = getTabId("Cashflow4You");

            $Field = array("cashflow4youname" => "Cashflow4You Name",
                "cashflow4you_no" => "Cashflow4You No",
                "paymentdate" => "Payment Date",
                "cashflow4you_paymethod" => "Payment Method",
                "paymentamount" => "Paid Amount",
                "relationid" => "Relation",
                "transactionid" => "Transaction ID",
                "due_date" => "Due Date",
                "currency_id" => "Currency",
                "cashflow4you_paytype" => "Payment Type",
                "cashflow4you_category" => "Payment Category",
                "relatedto" => "Related To",
                "cashflow4you_cash" => "Payment Mode",
                "cashflow4you_subcategory" => "Payment Subcategory",
                "cashflow4you_status" => "Payment Status",
                "accountingdate" => "Accounting Date",
                "vat_amount" => "VAT",
                "total_without_vat" => "Price without VAT",
                "tax_expense" => "Tax Component",
                "cashflow4you_associated_no" => "Associated No",
                "createdtime" => "Created Time",
                "modifiedtime" => "Modified Time",
                "description" => "Description",
                "smownerid" => "Assigned To"
            );
            foreach ($Field AS $colomnname => $fieldlabel) {
                $update = "UPDATE vtiger_field SET fieldlabel=? WHERE tabid=? AND vtiger_field.columnname=? AND fieldname!=?";
                $this->db->pquery($update, array($fieldlabel, $tabid, $colomnname, $fieldlabel));
            }

            $update = "UPDATE vtiger_field SET uitype=?, fieldlabel=?, typeofdata=? WHERE vtiger_field.columnname=? AND uitype=? AND fieldname=?";
            $this->db->pquery($update, array(72, "Paid Amount", "N~O",'p_paid_amount','8','paidamount'));

            $update = "UPDATE vtiger_field SET uitype=?, fieldlabel=?, typeofdata=? WHERE vtiger_field.columnname=? AND uitype=? AND fieldname=?";
            $this->db->pquery($update, array(72, "Remaining Amount", "N~O",'p_open_amount','8','openamount'));

            $update = "UPDATE vtiger_relatedlists SET actions=? WHERE (label=? OR label=? OR label=?) AND tabid =?";
            $this->db->pquery($update, array("", 'Invoice', 'Purchase Order', 'Purchase Order', $tabid));

            $update = "UPDATE vtiger_relatedlists SET actions=? WHERE label=? AND tabid =?";
            $this->db->pquery($update, array("SELECT,ADD", 'Documents', $tabid));

            if ($version <= 1.6) {
                $Modules = array('SalesOrder' => "vtiger_salesoreder", 'Potentials' => "vtiger_potential", 'ITS4YouPreInvoice' => "its4you_preinvoice",
                    'CreditNotes4You' => "vtiger_creditnotes4you");

                foreach ($Modules AS $module => $table) {
                    $inv_module = Vtiger_Module::getInstance($module);
                    if ($inv_module != false) {

                        if ($module == "ITS4YouPreInvoice" || $module == "CreditNotes4You" || $module == "Potentials") {
                            $this->db->pquery("ALTER TABLE `" . $table . "` ADD `p_open_amount` DECIMAL(25, 3) NOT NULL", array());
                            $this->db->pquery("ALTER TABLE `" . $table . "` ADD `p_paid_amount` DECIMAL(25, 3) NOT NULL", array());
                            $total = "total";
                            if ($module == "Potentials") {
                                $total = "amount";
                            }
                            $this->db->pquery("UPDATE " . $table . " SET p_open_amount=" . $total . ", p_paid_amount=?", array('0.000'));
                            $modulename = "Cashflow4You";
                            $linked_module = Vtiger_Module::getInstance($modulename);
                            $potentials = Vtiger_Module::getInstance($module);
                            $potentials->setRelatedList($linked_module, $modulename, array(), "get_dependents_list");

                        }
                    }
                }
            }

            $sql = "SELECT name FROM `vtiger_tab` INNER JOIN vtiger_links ON vtiger_links.tabid=vtiger_tab.tabid WHERE `linkurl` LIKE '%cashflow4you%' AND linktype != ?";
            $result = $this->db->pquery($sql, array('HEADERSCRIPT'));
            $i = 0;
            while ($row = $this->db->fetchByAssoc($result)) {
                $Modules[] = $row["name"];
            }
            foreach ($Modules AS $module) {
                $result = $this->db->pquery("SELECT fieldid FROM `vtiger_fieldmodulerel` WHERE `module` = ? AND `relmodule` = ?", array('Cashflow4You',$module));
                $rel_fieldid = $this->db->query_result($result, 0, 'fieldid');

                $result = $this->db->pquery("SELECT fieldid FROM `vtiger_field` WHERE `columnname` = ? AND `tablename` = ? AND `uitype` = ?", array('relationid','its4you_cashflow4you','10'));
                $fieldid = $this->db->query_result($result, 0, 'fieldid');

                if ($rel_fieldid != $fieldid) {
                    $insert = "INSERT INTO `vtiger_fieldmodulerel` (`fieldid` , `module` , `relmodule` , `status` , `sequence` )
                                VALUES ( ?, 'Cashflow4You', 'Potentials', NULL , NULL ),
                                       ( ?, 'Cashflow4You', 'ITS4YouPreInvoice', NULL , NULL ),
                                       ( ?, 'Cashflow4You', 'CreditNotes4You', NULL , NULL )";
                    $this->db->pquery($insert, array($fieldid, $fieldid, $fieldid));
                }
            }

            $result = $this->db->pquery("SELECT fieldid FROM `vtiger_field` WHERE `columnname` = ? AND `tablename` = ? AND `uitype` = ?", array('contactid','its4you_cashflow4you','57'));
            if ($this->db->num_rows($result) != 0) {
                $fieldid = $this->db->query_result($result, 0, 'fieldid');
                $this->db->pquery("UPDATE `vtiger_field` SET uitype=? WHERE `fieldid` = ?", array("10", $fieldid));
                $insert = "INSERT INTO `vtiger_fieldmodulerel` (`fieldid` , `module` , `relmodule` , `status` , `sequence` )
                            VALUES ( ?, ?, ?, NULL , NULL )";
                $this->db->pquery($insert, array($fieldid,'Cashflow4You','Contacts'));
            }

            static::enableModTracker('Cashflow4You');

        } else if ($eventType == 'module.preupdate') {
            // TODO Handle actions after this module is updated.
        }
    }

    private function addCashflowInformation() {
        $this->db->pquery("UPDATE vtiger_invoice SET p_open_amount=?, p_paid_amount=total, balance=?, received=total WHERE invoicestatus='Paid' AND p_open_amount IS NULL AND p_paid_amount IS NULL", array('0.000','0.000'));
        $this->db->pquery("UPDATE vtiger_invoice SET p_open_amount=total, p_paid_amount=?,balance=total, received=? WHERE invoicestatus!='Paid' AND p_open_amount IS NULL AND p_paid_amount IS NULL", array('0.000','0.000'));
        $this->db->pquery("UPDATE vtiger_purchaseorder SET p_open_amount=total, p_paid_amount=?,balance=total WHERE postatus!=? AND p_open_amount IS NULL AND p_paid_amount IS NULL", array('0.000','Cancelled'));
        $this->db->pquery("UPDATE vtiger_salesorder SET p_open_amount=total, p_paid_amount=? WHERE sostatus!=? AND p_open_amount IS NULL AND p_paid_amount IS NULL", array('0.000','Cancelled'));
        $this->db->pquery("UPDATE vtiger_potential SET p_open_amount=amount, p_paid_amount=?", array('0.000'));

        if (Vtiger_Module::getInstance("ITS4YouPreInvoice") != false) {
            $this->db->pquery("UPDATE its4you_preinvoice SET p_open_amount=total, p_paid_amount=? WHERE postatus!=? AND p_open_amount IS NULL AND p_paid_amount IS NULL", array('0.000','Cancelled'));
        }
        if (Vtiger_Module::getInstance("CreditNotes4You") != false) {
            $this->db->pquery("UPDATE vtiger_creditnotes4you SET p_open_amount=total, p_paid_amount=? WHERE postatus!=? AND p_open_amount IS NULL AND p_paid_amount IS NULL", array('0.000','Cancelled'));
        }
    }

    /*
    * Function to get the secondary query part of a report
    * @param - $module primary module name
    * @param - $secmodule secondary module name
    * returns the query string formed on fetching the related data for report for secondary module
    */
    function generateReportsSecQuery($module, $secmodule, $queryPlanner) {

        $matrix = $queryPlanner->newDependencyMatrix();
        $matrix->setDependency('vtiger_crmentityCashflow4You', array('vtiger_groupsCashflow4You', 'vtiger_usersCashflow4You', 'vtiger_lastModifiedByCashflow4You'));
        $matrix->setDependency('its4you_cashflow4you', array('vtiger_crmentityCashflow4You', 'its4you_cashflow4youcf', 'its4you_cashflow4youCashflow4You', ' its4you_cashflow4you_associatedto'));

        if (!$queryPlanner->requireTable('its4you_cashflow4you', $matrix)) {
            return '';
        }

        $query = $this->getRelationQuery($module, $secmodule, "its4you_cashflow4you", "cashflow4youid", $queryPlanner);

        $module_low = strtolower($module);

        if ($queryPlanner->requireTable('vtiger_crmentityCashflow4You', $matrix)) {
            $query .= " left join vtiger_crmentity as vtiger_crmentityCashflow4You on vtiger_crmentityCashflow4You.crmid=its4you_cashflow4you.cashflow4youid and vtiger_crmentityCashflow4You.deleted=0";
        }
        if ($queryPlanner->requireTable('its4you_cashflow4youcf')) {
            $query .= " left join its4you_cashflow4youcf on its4you_cashflow4you.cashflow4youid = its4you_cashflow4youcf.cashflow4youid";
        }
        if ($queryPlanner->requireTable('its4you_cashflow4you_associatedto')) {
            $query .= "	left join its4you_cashflow4you_associatedto as its4you_cashflow4you_associatedtoCashflow4You on its4you_cashflow4you_associatedtoCashflow4You.cashflow4youid = vtiger_crmentityCashflow4You.cashflow4youid";
        }
        if ($queryPlanner->requireTable('its4you_cashflow4youCashflow4You')) {
            $query .= "	left join its4you_cashflow4you as its4you_cashflow4youCashflow4You on its4you_cashflow4youCashflow4You.cashflow4youid = vtiger_crmentityCashflow4You.crmid";
        }
        if ($queryPlanner->requireTable('vtiger_groupsCashflow4You')) {
            $query .= "	left join vtiger_groups as vtiger_groupsCashflow4You on vtiger_groupsCashflow4You.groupid = vtiger_crmentityCashflow4You.smownerid";
        }
        if ($queryPlanner->requireTable('vtiger_usersCashflow4You')) {
            $query .= " left join vtiger_users as vtiger_usersCashflow4You on vtiger_usersCashflow4You.id = vtiger_crmentityCashflow4You.smownerid";
        }
        if ($queryPlanner->requireTable('vtiger_lastModifiedByCashflow4You')) {
            $query .= " left join vtiger_users as vtiger_lastModifiedByCashflow4You on vtiger_lastModifiedByCashflow4You.id = vtiger_crmentityCashflow4You.modifiedby ";
        }
		
		if ($queryPlanner->requireTable('vtiger_currency_infoCashflow4You')) {
			$moduleFocus = CRMEntity::getInstance($module);
			$query .= ' left join vtiger_currency_info as vtiger_currency_infoCashflow4You on vtiger_currency_infoCashflow4You.id = '.$moduleFocus->table_name.'.currency_id ';
		}
		
        if ($queryPlanner->requireTable("vtiger_accountRelCashflow4You")) {
            $query .= " left join vtiger_account as vtiger_accountRelCashflow4You on its4you_cashflow4you.relatedto = vtiger_accountRelCashflow4You.accountid AND vtiger_accountRelCashflow4You.accountid = its4you_cashflow4you.relatedto ";
        }
        if ($queryPlanner->requireTable("vtiger_vendorRelCashflow4You")) {
            $query .= " left join vtiger_vendor as vtiger_vendorRelCashflow4You on its4you_cashflow4you.relatedto = vtiger_vendorRelCashflow4You.vendorid AND vtiger_vendorRelCashflow4You.vendorid = its4you_cashflow4you.relatedto ";
        }
        if ($queryPlanner->requireTable("vtiger_contactdetailsRelCashflow4You")) {
            $query .= " left join vtiger_contactdetails as vtiger_contactdetailsRelCashflow4You on its4you_cashflow4you.contactid = vtiger_contactdetailsRelCashflow4You.contactid AND vtiger_contactdetailsRelCashflow4You.contactid = its4you_cashflow4you.contactid ";
        }

        return $query;
    }

    /*
     * Function to get the primary query part of a report
     * @param - $module primary module name
     * returns the query string formed on fetching the related data for report for secondary module
     */
    function generateReportsQuery($module, $queryPlanner) {
        global $current_user;

        $matrix = $queryPlanner->newDependencyMatrix();
        $matrix->setDependency('its4you_cashflow4you', array('vtiger_crmentityCashflow4You', 'vtiger_accountCashflow4You', 'vtiger_leaddetailsCashflow4You', 'its4you_cashflow4youcf', 'vtiger_potentialCashflow4You'));
        $query = "from its4you_cashflow4you
                   inner join vtiger_crmentity on vtiger_crmentity.crmid=its4you_cashflow4you.cashflow4youid";
        if ($queryPlanner->requireTable("its4you_cashflow4youcf")) {
            $query .= " left join its4you_cashflow4youcf on its4you_cashflow4you.cashflow4youid = its4you_cashflow4youcf.cashflow4youid";
        }
        if ($queryPlanner->requireTable("vtiger_usersCashflow4You")) {
            $query .= " left join vtiger_users as vtiger_usersCashflow4You on vtiger_usersCashflow4You.id = vtiger_crmentity.smownerid";
        }
        if ($queryPlanner->requireTable("vtiger_groupsCashflow4You")) {
            $query .= " left join vtiger_groups as vtiger_groupsCashflow4You on vtiger_groupsCashflow4You.groupid = vtiger_crmentity.smownerid";
        }

        $result = $this->db->pquery("SELECT fieldid FROM vtiger_field WHERE columnname = ? AND tablename = ?", array("relationid", "its4you_cashflow4you"));
        $relFieldId = $this->db->query_result($result, 0, "fieldid");
		
        // ITS4YOU-CR SlOl 19. 4. 2016 10:47:45
        if ($queryPlanner->requireTable("vtiger_purchaseorderRelCashflow4You" . $relFieldId)) {
            $query .= " left join vtiger_purchaseorder as vtiger_purchaseorderRelCashflow4You" . $relFieldId . " on its4you_cashflow4you.relationid = vtiger_purchaseorderRelCashflow4You" . $relFieldId . ".purchaseorderid AND vtiger_purchaseorderRelCashflow4You" . $relFieldId . ".purchaseorderid = its4you_cashflow4you.relationid ";
        }
        if ($queryPlanner->requireTable("vtiger_salesorderRelCashflow4You" . $relFieldId)) {
            $query .= " left join vtiger_salesorder as vtiger_salesorderRelCashflow4You" . $relFieldId . " on its4you_cashflow4you.relationid = vtiger_salesorderRelCashflow4You" . $relFieldId . ".salesorderid AND vtiger_salesorderRelCashflow4You" . $relFieldId . ".salesorderid = its4you_cashflow4you.relationid ";
        }
        if ($queryPlanner->requireTable("vtiger_invoiceRelCashflow4You" . $relFieldId)) {
            $query .= " left join vtiger_invoice as vtiger_invoiceRelCashflow4You" . $relFieldId . " on its4you_cashflow4you.relationid = vtiger_invoiceRelCashflow4You" . $relFieldId . ".invoiceid AND vtiger_invoiceRelCashflow4You" . $relFieldId . ".invoiceid = its4you_cashflow4you.relationid ";
        }
        if ($queryPlanner->requireTable("vtiger_potentialRelCashflow4You" . $relFieldId)) {
            $query .= " left join vtiger_potential as vtiger_potentialRelCashflow4You" . $relFieldId . " on its4you_cashflow4you.relationid = vtiger_potentialRelCashflow4You" . $relFieldId . ".potentialid AND vtiger_potentialRelCashflow4You" . $relFieldId . ".potentialid = its4you_cashflow4you.relationid ";
        }
        if ($queryPlanner->requireTable("vtiger_creditnotes4youRelCashflow4You" . $relFieldId)) {
            $query .= " left join vtiger_creditnotes4you as vtiger_creditnotes4youRelCashflow4You" . $relFieldId . " on its4you_cashflow4you.relationid = vtiger_creditnotes4youRelCashflow4You" . $relFieldId . ".creditnotes4you_id AND vtiger_creditnotes4youRelCashflow4You" . $relFieldId . ".creditnotes4you_id = its4you_cashflow4you.relationid ";
        }
        if ($queryPlanner->requireTable("its4you_preinvoiceRelCashflow4You" . $relFieldId)) {
            $query .= " left join its4you_preinvoice as its4you_preinvoiceRelCashflow4You" . $relFieldId . " on its4you_cashflow4you.relationid = its4you_preinvoiceRelCashflow4You" . $relFieldId . ".creditnotes4you_id AND its4you_preinvoiceRelCashflow4You" . $relFieldId . ".creditnotes4you_id = its4you_cashflow4you.relationid ";
        }

        $result = $this->db->pquery("SELECT fieldid FROM vtiger_field WHERE columnname = ? AND tablename = ?", array("contactid", "its4you_cashflow4you"));
        $relFieldId = $this->db->query_result($result, 0, "fieldid");

        if ($queryPlanner->requireTable("vtiger_contactdetailsRelCashflow4You" . $relFieldId)) {
            $query .= " left join vtiger_contactdetails as vtiger_contactdetailsRelCashflow4You" . $relFieldId . " on its4you_cashflow4you.contactid = vtiger_contactdetailsRelCashflow4You" . $relFieldId . ".contactid AND vtiger_contactdetailsRelCashflow4You" . $relFieldId . ".contactid = its4you_cashflow4you.contactid ";
        }

        $result = $this->db->pquery("SELECT fieldid FROM vtiger_field WHERE columnname = ? AND tablename = ?", array("relatedto", "its4you_cashflow4you"));
        $relFieldId = $this->db->query_result($result, 0, "fieldid");

        if ($queryPlanner->requireTable("vtiger_vendorRelCashflow4You" . $relFieldId)) {
            $query .= " left join vtiger_vendor as vtiger_vendorRelCashflow4You" . $relFieldId . " on its4you_cashflow4you.relatedto = vtiger_vendorRelCashflow4You" . $relFieldId . ".vendorid AND vtiger_vendorRelCashflow4You" . $relFieldId . ".vendorid = its4you_cashflow4you.relatedto ";
        }
        if ($queryPlanner->requireTable("vtiger_accountRelCashflow4You" . $relFieldId)) {
            $query .= " left join vtiger_account as vtiger_accountRelCashflow4You" . $relFieldId . " on its4you_cashflow4you.relatedto = vtiger_accountRelCashflow4You" . $relFieldId . ".accountid AND vtiger_accountRelCashflow4You" . $relFieldId . ".accountid = its4you_cashflow4you.relatedto ";
        }

        // ITS4YOU-END
        return $query;
    }

    private function setNumbering() {
        // set numbering for Cashflow4You
        $result1 = $this->db->pquery("SELECT * FROM vtiger_modentity_num WHERE semodule=?", array('Cashflow4you'));
        if ($this->db->num_rows($result1) == 0) {
            $num_id = $this->db->getUniqueId('vtiger_modentity_num');
            $this->db->pquery("INSERT INTO vtiger_modentity_num (num_id,semodule,prefix,start_id,cur_id,active) VALUES (?,?,?,?,?,?)", array($num_id,'Cashflow4you','PAY','001','001','1'));
        }
    }

    private function setPicklistDependency() {
        //set picklist dependency
        $DependencyTab = [["sourcefield" => "cashflow4you_cash", "targetfield" => "cashflow4you_paymethod", "sourcevalue" => "Cashflow", "targetvalues" => '["Cash","Other"]'],
            ["sourcefield" => "cashflow4you_cash", "targetfield" => "cashflow4you_paymethod", "sourcevalue" => "Bank account", "targetvalues" => '["Bank Transfer","Credit card","Google Checkout","Paypal","Wire transfer"]'],
            ["sourcefield" => "cashflow4you_category", "targetfield" => "cashflow4you_subcategory", "sourcevalue" => "Income for services", "targetvalues" => '["Extensions"]'],
            ["sourcefield" => "cashflow4you_category", "targetfield" => "cashflow4you_subcategory", "sourcevalue" => "Income for products", "targetvalues" => '["Programming"]'],
            ["sourcefield" => "cashflow4you_category", "targetfield" => "cashflow4you_subcategory", "sourcevalue" => "Office cost", "targetvalues" => '["Telephone"]'],
            ["sourcefield" => "cashflow4you_category", "targetfield" => "cashflow4you_subcategory", "sourcevalue" => "Telephone", "targetvalues" => '["none"]'],
            ["sourcefield" => "cashflow4you_category", "targetfield" => "cashflow4you_subcategory", "sourcevalue" => "Salaries", "targetvalues" => '["none"]'],
            ["sourcefield" => "cashflow4you_category", "targetfield" => "cashflow4you_subcategory", "sourcevalue" => "Wages", "targetvalues" => '["none"]'],
            ["sourcefield" => "cashflow4you_category", "targetfield" => "cashflow4you_subcategory", "sourcevalue" => "Rent", "targetvalues" => '["none"]'],
            ["sourcefield" => "cashflow4you_category", "targetfield" => "cashflow4you_subcategory", "sourcevalue" => "Fuel", "targetvalues" => '["Auto"]'],
            ["sourcefield" => "cashflow4you_category", "targetfield" => "cashflow4you_subcategory", "sourcevalue" => "Other", "targetvalues" => '[]']
        ];
        $moduleCashflow = Vtiger_Module::getInstance('Cashflow4You');
        $id = $moduleCashflow->id;

        $insert = 'INSERT INTO vtiger_picklist_dependency (id,tabid,sourcefield,targetfield,sourcevalue,targetvalues) VALUES (?,?,?,?,?,?)';

        $query = "SELECT MAX(id) AS id FROM vtiger_picklist_dependency";
        $result = $this->db->pquery($query, array());
        $curr_id = $this->db->query_result($result, 0, "id");
        foreach ($DependencyTab AS $Value) {
            $query = "SELECT * FROM vtiger_picklist_dependency WHERE tabid=? AND sourcefield=? AND targetfield=? AND sourcevalue=? AND targetvalues=?";
            $result1 = $this->db->pquery($query, array($id, $Value["sourcefield"], $Value["targetfield"], $Value["sourcevalue"], $Value["targetvalues"]));
            if ($this->db->num_rows($result1) == 0) {
                $curr_id = $this->db->getUniqueID("vtiger_picklist_dependency");
                $this->db->pquery($insert, array($curr_id, $id, $Value["sourcefield"], $Value["targetfield"], $Value["sourcevalue"], $Value["targetvalues"]));
            }
        }
        if (isset($curr_id) && $curr_id != "") {
            $this->db->pquery("UPDATE vtiger_picklist_dependency_seq SET id=?", array($curr_id));
        }
    }

    private function DeleteDB() {
        $this->db->pquery("DELETE FROM `vtiger_fieldmodulerel` WHERE module=?", array('Cashflow4You'));
        $this->db->pquery("DELETE FROM `vtiger_modentity_num` WHERE `semodule` =?", array( 'Cashflow4you'));
        $this->db->pquery("DELETE FROM `vtiger_modtracker_relations` WHERE `targetmodule` =?", array( 'Cashflow4you'));

        $query = "DELETE FROM `vtiger_picklist` WHERE `name` = 'cashflow4you_cash' OR `name` = 'cashflow4you_category' OR `name` = 'cashflow4you_paymethod' OR `name` = 'cashflow4you_paytype' OR `name` = 'cashflow4you_status' OR `name` = 'cashflow4you_subcategory'";
        $this->db->pquery($query, array());

        $query = "DELETE FROM `vtiger_picklist_dependency` WHERE `sourcefield` = 'cashflow4you_cash' OR `sourcefield` = 'cashflow4you_category' OR `sourcefield` = 'cashflow4you_paymethod' OR `sourcefield` = 'cashflow4you_paytype' OR `sourcefield` = 'cashflow4you_status' OR `sourcefield` = 'cashflow4you_subcategory'";
        $this->db->pquery($query, array());
    }

    function fix() {
        //$this->addCashflowInformation();
        $this->setNumbering();
        $this->setPicklistDependency();

        $this->db->pquery("UPDATE vtiger_cashflow4you_paytype SET `presence`=? WHERE cashflow4you_paytype=? OR cashflow4you_paytype=<", array('0','Incoming','Outgoing'));
    }

    public static function enableModTracker($moduleName) {
        include_once 'vtlib/Vtiger/Module.php';
        include_once 'modules/ModTracker/ModTracker.php';

        //Enable ModTracker for the module
        $moduleInstance = Vtiger_Module::getInstance($moduleName);
        ModTracker::enableTrackingForModule($moduleInstance->getId());
    }

    static function getWidget($name) {
        if ($name == 'Payments' &&
            isPermitted('ModComments', 'DetailView') == 'yes') {
            require_once dirname(__FILE__) . '/widgets/DetailViewBlockComment.php';
            return (new ModComments_DetailViewBlockCommentWidget());
        }
        return false;
    }
}