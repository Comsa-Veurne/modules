<?php

namespace Backend\Modules\Catalog\Actions;

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
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
 * This is the add specification-action, it will display a form to create a new specification
 *
 * @author Tim van Wolfswinkel <tim@webleads.nl>
 */
class AddSpecification extends BackendBaseActionAdd
{
    private $fieldLanguages = array();

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        $this->loadForm();
        $this->validateForm();

        $this->parse();
        $this->display();
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        $this->frm = new BackendForm('addSpecification');


        foreach ((array)BackendModel::get('fork.settings')->get('Core', 'languages') as $key => $language) {
            $fieldTitle = $this->frm->addText("title_" . strtolower($language));

            $this->fieldLanguages[$key]["key"] = $key;
            $this->fieldLanguages[$key]["language"] = $language;
            $this->fieldLanguages[$key]["title"] = $fieldTitle->parse();
        }

        $this->meta = new BackendMeta($this->frm, null, 'title_nl', true);
        $this->meta->setURLCallback('Backend\Modules\Catalog\Engine\Model', 'getURLForSpecification');
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $this->frm->cleanupFields();

            // validate fields

            $this->meta->validate();

            if ($this->frm->isCorrect()) {
                // build item
                $item['language'] = BL::getWorkingLanguage();
                $item['meta_id'] = $this->meta->save();
                $item['sequence'] = BackendCatalogModel::getMaximumSpecificationSequence() + 1;

                // save the data
                $item['id'] = BackendCatalogModel::insertSpecification($item);

                //--Add the languages
                foreach ((array)BackendModel::get('fork.settings')->get('Core', 'languages') as $key => $language) {
                    $itemLanguage = array();
                    $itemLanguage['id'] = $item['id'];
                    $itemLanguage['language'] = $language;
                    $itemLanguage['title'] = $this->frm->getField('title_' . $language)->getValue();

                    BackendCatalogModel::insertSpecificationLanguage($itemLanguage);
                }

                // trigger event
                BackendModel::triggerEvent($this->getModule(), 'after_add_specification', array('item' => $item));

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createURLForAction('specifications') . '&report=added-specification&var=' . urlencode($this->frm->getField('title_nl')->getValue()) . '&highlight=row-' . $item['id']
                );
            }
        }
    }

    protected function parse()
    {
        parent::parse();

        $this->tpl->assign('fieldLanguages', $this->fieldLanguages);
    }
}