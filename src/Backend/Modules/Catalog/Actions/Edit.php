<?php

namespace Backend\Modules\Catalog\Actions;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Catalog\Engine\Model as BackendCatalogModel;
use Backend\Modules\Media\Engine\Helper as BackendMediaHelper;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit-action, it will display a form with the product data to edit
 *
 * @author Tim van Wolfswinkel <tim@webleads.nl>
 */
class Edit extends BackendBaseActionEdit
{
    /**
     * All categories
     *
     * @var    array
     */
    private $categories;

    /**
     * Products grouped by categories
     *
     * @var    array
     */
    private $allProductsGroupedByCategories;

    /**
     * All specifications
     *
     * @var    array
     */
    private $specifications;

    /**
     * All products
     *
     * @var    array
     */
    private $products;

    /**
     * All related products
     *
     * @var    array
     */
    private $relatedProducts;

    /**
     * All brands
     *
     * @var    array
     */
    private $brands;

    private $fieldLanguages = array();
    private $media;

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        $this->loadData();
        $this->loadForm();

        $this->validateForm();

        $this->header->addJsData("Media", "mediaModule", (string)$this->getModule());
        $this->header->addJsData("Media", "mediaAction", (string)$this->getAction());
        $this->header->addJsData("Media", "mediaId", (int)$this->id);
        $this->header->addJsData("Media", "mediaType", (string)'product');
        $this->parse();
        $this->display();
    }

    /**
     * Load the item data
     */
    protected function loadData()
    {
        $this->id = $this->getParameter('id', 'int', null);

        if ($this->id == null || !BackendCatalogModel::exists($this->id)) {
            $this->redirect(
                BackendModel::createURLForAction('index') . '&error=non-existing'
            );
        }

        $this->record = BackendCatalogModel::get($this->id);

        $this->categories = (array)BackendCatalogModel::getCategories(true);
        $this->products = (array)BackendCatalogModel::getAll();
        $this->allProductsGroupedByCategories = (array)BackendCatalogModel::getAllProductsGroupedByCategories();
        $this->relatedProducts = (array)BackendCatalogModel::getRelatedProducts($this->id);
        $this->specifications = (array)BackendCatalogModel::getSpecifications();

        // get brands
        $this->brands = BackendCatalogModel::getBrandsForDropdown();
    }

    /**
     * Load the form
     */
    protected function loadForm()
    {
        // create form
        $this->frm = new BackendForm('edit');

        $this->frm->addText('price', $this->record['price'], null, 'inputText price', 'inputTextError price');
        $this->frm->addText('tags', BackendTagsModel::getTags($this->URL->getModule(), $this->record['id']), null, 'inputText tagBox', 'inputTextError tagBox');
        $this->frm->addDropdown('related_products', $this->allProductsGroupedByCategories, $this->relatedProducts, true);
        $this->frm->addCheckbox('allow_comments', ($this->record['allow_comments'] === 'Y' ? true : false));
        $this->frm->addCheckbox('frontpage', $this->record['frontpage']);
        $this->frm->addCheckbox('contact', $this->record['contact']);
        $this->frm->addDropdown('ballcolor', BackendCatalogModel::$ballColorArray, $this->record['ballcolor']);

        foreach ((array)BackendModel::get('fork.settings')->get('Core', 'languages') as $key => $language) {
            $productLanguage = BackendCatalogModel::getLanguage($this->id, $language);

            $fieldTitle = $this->frm->addText("title_" . strtolower($language), $productLanguage['title']);
            $fieldText = $this->frm->addEditor("text_" . strtolower($language), $productLanguage['text']);
            $fieldSummary = $this->frm->addEditor("summary_" . strtolower($language), $productLanguage['summary']);
            $fieldBalltext = $this->frm->addText("balltext_" . strtolower($language), $productLanguage['balltext']);

            $this->fieldLanguages[$key]["key"] = $key;
            $this->fieldLanguages[$key]["language"] = $language;
            $this->fieldLanguages[$key]["title"] = $fieldTitle->parse();
            $this->fieldLanguages[$key]["text"] = $fieldText->parse();
            $this->fieldLanguages[$key]["summary"] = $fieldSummary->parse();
            $this->fieldLanguages[$key]["balltext"] = $fieldBalltext->parse();
        }

        // categories
        $this->frm->addDropdown('category_id', $this->categories, $this->record['category_id']);

        $this->frm->addDropdown('brand_id', $this->brands, $this->record['brand_id']);

        $specificationsHTML = array();

        $first = true;

        // specifications
        foreach ($this->specifications as $specification) {
            $specificationName = 'specification' . $specification['id'];

            $languages = array();
            $fields = array();

            // parse specification into template
            $this->tpl->assign('id', $specification['id']);
            $this->tpl->assign('label', $specification['title']);
            $this->tpl->assign('spec', true);

            foreach ((array)BackendModel::get('fork.settings')->get('Core', 'languages') as $key => $language) {
                $value = BackendCatalogModel::getSpecificationValue($specification['id'], $this->record['id'], $language);

                // check if value is set
                $value = (isset($value['value']) ? $value['value'] : null);

                $specificationName = 'specification' . $specification['id'] . '_' . $language;

                $specificationText = $this->frm->addText($specificationName, $value);
                $specificationHTML = $specificationText->parse();

                $fields[] = array('field' => $specificationHTML);
                $languages[] = array('language' => $language);
            }

            $this->tpl->assign('fields', $fields);

            //--Check first loop
            if ($first == false) {
                $languages = array();
            }
            $this->tpl->assign('languages', $languages);

            $specificationsHTML[]['specification'] = $this->tpl->getContent(BACKEND_MODULES_PATH . '/' . $this->getModule() . '/Layout/Templates/Specification.tpl');
        }

        $this->tpl->assign('specifications', $specificationsHTML);

        // meta object
        $this->meta = new BackendMeta($this->frm, $this->record['meta_id'], 'title_nl', true);

        // set callback for generating a unique URL
        $this->meta->setUrlCallback('Backend\Modules\Catalog\Engine\Model', 'getURL', array($this->record['id']));

        //--Media
        $this->media = new BackendMediaHelper($this->frm, (string)$this->getModule(), (int)$this->id, (string)$this->getAction(), 'product');
    }

    /**
     * Parse the page
     */
    protected function parse()
    {
        parent::parse();

        // get url
        $url = BackendModel::getURLForBlock($this->URL->getModule(), 'detail');
        $url404 = BackendModel::getURL(404);

        // parse additional variables
        if ($url404 != $url) {
            $this->tpl->assign('detailURL', SITE_URL . $url);
        }

        $this->tpl->assign('product', $this->record);
        $this->tpl->assign('fieldLanguages', $this->fieldLanguages);
        //--Add media
        $this->tpl->assign('mediaItems', $this->media->getMediaItems());
    }

    /**
     * Validate the form
     */
    protected function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $this->frm->cleanupFields();

            // validation
            $fields = $this->frm->getFields();

            //            $fields['title']->isFilled(BL::err('FieldIsRequired'));
            //            $fields['summary']->isFilled(BL::err('FieldIsRequired'));
            $fields['category_id']->isFilled(BL::err('FieldIsRequired'));
            if ($fields['category_id']->getValue() == 'no_category') {
                $fields['category_id']->addError(BL::err('FieldIsRequired'));
            }

            // validate meta
            $this->meta->validate();
            //--Validate Media
            $this->media->validate();
            if ($this->frm->isCorrect()) {
                $item['id'] = $this->id;
                $item['language'] = BL::getWorkingLanguage();
                $item['price'] = $fields['price']->getValue();
                $item['category_id'] = $this->frm->getField('category_id')->getValue();
                $item['brand_id'] = $fields['brand_id']->getValue();
                $item['allow_comments'] = $this->frm->getField('allow_comments')->getChecked() ? 'Y' : 'N';
                $item['frontpage'] = $this->frm->getField('frontpage')->getChecked();
                $item['contact'] = $this->frm->getField('contact')->getChecked();
                $item['meta_id'] = $this->meta->save();
                $item['ballcolor'] = $fields['ballcolor']->getValue();

                BackendCatalogModel::update($item);
                $item['id'] = $this->id;

                //--Add the languages
                foreach ((array)BackendModel::get('fork.settings')->get('Core', 'languages') as $key => $language) {
                    $itemLanguage = array();
                    $itemLanguage['id'] = $item['id'];
                    $itemLanguage['language'] = $language;
                    $itemLanguage['title'] = $this->frm->getField('title_' . $language)->getValue();
                    $itemLanguage['summary'] = $this->frm->getField('summary_' . $language)->getValue();
                    $itemLanguage['text'] = $this->frm->getField('text_' . $language)->getValue();
                    $itemLanguage['url'] = BackendCatalogModel::getURLLanguage($this->frm->getField('title_' . $language)->getValue(), $item['id'], $language);
                    $itemLanguage['balltext'] = $this->frm->getField('balltext_' . $language)->getValue();

                    BackendCatalogModel::updateLanguage($itemLanguage, $language);
                }

                $specificationArray = array();

                // loop trough specifications and insert values
                foreach ($this->specifications as $specification) {
                    foreach ((array)BackendModel::get('fork.settings')->get('Core', 'languages') as $key => $language) {
                        $field = 'specification' . $specification['id'] . '_' . $language;

                        $specificationArray['value'] = $fields[$field]->getValue();
                        $specificationArray['language'] = $language;
                        $specificationArray['product_id'] = $item['id'];
                        $specificationArray['specification_id'] = $specification['id'];

                        // when specification value already exists. update value
                        if (BackendCatalogModel::existsSpecificationValue($item['id'], $specification['id'], $language) != false) {
                            // update specification with product id and value
                            BackendCatalogModel::updateSpecificationValue($specification['id'], $item['id'], $language, $specificationArray);
                        } else {
                            // when specification value doesnt exists, insert new value
                            BackendCatalogModel::insertSpecificationValue($specificationArray);
                        }
                    }
                }

                // save the tags
                BackendTagsModel::saveTags($item['id'], $fields['tags']->getValue(), $this->URL->getModule());

                // add search index
                BackendSearchModel::saveIndex($this->getModule(), $item['id'], array('title' => $this->frm->getField('title_nl')->getValue(), 'summary' => $this->frm->getField('summary_nl')->getValue(), 'text' => $this->frm->getField('text_nl')->getValue()));

                // save related projects
                BackendCatalogModel::saveRelatedProducts($item['id'], $this->frm->getField('related_products')->getValue(), $this->relatedProducts);

                // trigger event
                BackendModel::triggerEvent(
                    $this->getModule(), 'after_edit', $item
                );

                $this->redirect(
                    BackendModel::createURLForAction('index') . '&report=edited&highlight=row-' . $item['id']
                );
            }
        }
    }
}