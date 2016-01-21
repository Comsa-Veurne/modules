<?php

namespace Frontend\Modules\Addresses\Actions;

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Form as FrontendForm;
use Frontend\Modules\Addresses\Engine\Model as FrontendAddressesModel;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the Index-action, it will display the overview of addresses posts
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Index extends FrontendBaseBlock
{
    /**
     * The record data
     *
     * @var array
     */
    private $items;

    /**
     * @var frm
     */
    private $frm;

    /**
     * @var search
     */
    private $search, $searchGroups, $searchTopGroups, $lat, $lng;

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        $this->addJS('https://maps.googleapis.com/maps/api/js?sensor=false', true, false);
        $this->addJS('/src/Frontend/Modules/Addresses/Js/markerclusterer.js', true, false);
        $this->addJS('/src/Frontend/Modules/Addresses/Js/bootstrap.min.js', true, false);
        $this->addJS('/src/Frontend/Modules/Addresses/Js/bootstrap-multiselect.js', true, false);

        $this->addCSS('/src/Frontend/Modules/Addresses/Layout/Css/bootstrap-multiselect.css', true, false);
        $this->addCSS('/src/Frontend/Modules/Addresses/Layout/Css/Addresses.css', true, false);
        //--Get search
        $this->search = urldecode(\SpoonFilter::getGetValue("search", null, ""));
        if ($this->search != "") {
            $this->lat = urldecode(\SpoonFilter::getGetValue("lat", null, ""));
            $this->lng = urldecode(\SpoonFilter::getGetValue("lng", null, ""));
        }
        $this->addJSData('search', $this->search);
        $this->addJSData('lat', $this->lat);
        $this->addJSData('lng', $this->lng);
        $this->searchGroups = (\SpoonFilter::getGetValue("groups", null, array(), 'array'));
        //$this ->searchTopGroups = (\SpoonFilter::getGetValue("topgroups", null, array(),'array'));

        $this->loadTemplate();

        $this->loadData();

        $this->loadForm();

        $this->parse();
    }

    /**
     * Load the data
     */
    protected function loadData()
    {
//        if ($this->search != "")
//        {
        $this->items = FrontendAddressesModel::getAllAddresses($this->search, $this->searchGroups /*array_unique(array_merge($this->searchGroups, $this->searchTopGroups))*/, $this->lat, $this->lng);
//        }
//        else
//        {
//            $this->items = FrontendAddressesModel::getAllAddresses();
//        }

        //--Check if map has to be centered
        if ($this->search != "") {
            $this->addJSData('centerMap', 1);
        } else {
            $this->addJSData('centerMap', 0);
        }

        //--Fields for the jsdata
        $jsData = array();
        $fields = array("id", "name", "firstname", "address", "zipcode", "city", "country", "phone", "fax", "website",
            "lat", "lng");

        //--Check if items are not empty
        if (!empty($this->items)) {
            //--Loop the addresses
            foreach ($this->items as $row) {
                //--Create empty array
                $address = array();

                //--Loop the fields for the address
                foreach ($fields as $field) {
                    $address[$field] = $row[$field];
                }

                //--Add data to array for JS
                $jsData[] = $address;
            }
        }

        //--Add JSData
        $this->addJSData('items', $jsData);
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        $this->frm = new FrontendForm("search", null, "get", null, false);
        $this->frm->addText("search", $this->search, null, "form-control");
        $this->frm->addHidden("lat", null, null);
        $this->frm->addHidden("lng", null, null);
        $groups = FrontendAddressesModel::getAllGroupsForDropbox();
        $this->frm->addDropdown("groups", $groups, null, true);
//        $topGroups = FrontendAddressesModel::getAllTopGroups();
//        $topGroupsChk = array();
//        foreach($topGroups as $tgk => $tg){
//            $topGroupsChk[] = array('label' => $tg, 'value' => $tgk);
//        }
//        $this->frm->addMultiCheckbox("topgroups", $topGroupsChk, null);
    }

    /**
     * Parse the page
     */
    protected function parse()
    {

        if ($this->search != "")
            $this->tpl->assign('search', $this->search);

        //--Check if there are items found
        if (count($this->items) == 0) {
            $this->tpl->assign('countItems', false);
        } else {
            $this->tpl->assign('items', $this->items);
            $this->tpl->assign('countItems', count($this->items));
        }

        $this->frm->parse($this->tpl);
    }
}