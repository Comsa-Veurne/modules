<?php

namespace Frontend\Modules\Addresses\Engine;

use Frontend\Core\Engine\Language as FL;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Symfony\Component\Intl\Intl as Intl;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the addresses module
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Model
{
    /**
     * Get all the categories
     *
     * @return array
     */
    public static function getAllGroups()
    {
        $return = (array)FrontendModel::getContainer()->get('database')->getRecords(
            'SELECT g.id, g.title, m.url, COUNT(a.address_id) AS total,m.title AS meta_title,m.title_overwrite AS meta_title_overwrite, m.description AS meta_description,m.description_overwrite AS meta_description_overwrite,m.keywords AS meta_keywords,m.keywords_overwrite AS meta_keywords_overwrite, m.data AS meta_data
			 FROM addresses_groups AS g
			 INNER JOIN meta AS m ON g.meta_id = m.id
			 LEFT JOIN addresses_in_group AS a ON a.group_id = g.id
			 GROUP BY g.id
			 ORDER BY g.sequence ASC', array('N'), 'id'
        );

        //--Get link for the categories
        $categoryLink = FrontendNavigation::getURLForBlock('Addresses', 'Group');

        // loop items and unserialize
        foreach ($return as &$row) {
            $row['group_full_url'] = $categoryLink . '/' . $row['url'];

            if (isset($row['meta_data'])) {
                $row['meta_data'] = @unserialize($row['meta_data']);
            }
        }

        return $return;
    }

    /**
     *
     * Get all the users for a group
     *
     * @param $id
     *
     * @return array()
     */

    public static function getAllAddresses($search = "", $groupSearch = null, $lat, $lon, $orderby = false)
    {
        $return = null;
        $strOr = "";
        $strWhere = "";
        $strOrderBy = "";

        if ($orderby != false) {
            $strOrderBy = " ORDER BY " . $orderby;
        }

        //--Check if search is filled in
        /*if ($search != "")
        {*/
        $returnTemp = array();
        //--Search fields
        $strDistance = "";
        if ($lat != "" && $lon != "") {
            //$strDistance =",111.045*haversine(lat,lng,".$lat.", ".$lon.") AS distance";
//            $fields = array("company", "address", "zipcode", "city", "fax", "website", "email", "zipcodes");
//            $arrSearch = explode(" ", $search);
//
//            //--Reset or
//            $strOr = '';
            //$str/Where .= " AND (";
//
//            //--Loop all the field to search in
//            for ($i = 0; $i < count($arrSearch); $i++) {
//                $rowSearch = $arrSearch[$i];
//                foreach ($fields as $field) {
//                    $strWhere .= $strOr . " $field LIKE '%" . $rowSearch . "%'";
//                    $strOr = " OR ";
//                }
//                //if(($i + 1) < count($arrSearch)) {
//                //$strOr = ") AND (";
//                //}
//            }
            $strWhere = " AND (";
            //$strWhere .= "111.045*haversine(lat,lng," . $lat . ", " . $lon . ") < 20";
            $strWhere .= "(111.045* (DEGREES(ACOS(
                                              COS(RADIANS(lat)) *
                                              COS(RADIANS(" . $lat . ")) *
                                              COS(RADIANS(" . $lon . ") - RADIANS(lng)) +
                                              SIN(RADIANS(lat)) * SIN(RADIANS(" . $lat . "))))) < 20)";
            $strWhere .= ")";

        } else {
            $strWhere = "";
        }

        if ($groupSearch != null) {
            $strHaving = " HAVING ";
            for ($i = 0; $i < count($groupSearch); $i++) {
                $group = $groupSearch[$i];
                $strHaving .= "FIND_IN_SET(" . $group . ", groups) > 0";
                if (($i + 1) < count($groupSearch)) {
                    $strHaving .= " AND ";
                }
            }
        } else {
            $strHaving = "";
        }

