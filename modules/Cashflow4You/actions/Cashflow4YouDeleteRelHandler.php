<?php
/* * *******************************************************************************
 * The content of this file is subject to the Cashflow4You license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 * ****************************************************************************** */

class Cashflow4YouDeleteRelHandler extends VTEventHandler {
    public function handleEvent($handlerType, $entityData) {
        $entityId = $entityData->getId();

        $db = PearDatabase::getInstance();
        $select_res = $db->pquery("SELECT relcrmid, relmodule  FROM  vtiger_crmentityrel WHERE crmid=?", array($entityId));
        $row = $db->num_rows($select_res);
        $i = 0;
        if ($row > 0) {
            while ($row = $db->fetch_row($select_res)) {
                $Relation[$i]["id"] = $row["relcrmid"];
                $Relation[$i++]["module"] = $row["relmodule"];
            }
            $cashflow_utils = new Cashflow4You_Relation_Model();
            foreach ($Relation AS $relation) {
                $cashflow_utils->updateSavedRelation($relation["module"], $relation["id"]);
            }
        }
    }
}
