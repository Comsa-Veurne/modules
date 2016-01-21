<?php

namespace Backend\Modules\Addresses\Actions;

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Model as BackendModel;

class UpdateShops extends BackendBaseActionIndex
{

    public function execute()
    {
        parent::execute();

        $txtText = \SpoonFile::getContent(BACKEND_MODULE_PATH . "/meubelwinkels.txt");
        $arrText = explode("\n", $txtText);

        $strShop = "";
        $arrShops = array();
        $arrShopsFinal = array();
        $arrElements = array("company", "phone", "zipcode", "city", "address", "contact", "email", "website", "fax", "vat", "assort", "m�", "open", "gesloten", "visit");
        $arrElementsDash = array("assort", "m�", "open", "gesloten", "visit");
        foreach ($arrText as $line) {

            //--Check if the line is only a zipcode or pagenumbers (1000 or 52 53)
            if (preg_match("/^\d+$/", $line) || preg_match("/^[0-9 ]+$/", $line)) {
                continue;
            }

            //--Search for T : in the line (this is the first line of the address)
            if (strpos($line, "T :") !== false) {
                //--If line is not empty, add it to the array
                if (!empty($strShop)) {
                    $arrShops[] = $strShop;
                }
                $strShop = "";
            }
            //--Add the line + add a marker [LINE]
            $strShop .= $line . "[LINE]";
        }
        //--Loop all the shops
        foreach ($arrShops as $shop) {
            //--Explode the shop with [LINE]
            $arrShop = explode("[LINE]", $shop);

            $arrShopFinal = array();

            //--Get the phone number and name of the shop
            $strPosTelephone = strpos($arrShop[0], "T :");

            //--Create array
            $arrShopFinal["company"] = ucwords(mb_strtolower(substr($arrShop[0], 0, $strPosTelephone)));
            $arrShopFinal["phone"] = trim(str_replace("T :", "", substr($arrShop[0], $strPosTelephone)));

            //--Get the address
            $strAddress = ucwords(mb_strtolower($arrShop[1]));

            //--Get position of the space
            $strPosSpaceZipcode = strpos($strAddress, " ");

            //--Add the zipcode
            $arrShopFinal["zipcode"] = substr($strAddress, 0, $strPosSpaceZipcode);

            //--Alter the address-string
            $strAddress = substr($strAddress, $strPosSpaceZipcode);

            //--Search comma
            $strPosCommaCity = strpos($strAddress, ",");

            //--Add the city
            $arrShopFinal["city"] = substr($strAddress, 0, $strPosCommaCity);

            //--Add the address
            $arrShopFinal["address"] = trim(substr($strAddress, $strPosCommaCity + 1));

            //--Unset first and second item
            unset($arrShop[0]);
            unset($arrShop[1]);

            //--Loop the shop
            foreach ($arrShop as $key => $row) {
                //--Get the contact
                if (!isset($arrShopFinal["contact"]) && strpos($row, "contact:") !== false) {
                    $arrShopFinal["contact"] = ucwords(mb_strtolower(trim(substr($row, 8))));
                }

                //--Find the e-mailaddress in the string
                if (!isset($arrShopFinal["email"])) {
                    preg_match("/[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})/i", $row, $matches);
                    if (!empty($matches)) {
                        $arrShopFinal["email"] = $matches[0];
                    }
                }

                //--Find the website address
                if (!isset($arrShopFinal["website"])) {
                    preg_match("/www\.[a-zA-Z0-9-]+\.[a-z]{2,7}/i", $row, $matches);
                    if (!empty($matches)) {
                        $arrShopFinal["website"] = $matches[0];
                    }
                }

                //--Find the fax
                if (!isset($arrShopFinal["fax"])) {
                    preg_match("/F: ([\\s0-9]+)/i", $row, $matches);

                    if (!empty($matches)) {
                        $arrShopFinal["fax"] = $matches[1];
                    }
                }

                //--Find the VAT
                if (!isset($arrShopFinal["btw"])) {
                    preg_match("/BTW : ([A-Z]{2}[\\s]*[0-9-\\.\\s]+)/i", $row, $matches);

                    if (!empty($matches)) {
                        $arrShopFinal["vat"] = $matches[1];
                    }
                }

                //--Check if the dash is for a numeric value (not  - assort:)
                preg_match("/([0-9]{1}[\\s]-[\\s][0-9]{1})/i", $row, $matches);

                if (!empty($matches)) {
                    foreach ($matches as $match) {
                        $strMatchReplace = str_replace(" - ", "-", $match);
                        $row = str_replace($match, $strMatchReplace, $row);
                    }
                }

                //--Split the text with " - ";
                $arrDashes = explode(" - ", $row);
                //--Check if there are elements
                if (!empty($arrDashes)) {
                    //--Loop the different pieces
                    foreach ($arrDashes as $dash) {
                        //--Loop the elements that are possible for the dash-element
                        foreach ($arrElementsDash as $element) {
                            //--Check if the element is found, if true, add the element to the array
                            if (strpos($dash, $element . ":") !== false) {
                                $arrShopFinal[$element] = str_replace($element . ":", "", $dash);
                            }
                        }
                    }
                }
            }

            //--Check if all elements are filled in
            foreach ($arrElements as $element) {
                //--If key not exists, add an empty value to it
                if (!isset($arrShopFinal[$element])) {
                    //--Fill in empty value
                    $arrShopFinal[$element] = "";
                } else {
                    //--Replace to utf8
                    $arrShopFinal[$element] = trim($arrShopFinal[$element]);

                    //--Replace ? to '
                    $arrShopFinal[$element] = str_replace("?", "'", $arrShopFinal[$element]);
                }
            }

            //--Replace m� by size (for the database)
            $arrShopFinal["size"] = $arrShopFinal["m�"];
            unset($arrShopFinal["m�"]);

            //--Replace gesloten by closed (for the database)
            $arrShopFinal["closed"] = $arrShopFinal["gesloten"];
            unset($arrShopFinal["gesloten"]);

            $arrShopFinal["country"] = substr($arrShopFinal["vat"], 0, 2);
            $arrShopFinal["country"] = $arrShopFinal["country"] == "" ? "BE" : $arrShopFinal["country"];

            //--Add final shop to all shops
            $arrShopsFinal[] = $arrShopFinal;
        }

        print "<pre>";

        //--Loop all the shops
        foreach ($arrShopsFinal as $row) {

            $arrId = (array)BackendModel::getContainer()->get('database')->getVar('SELECT i.id
			 FROM addresses AS i
			 WHERE i.email = ? AND i.address = ? ', array($row['email'], $row['address']));

            $id = (int)$arrId[0];

            if ($id > 0) {

                $arrUpdate = array("contact" => $row['contact']);
                BackendModel::getContainer()->get('database')->update('addresses', $arrUpdate, 'id = ?', (int)$id);

            } else {
                echo $id;
                print_r($row);
            }
        }

        die();
    }
}