<?php

namespace Frontend\Modules\Addresses\Actions;

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Addresses\Engine\Model as FrontendAddressesModel;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the Detail-action, it will display the overview of addresses posts
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Detail extends FrontendBaseBlock
{

    /**
     * The record data
     *
     * @var array
     */
    private $record;

    /**
     * Execute the action
     */
    public function execute()
    {
        $this->addJS('http://maps.google.com/maps/api/js?sensor=true', true, false);
        $this->addJS('/src/Frontend/Modules/Addresses/Js/bootstrap.min.js', true, false);
        $this->addJS('/src/Frontend/Modules/Addresses/Js/bootstrap-multiselect.js', true, false);
        $this->addCSS('/src/Frontend/Modules/Addresses/Layout/Css/Addresses.css', true, false);

        parent::execute();
        $this->loadTemplate();
        $this->loadData();
        $this->parse();
    }

    /**
     * Load the data
     */
    protected function loadData()
    {


        //--Check the params
        if ($this->URL->getParameter(0) === null) {
            $this->redirect(FrontendNavigation::getURL(404), 404);
        }

        //--Get record
        $this->record = FrontendAddressesModel::get($this->URL->getParameter(1));

        if (empty($this->record)) {
            $this->redirect(FrontendNavigation::getURL(404), 307);
        }
    }

    /**
     * Parse the page
     */
    protected function parse()
    {
        $this->header->setPageTitle($this->record['meta_title'], ($this->record['meta_title_overwrite'] == 'Y'));

        $this->header->addMetaDescription($this->record['company'] . " - " . $this->record['address'] . " " . $this->record['zipcode'] . " " . $this->record['city'] . " - Tel. " . $this->record['phone'], ($this->record['meta_description_overwrite'] == 'Y'));
        $this->header->addMetaKeywords($this->record['company'] . " - " . $this->record['address'] . " " . $this->record['zipcode'] . " " . $this->record['city'] . " - " . $this->record['phone'], ($this->record['meta_keywords_overwrite'] == 'Y'));

        $this->tpl->assign('item', $this->record);

        //--Add JSData
        $this->addJSData('item', $this->record);

        $this->addJSData('items', array($this->record));

        $this->tpl->assign('goback', htmlspecialchars($_SERVER['HTTP_REFERER']));
    }
}