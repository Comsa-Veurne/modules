<?php

namespace Backend\Modules\Catalog\Actions;

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Catalog\Engine\Model as BackendCatalogModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */
/**
 * This is the add-action, it will display a form to create a new product
 *
 * @author Tim van Wolfswinkel <tim@webleads.nl>
 */
class Add extends BackendBaseActionAdd
{
    /**
     * The product id
     *
     * @var    int
     */
    private $id;

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
     * All brands
     *
     * @var    array
     */
    private $brands;

    private $fieldLanguages = array();

    /**
     * Execute the actions
     */
    public function execute()
    {
        parent::execute();

        $this->loadData();
        $this->loadForm();

        $this->validateForm();

        $this->parse();
        $this->display();
    }

    /**
     * Load the item data
     */
    protected function loadData()
    {
        $this->id = $this->getParameter('product_id', 'int', null);

        if ($this->id != null)
        {
            $this->record = BackendCatalogModel::get($this->id);
        }

        // get categories
        $this->categories = BackendCatalogModel::getCategories(true);

        // Get all products grouped by categories
        $this->allProductsGroupedByCategories = BackendCatalogModel::getAllProductsGroupedByCategories();

        // get specifications
        $this->specifications = BackendCatalogModel::getSpecifications();

        // get brands
        $this->brands = BackendCatalogModel::getBrandsForDropdown();
    }

    /**
     * Load the form
     */
    protected function loadForm()
    {
        $this->frm = new BackendForm('add');

        // product fields
        $this->frm->addText('price', null, null, 'inputText price', 'inputTextError price');
        $this->frm->addDropdown('ballcolor', BackendCatalogModel::$ballColorArray);
        $this->frm->addCheckbox('allow_comments', true);
        $this->frm->addText('tags', null, null, 'inputText tagBox', 'inputTextError tagBox');
        $this->frm->addDropdown('related_products', $this->allProductsGroupedByCategories, null, true);

        foreach ((array)BackendModel::get('fork.settings')->get('Core', 'languages') as $key => $language)
        {
            $fieldTitle = $this->frm->addText("title_" . strtolower($language));
            $fieldText = $this->frm->addEditor("text_" . strtolower($language));
            $fieldBalltext = $this->frm->addText("balltext_" . strtolower($language));
            $fieldSummary = $this->frm->addEditor("summary_" . strtolower($language));

            $this->fieldLanguages[$key]["key"] = $key;
            $this->fieldLanguages[$key]["language"] = $language;
            $this->fieldLanguages[$key]["title"] = $fieldTitle->parse();
            $this->fieldLanguages[$key]["text"] = $fieldText->parse();
            $this->fieldLanguages[$key]["summary"] = $fieldSummary->parse();
            $this->fieldLanguages[$key]["balltext"] = $fieldBalltext->parse();
        }
        $this->frm->addCheckbox('frontpage', true);
        $this->frm->addCheckbox('contact', true);
        $this->frm->addDropdown('category_id', $this->categories, \SpoonFilter::getGetValue('category', null, null, 'int'));
        $this->frm->addDropdown('brand_id', $this->brands);
        $this->frm->getField('brand_id')->setDefaultElement('');

        $specificationsHTML = array();

        $first = true;
        // specifications
        foreach ($this->specifications as $specification)
        {
            $languages = array();
            $fields = array();

            // parse specification into template
            $this->tpl->assign('id', $specification['id']);
            $this->tpl->assign('label', $specification['title']);
            $this->tpl->assign('spec', true);

            //--Loop the languages
            foreach ((array)BackendModel::get('fork.settings')->get('Core', 'languages') as $key => $language)
            {
                $specificationName = 'specification' . $specification['id'] . '_' . $language;

                $specificationText = $this->frm->addText($specificationName);
                $specificationHTML = $specificationText->parse();

                $fields[] = array('field' => $specificationHTML);
                $languages[] = array('language' => $language);
            }

            $this->tpl->assign('fields', $fields);

            //--Check first loop
            if ($first == false)
            {
                $languages = array();
            }
            $this->tpl->assign('languages', $languages);

            $specificationsHTML[]['specification'] = $this->tpl->getContent(BACKEND_MODULES_PATH . '/' . $this->getModule() . '/Layout/Templates/Specification.tpl');

            $first = false;
        }

        $this->tpl->assign('specifications', $specificationsHTML);

        // meta
        $this->meta = new BackendMeta($this->frm, null, 'title_nl', true);
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
        if ($url404 != $url)
        {
            $this->tpl->assign('detailURL', SITE_URL . $url);
        }

        $this->tpl->assign('fieldLanguages', $this->fieldLanguages);
    }

