<?php

require_once('modules/Products/Products.php');

class Arrivages_SaveStepOne_Action extends Vtiger_Save_Action {

    private $nbr_Articles = 0;
    private $IdArrivage;

    public function process(Vtiger_Request $request) {

        $db = PearDatabase::getInstance();

        $Nom_Arrivage = $request->get('nom');
        $serialnumber = $request->get('serialnumber');
        $codebarre = $request->get('codebarre');
        $Doc_title = "Arrivage_" . (date('Ymd'));
        $Num_Arrivage = $Nom_Arrivage . "-" . (date('ymd'));


        $ArrivageModel = Vtiger_Record_Model::getCleanInstance("Arrivages");
        $currentUserModel = Users_Record_Model::getCurrentUserModel();

        $ArrivageModel->set("name", $Num_Arrivage);
        $ArrivageModel->save();
        $arrivageid = $ArrivageModel->getId();

        $totalArticlesCount = $_REQUEST['totalArticlesCount'];

//    $monfichier = fopen('debug_session.txt', 'a+');
//    fputs($monfichier, "\n" . ' id 1:  ' .$apprenantid);
//    fclose($monfichier);
        $sequence_no = 1;
        try {
            for ($i = 1; $i <= $totalArticlesCount; $i++) {
                $codebarre = $_REQUEST['codebarre' . $i];
                $nomproduit = $_REQUEST['nomproduit' . $i];
                $qty = $_REQUEST['qty' . $i];

                $query_products = 'SELECT productid,qtyinstock
            FROM vtiger_products
            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid =vtiger_products.productid            
            WHERE codebarre=? and deleted = 0';

                $params_products = array($codebarre);
                $result_products = $db->pquery($query_products, $params_products);
                $num_rows_products = $db->num_rows($result_products);

                if ($num_rows_products > 0) {
                    $productid = $db->query_result($result_products, 0, 'productid');
                    $qtyinstock = $db->query_result($result_products, 0, 'qtyinstock');

                    echo "test 01 <br/>";
                    $qtyinstock_ajour = $qtyinstock + $qty;
                    echo "qtyinstock_ajour <br/>" . $qtyinstock_ajour;
                    $query = "UPDATE vtiger_products SET qtyinstock = ? WHERE productid =?";
                    $qparams = array($qtyinstock_ajour, $productid);
                    $db->pquery($query, $qparams);

                    $sql1 = "INSERT INTO vtiger_history_arrivages (arrivageid,productid,type,oldqty,newqty) VALUES (?, ?, ?, ?, ?)";
                    $params1 = array($arrivageid, $productid, 'MISEAJOUR', $qtyinstock, $qtyinstock_ajour);
                    $db->pquery($sql1, $params1);
                } else {
                    $focus = new Products();
                    $focus->mode = 'create';

                    $focus->column_fields['productname'] = $nomproduit;
                    $focus->column_fields['codebarre'] = $codebarre;
                    $focus->column_fields['qtyinstock'] = $qty;


                    $focus->save("Products");
                    $return_id = $focus->id;
                    $sql1 = "INSERT INTO vtiger_history_arrivages (arrivageid,productid,type,oldqty,newqty) VALUES (?, ?, ?, ?, ?)";
                    $params1 = array($arrivageid, $return_id, 'AJOUT', 0, $qty);
                    $db->pquery($sql1, $params1);
                }
            }
        } catch (Exception $ex) {
            header('Location: index.php?module=Arrivages&view=Wizard&Error=1');
            exit();
        }

        header('Location: index.php?module=Arrivages&view=Detail&record=' . $arrivageid);
        exit();
    }

    private function getUploadedFile($DocId) {
        $documentRecordModel = Vtiger_Record_Model::getInstanceById($DocId, 'Documents');
        $fileDetails = $documentRecordModel->getFileDetails();
        return $fileDetails['path'] . $fileDetails['attachmentsid'] . "_" . $fileDetails['name'];
    }

    private function getArticleFromCSVFile($file) {
        $CsvFile = @fopen($file, "r");
        while (($line = fgetcsv($CsvFile)) !== FALSE) {
            print_r($line);
        }
        fclose($CsvFile);

        die(print_r($CsvFile));
    }

    private function addProduct($InfoArray) {
        CRMEntity::getInstance("Products");
        $Produit = new Products();
        $Produit->column_fields["productname"] = $InfoArray[6];
        $Produit->column_fields["productcategory"] = $InfoArray[4];
        $Produit->column_fields["manufacturer"] = $InfoArray[5];
        $Produit->column_fields["mfr_part_no"] = $InfoArray[6];
        $Produit->column_fields["qtyinstock"] = 1;
        $Produit->save("Products");
        return $Produit->id;
    }

    private function updateProduct($productId) {

        $productStock = getPrdQtyInStck($productId);
        $quantity = intval($productStock) + 1;
        updateProductQty($productId, $quantity);
    }

    private function getDayArrivageCount() {
        $db = PearDatabase::getInstance();
        $sql = "SELECT count(*) "
                . "FROM vtiger_arrivages "
                . "INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_arrivages.arrivagesid "
                . "where DATE(createdtime) = DATE(NOW()) ; ";
        $result = $db->pquery($sql);
        return $db->query_result($result, 0);
    }

}
