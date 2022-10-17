<?php
/* * *******************************************************************************
 * The content of this file is subject to the Cashflow4You license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 * ****************************************************************************** */
//require_once('modules/Cashflow4You/models/Utils.php');

class Cashflow4You_Record_Model extends Vtiger_Record_Model {

	/**
	 * Function returns collor
	 */
	function getCollor( $name ) {
        $collor = null;
        if( $name == "paymentamount" ) {
            $db = PearDatabase::getInstance();
            $id = $this->getId();

            $select_res = $db->pquery("SELECT cashflow4you_paytype FROM  its4you_cashflow4you WHERE cashflow4youid=?", Array( $id ) );
            $paytype = $db->query_result($select_res, 0, 'cashflow4you_paytype');
            $utils = new Cashflow4You_Utils_Model();

            if( $paytype == "Outgoing" ) {
                $collor = $utils->getCollor("red");
            } else {
                $collor = $utils->getCollor("green");
            }
        }
        return $collor;
    }
}