<?php

namespace Backend\Modules\Addresses\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Modules\Addresses\Engine\Model as BackendAddressesModel;

class ImportFabricants extends BackendBaseActionIndex
{

    public function execute()
    {
        parent::execute();

        $txtText = \SpoonFile::getContent(BACKEND_MODULE_PATH . "/fabrikanten.txt");
        $arrText = explode("\n", $txtText);

        $arrShop = array();
        $arrShops = array();
        $arrCompanyNames = array();
        foreach ($arrText as $intKey => $strValue) {
            //--Check for phone
            $strPosTelephone = strpos($strValue, "Tel.:");
            if ($strPosTelephone !== false) {
                $arrShop["phone"] = trim(str_replace("Tel.:", "", substr($strValue, $strPosTelephone)));
            }

            //--Check for fax
            $strPosFax = strpos($strValue, "Fax:");
            if ($strPosFax !== false) {
                $arrShop["fax"] = trim(str_replace("Fax:", "", substr($strValue, $strPosFax)));
            }

            //--Find the e-mailaddress in the string
            if (!isset($arrShopFinal["email"])) {
                preg_match("/[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})/i", $strValue, $matchesEmail);
                if (!empty($matchesEmail)) {
                    $arrShop["email"] = $matchesEmail[0];
                }
            }

            //--Find the website address
            if (!isset($arrShopFinal["website"])) {
                preg_match("/www\.[a-zA-Z0-9-]+\.[a-z]{2,7}/i", $strValue, $matchesWebsite);
                if (!empty($matchesWebsite)) {
                    $arrShop["website"] = $matchesWebsite[0];
                }
            }

            //--Check the value
            if ($strValue != "" && !in_array($strValue, $arrCompanyNames) && $arrText[$intKey + 1] == "" && $arrText[$intKey + 2] == "" && empty($matchesWebsite)) {
                //--Check if shop is empty
                if (!empty($arrShop)) {
                    //--Add shop to shops-array
                    $arrShops[] = $arrShop;
                }

                $arrCity = explode(" ", $arrText[$intKey + 4], 2);

                //--New shop
                $arrShop = array();
                $arrShop['company'] = $strValue;
                $arrShop['address'] = ucwords(strtolower($arrText[$intKey + 3]));
                $arrShop['zipcode'] = ucwords(strtolower($arrCity[0]));
                $arrShop['city'] = ucwords(strtolower($arrCity[1]));
                $arrShop['country'] = "BE";

                //--Split zipcode
                $arrCountry = explode("-", $arrShop['zipcode']);
                if (count($arrCountry) > 1) {
                    $arrShop['country'] = strtoupper($arrCountry[0]);
                }

                //--Add companyname to the values
                $arrCompanyNames[] = $strValue;
            }
        }

        //--Loop all the shops
        foreach ($arrShops as $row) {
            $meta = array();
            $meta["keywords"] = $row["company"];
            $meta["description"] = "import-address"; //$row["company"];
            $meta["title"] = $row["company"];

            $meta["url"] = $row["company"];

            //--Replace the values with utf8
            foreach ($row as &$value) {
                $value = utf8_encode($value);
            }

            //--Insert meta
            $row["meta_id"] = BackendAddressesModel::insertMeta($meta);

            //--Add address to the database
            $address_id = BackendAddressesModel::insert($row);

            //--Add address to group
            $address = array("address_id" => $address_id, "group_id" => 2);

            BackendAddressesModel::insertAddressToGroup($address);
        }
    }
}