//        echo $strWhere; echo "<br />";
//        echo $strHaving; echo "<br />";
//        echo "SELECT a.*, m.url, m.data as meta_data,
//                     (SELECT GROUP_CONCAT(aig.group_id SEPARATOR ',') FROM addresses_in_group aig WHERE aig.address_id = a.id GROUP BY aig.address_id) as groups
//													FROM addresses AS a
//													INNER JOIN meta AS m ON m.id = a.meta_id
//													WHERE a.hidden=? $strWhere $strHaving  $strOrderBy";
        $returnSearch = FrontendModel::getContainer()->get('database')->getRecords(
            "SELECT a.*, m.url, m.data as meta_data,
                     (SELECT GROUP_CONCAT(aig.group_id SEPARATOR ',') FROM addresses_in_group aig WHERE aig.address_id = a.id GROUP BY aig.address_id) as groups
                     $strDistance
													FROM addresses AS a
													INNER JOIN meta AS m ON m.id = a.meta_id
													WHERE a.hidden=? $strWhere $strHaving  $strOrderBy", array('N')
        );

        /* if (!empty($returnSearch))
         {
             $returnTemp = array_merge($returnTemp, $returnSearch);
         }*/


        if (!empty($returnSearch)) {

            foreach ($returnSearch as $key => $row) {
                $return[$row['id']] = $row;
            }
        }
        /* }
         else
         {
             $return = FrontendModel::getContainer()->get('database')->getRecords(
                 "SELECT a.*, m.url, m.data as meta_data
                                                     FROM addresses AS a
                                                     INNER JOIN meta AS m ON m.id = a.meta_id
                                                     WHERE a.hidden=?", array('N')
             );
         }*/

        //--Get the detail link
        $detailLink = FrontendNavigation::getURLForBlock('Addresses', 'Detail');

        //--Get folders
        $folders = FrontendModel::getThumbnailFolders(FRONTEND_FILES_PATH . '/Addresses/Images', true);

        //--Get all the countries
        $countries = Intl::getRegionBundle()->getCountryNames(FRONTEND_LANGUAGE);

        if (!empty($return)) {
            // loop items and unserialize
            foreach ($return as &$row) {
                $row['groups'] = FrontendModel::getContainer()->get('database')->getRecords(
                    "SELECT title FROM addresses_in_group aig INNER JOIN addresses_groups ag ON aig.group_id = ag.id WHERE aig.address_id = ?", array($row['id'])
                );
                //--Get countryname
                $row['countryname'] = $countries[$row['country']];

                //--Replace characters
                $row['company'] = self::replaceCharacters($row['company']);

                //--Create detail link
                $row['full_url'] = $detailLink . '/' . $row['url'];

                //--Check meta data
                if (isset($row['meta_data'])) {
                    $row['meta_data'] = @unserialize($row['meta_data']);
                }

                // image?
                if (isset($row['image'])) {
                    //--Loop als the folders
                    foreach ($folders as $folder) {
                        $row['image_' . $folder['dirname']] = $folder['url'] . '/' . $folder['dirname'] . '/' . $row['image'];
                    }
                }
            }
            return $return;
        }
    }

    /**
     *
     * Get all the users for a group
     *
     * @param $id
     *
     * @return array()
     */
    public static function getAddressesForGroup($id, $search = "", $orderby = false)
    {

        $strOr = "";
        $strWhere = "";
        $strOrderBy = "";

        if ($orderby != false) {
            $strOrderBy = " ORDER BY " . $orderby;
        }

        //--Check if search is filled in
        if ($search != "") {
            $returnTemp = array();
            //--Search fields
            $fields = array("company", "address", "zipcode", "city", "fax", "website", "email");
            $arrSearch = explode(" ", $search);

            foreach ($arrSearch as $rowSearch) {
                //--Reset or
                $strOr = '';
                $strWhere = " AND (";

                //--Loop all the field to search in
                foreach ($fields as $field) {
                    $strWhere .= $strOr . " $field LIKE '%" . $rowSearch . "%'";
                    $strOr = " OR ";
                }

                $strWhere .= ")";

                $returnSearch = FrontendModel::getContainer()->get('database')->getRecords(
                    "SELECT i.address_id, a.name, a.firstname, a.company, a.address, a.zipcode, a.city, a.country, a.phone, a.fax, a.email, a.website, a.lat, a.lng, a.contact, a.assort, a.vat, a.size, a.open, a.closed, a.visit, m.url, m.data as meta_data
													FROM addresses_in_group AS i
													INNER JOIN addresses AS a ON a.id = i.address_id
													INNER JOIN meta AS m ON m.id = a.meta_id
													WHERE i.group_id = ? AND a.hidden=? $strWhere $strOrderBy", array($id, 'N')
                );

                if (!empty($returnSearch)) {
                    $returnTemp = array_merge($returnTemp, $returnSearch);
                }
            }

            if (!empty($returnTemp)) {

                foreach ($returnTemp as $key => $row) {
                    $return[$row['address_id']] = $row;
                }
            }
        } else {
            $return = FrontendModel::getContainer()->get('database')->getRecords(
                "SELECT i.address_id, a.name, a.firstname, a.company, a.address, a.zipcode, a.city, a.country, a.phone, a.fax, a.email, a.website, a.lat, a.lng, a.contact, a.assort, a.vat, a.size, a.open, a.closed, a.visit, m.url, m.data as meta_data
													FROM addresses_in_group AS i
													INNER JOIN addresses AS a ON a.id = i.address_id
													INNER JOIN meta AS m ON m.id = a.meta_id
													WHERE i.group_id = ? AND a.hidden=? $strWhere $strOrderBy", array($id, 'N')
            );
        }

        //--Get link for the categories
        $detailLink = FrontendNavigation::getURLForBlock('Addresses', 'Detail');

        //--Get folders
        $folders = FrontendModel::getThumbnailFolders(FRONTEND_FILES_PATH . '/addresses/images', true);

        if (!empty($return)) {
            // loop items and unserialize
            foreach ($return as &$row) {

                $row['company'] = self::replaceCharacters($row['company']);

                $row['full_url'] = "/" . strtolower(FRONTEND_LANGUAGE) . "/" . FL::getAction('Adressen') . '/' . FL::getAction('detail') . '/' . $row['url'];

                if (isset($row['meta_data'])) {
                    $row['meta_data'] = @unserialize($row['meta_data']);
                }

                // image?
                if (isset($row['image'])) {
                    foreach ($folders as $folder) {
                        $row['image_' . $folder['dirname']] = $folder['url'] . '/' . $folder['dirname'] . '/' . $row['image'];
                    }
                }
            }
        }

        return $return;
    }

    /**
     * Get an item
     *
     * @param string $URL The URL for the item.
     *
     * @return array
     */
    public static function get($URL)
    {
        $return = (array)FrontendModel::getContainer()->get('database')->getRecord(
            'SELECT i.*, l.*,
			 m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
			 m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
			 m.title AS meta_title, m.title_overwrite AS meta_title_overwrite,
			 m.url,
			 m.data AS meta_data
			 FROM addresses AS i
			 INNER JOIN addresses_lang AS l ON i.id = l.id AND l.language = ?
			 INNER JOIN meta AS m ON i.meta_id = m.id
			 WHERE i.hidden = ? AND m.url = ?
			 LIMIT 1', array(FRONTEND_LANGUAGE, 'N', (string)$URL)
        );

        if (empty($return)) {
            return array();
        }

        // unserialize
        if (isset($return['meta_data'])) {
            $return['meta_data'] = @unserialize($return['meta_data']);
        }

        // image?
        if (isset($return['image'])) {
            $folders = FrontendModel::getThumbnailFolders(FRONTEND_FILES_PATH . '/Addresses/Images', true);

            foreach ($folders as $folder) {
                $return['image_' . $folder['dirname']] = $folder['url'] . '/' . $folder['dirname'] . '/' . $return['image'];
            }
        }

        if (isset($return["country"])) {


            //--Get country
            $return["country"] = Intl::getRegionBundle()->getCountryName($return['country'], FRONTEND_LANGUAGE);
        }

        $return['company'] = self::replaceCharacters($return['company']);

        // return
        return $return;
    }

    public static function getGroupsForAddress($id)
    {
        $return = (array)FrontendModel::getContainer()->get('database')->getRecords(
            'SELECT g.id, g.title, m.url, m.title AS meta_title,m.title_overwrite AS meta_title_overwrite, m.data AS meta_data
			 FROM addresses_in_group AS ag
			 INNER JOIN addresses_groups AS g ON ag.group_id = g.id
			 INNER JOIN meta AS m ON g.meta_id = m.id
			 WHERE ag.address_id = ?
			 ORDER BY g.sequence ASC', array($id), 'id'
        );

        //--Get link for the categories
        $categoryLink = FrontendNavigation::getURLForBlock('Addresses', 'Group');

        // loop items and unserialize
        foreach ($return as &$row) {
            $row['group_full_url'] = $categoryLink . '/' . $row['url'];

            if (isset($row['meta_data'])) {
                $row['meta_data'] = @unserialize($row['meta_data']);
            }
        }

        return $return;
    }

    public static function replaceCharacters($string)
    {
        $string = str_ireplace("BVBA", "", $string);
        $string = str_ireplace("NV ", "", $string);
        $string = str_ireplace(" NV", "", $string);

        return $string;
    }

    public static function getUrlForId($id)
    {
        $return = (array)FrontendModel::getContainer()->get('database')->getVar(
            'SELECT m.url
			 FROM addresses AS a
			 INNER JOIN meta AS m on m.id = a.meta_id
			 WHERE a.id = ?', array($id)
        );

        return $return[0];
    }

    public static function getAllGroupsForDropbox()
    {

        $groups = FrontendModel::getContainer()->get('database')->getPairs("SELECT i.id, i.title
														FROM addresses_groups AS i
														/*WHERE i.id != 1 AND i.id != 2*/");

        return $groups;
    }

    public static function getAllTopGroups()
    {

        $groups = FrontendModel::getContainer()->get('database')->getPairs("SELECT i.id, i.title
														FROM addresses_groups AS i
														/*WHERE i.id = 1 OR i.id = 2*/");

        return $groups;
    }

}