    /**
     * Validate the form
     */
    protected function validateForm()
    {
        if ($this->frm->isSubmitted())
        {
            $this->frm->cleanupFields();

            // validation
            $fields = $this->frm->getFields();

            // required fields
            $fields['category_id']->isFilled(BL::err('FieldIsRequired'));
            if ($fields['category_id']->getValue() == 'no_category')
            {
                $fields['category_id']->addError(BL::err('FieldIsRequired'));
            }

            // validate meta
            $this->meta->validate();

            if ($this->frm->isCorrect())
            {
                // build the item
                $item['language'] = BL::getWorkingLanguage();
                $item['price'] = $fields['price']->getValue();
                $item['summary'] = $fields['summary_nl']->getValue();
                $item['text'] = $fields['text_nl']->getValue();
                $item['allow_comments'] = $fields['allow_comments']->getChecked() ? 'Y' : 'N';
                $item['num_comments'] = 0;
                $item['sequence'] = BackendCatalogModel::getMaximumSequence() + 1;
                $item['category_id'] = $fields['category_id']->getValue();
                $item['brand_id'] = $fields['brand_id']->getValue();
                $item['meta_id'] = $this->meta->save();
                $item['ballcolor'] = $fields['ballcolor']->getValue();
                $item['frontpage'] = $fields['frontpage']->getChecked();
                $item['contact'] = $fields['contact']->getChecked();

                // insert it
                $item['id'] = BackendCatalogModel::insert($item);

                //--Add the languages
                foreach ((array)BackendModel::get('fork.settings')->get('Core', 'languages') as $key => $language)
                {
                    $itemLanguage = array();
                    $itemLanguage['id'] = $item['id'];
                    $itemLanguage['language'] = $language;
                    $itemLanguage['title'] = $this->frm->getField('title_' . $language)->getValue();
                    $itemLanguage['text'] = $this->frm->getField('text_' . $language)->getValue();
                    $itemLanguage['summary'] = $this->frm->getField('summary_' . $language)->getValue();
                    $itemLanguage['url'] = BackendCatalogModel::getURLLanguage($this->frm->getField('title_' . $language)->getValue(),null,$language);
                    $itemLanguage['balltext'] = $this->frm->getField('balltext_' . $language)->getValue();

                    BackendCatalogModel::insertLanguage($itemLanguage);
                }

                $specificationArray = array();

                // loop trough specifications and insert values
                foreach ($this->specifications as $specification)
                {
                    // build the specification
                    $specificationArray['product_id'] = $item['id'];
                    $specificationArray['specification_id'] = $specification['id'];
                    foreach ((array)BackendModel::get('fork.settings')->get('Core', 'languages') as $key => $language)
                    {

                        $field = 'specification' . $specification['id'].'_' . $language;

                        // check if there is an value
                        if ($fields[$field]->getValue() != null)
                        {
                            $specificationArray['value'] = $fields[$field]->getValue();
                            $specificationArray['language'] = $language;

                            // insert specification with product id and value
                            BackendCatalogModel::insertSpecificationValue($specificationArray);
                        }
                    }
                }

                // save the tags
                BackendTagsModel::saveTags($item['id'], $fields['tags']->getValue(), $this->URL->getModule());

                // save the related products
                BackendCatalogModel::saveRelatedProducts($item['id'], $this->frm->getField('related_products')->getValue());

                // add search index
                BackendSearchModel::saveIndex($this->getModule(), $item['id'], array('title' => $this->frm->getField('title_nl')->getValue(), 'summary' => $this->frm->getField('summary_nl')->getValue(), 'text' => $this->frm->getField('text_nl')->getValue()));

                // trigger event
                BackendModel::triggerEvent($this->getModule(), 'after_add', $item);

                // redirect page
                $this->redirect(
                    BackendModel::createURLForAction('index') . '&report=added&highlight=row-' . $item['id']
                );
            }
        }
    }
}