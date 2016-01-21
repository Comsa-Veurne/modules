<?php

namespace Backend\Modules\Catalog\Actions;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Catalog\Engine\Model as BackendCatalogModel;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit specification-action, it will display a form to edit a specification
 *
 * @author Tim van Wolfswinkel <tim@webleads.nl>
 */
class EditSpecification extends BackendBaseActionEdit
{

    private $fieldLanguages = array();

    /**
     * The specification id
     *
     * @var    array
     */
    protected $id;

    /**
     * The specification record
     *
     * @var    array
     */
    protected $record;

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        $this->getData();
        $this->loadForm();
        $this->validateForm();

        $this->parse();
        $this->display();
    }

    /**
     * Get the data
     */
    private function getData()
    {
        $this->id = $this->getParameter('id', 'int');

        if ($this->id == null || !BackendCatalogModel::existsSpecification($this->id)) {
            $this->redirect(
                BackendModel::createURLForAction('specifications') . '&error=non-existing'
            );
        }

        $this->record = BackendCatalogModel::getSpecification($this->id);
    }

    /**
     * Parse the form
     */
    protected function parse()
    {
        parent::parse();

        // assign the data
        $this->tpl->assign('item', $this->record);
        $this->tpl->assign('fieldLanguages', $this->fieldLanguages);
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        $this->frm = new BackendForm('editSpecification');

        foreach ((array)BackendModel::get('fork.settings')->get('Core', 'languages') as $key => $language) {
            $specificationLanguage = BackendCatalogModel::getSpecificationLanguage($this->id, $language);

            $fieldTitle = $this->frm->addText("title_" . strtolower($language), $specificationLanguage['title']);

            $this->fieldLanguages[$key]["key"] = $key;
            $this->fieldLanguages[$key]["language"] = $language;
            $this->fieldLanguages[$key]["title"] = $fieldTitle->parse();
        }

        $this->meta = new BackendMeta($this->frm, $this->record['meta_id'], 'title_nl', true);
        $this->meta->setUrlCallback('Backend\Modules\Catalog\Engine\Model', 'getURLForSpecification', array($this->record['id']));
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $this->frm->cleanupFields();

            // validate fields
//            $this->frm->getField('title')->isFilled(BL::err('TitleIsRequired'));

            $this->meta->validate();

            if ($this->frm->isCorrect()) {
                // build item
                $item['id'] = $this->id;
                $item['language'] = BL::getWorkingLanguage();
                $item['meta_id'] = $this->meta->save(true);

                // save the data
                BackendCatalogModel::updateSpecification($item['id'], $item);

                //--Add the languages
                foreach ((array)BackendModel::get('fork.settings')->get('Core', 'languages') as $key => $language) {
                    $itemLanguage = array();
                    $itemLanguage['id'] = $item['id'];
                    $itemLanguage['language'] = $language;
                    $itemLanguage['title'] = $this->frm->getField('title_' . $language)->getValue();


                    BackendCatalogModel::updateSpecificationLanguage($itemLanguage, $language);
                }

                // trigger event
                BackendModel::triggerEvent($this->getModule(), 'after_edit_specification', array('item' => $item));

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createURLForAction('specifications') . '&report=edited-specification&var=' . urlencode($this->frm->getField('title_nl')->getValue()) . '&highlight=row-' . $item['id']
                );
            }
        }
    }
}