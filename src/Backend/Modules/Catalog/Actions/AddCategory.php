<?php

namespace Backend\Modules\Catalog\Actions;

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Catalog\Engine\Model as BackendCatalogModel;
use Symfony\Component\Filesystem\Filesystem;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the add category-action, it will display a form to create a new category
 *
 * @author Tim van Wolfswinkel <tim@webleads.nl>
 */
class AddCategory extends BackendBaseActionAdd
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
        $this->frm = new BackendForm('addCategory');

        $this->frm->addImage('image');
        $this->frm->addDropdown('ballcolor', BackendCatalogModel::$ballColorArray);

        // get the categories
        $categories = BackendCatalogModel::getCategories(true);

        $this->frm->addDropdown('parent_id', $categories);

        foreach ((array)BackendModel::get('fork.settings')->get('Core', 'languages') as $key => $language) {
            $fieldTitle = $this->frm->addText("title_" . strtolower($language));
            $fieldDescription = $this->frm->addEditor("description_" . strtolower($language));
            $fieldSummary = $this->frm->addEditor("summary_" . strtolower($language));
            $fieldBalltext = $this->frm->addText("balltext_" . strtolower($language));

            $this->fieldLanguages[$key]["key"] = $key;
            $this->fieldLanguages[$key]["language"] = $language;
            $this->fieldLanguages[$key]["title"] = $fieldTitle->parse();
            $this->fieldLanguages[$key]["description"] = $fieldDescription->parse();
            $this->fieldLanguages[$key]["summary"] = $fieldSummary->parse();
            $this->fieldLanguages[$key]["balltext"] = $fieldBalltext->parse();

            /* $this->meta[$language]['meta'] = new BackendMeta($this->frm, null, 'title_' . $language, true, $language);
             $this->meta[$language]['meta']->setURLCallback('Backend\Modules\Catalog\Engine\Model', 'getURLForCategory');
             $this->meta[$language]['language'] = $language;
             $this->meta[$language]['fields'] = $this->meta[$language]['meta']->returnFields();*/
        }

        $this->meta = new BackendMeta($this->frm, null, 'title_nl', true);
        $this->meta->setURLCallback('Backend\Modules\Catalog\Engine\Model', 'getURLForCategory');
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $this->frm->cleanupFields();

            // validate fields

            if ($this->frm->getField('image')->isFilled()) {
                $this->frm->getField('image')->isAllowedExtension(array('jpg', 'png', 'gif', 'jpeg'), BL::err('JPGGIFAndPNGOnly'));
                $this->frm->getField('image')->isAllowedMimeType(array('image/jpg', 'image/png', 'image/gif', 'image/jpeg'), BL::err('JPGGIFAndPNGOnly'));
            }

            $this->meta->validate();

            if ($this->frm->isCorrect()) {
                // build item
                $item['language'] = BL::getWorkingLanguage();
                $item['title'] = $this->frm->getField('title_nl')->getValue();
                $item['meta_id'] = $this->meta->save();
                $item['sequence'] = BackendCatalogModel::getMaximumCategorySequence() + 1;
                $item['ballcolor'] = $this->frm->getField('ballcolor')->getValue();

                if ($this->frm->getField('parent_id')->getValue() <> 'no_category') {
                    $item['parent_id'] = $this->frm->getField('parent_id')->getValue();
                }

                // the image path
                $imagePath = FRONTEND_FILES_PATH . '/' . $this->getModule() . '/categories';

                // create folders if needed
                $fs = new Filesystem();
                if (!$fs->exists($imagePath . '/source')) {
                    $fs->mkdir($imagePath . '/source');
                }
                if (!$fs->exists($imagePath . '/150x150')) {
                    $fs->mkdir($imagePath . '/150x150');
                }
                if (!$fs->exists($imagePath . '/800x')) {
                    $fs->mkdir($imagePath . '/800x');
                }
                if (!$fs->exists($imagePath . '/400x480')) {
                    $fs->mkdir($imagePath . '/400x480');
                }

                // is there an image provided?
                if ($this->frm->getField('image')->isFilled()) {
                    // build the image name
                    $item['image'] = $this->meta->getUrl() . $this->frm->getField('image')->getExtension();

                    // upload the image & generate thumbnails
                    $this->frm->getField('image')->generateThumbnails($imagePath, $item['image']);
                }

                // save the data
                $item['id'] = BackendCatalogModel::insertCategory($item);

                //--Add the languages
                foreach ((array)BackendModel::get('fork.settings')->get('Core', 'languages') as $key => $language) {
                    $itemLanguage = array();
                    $itemLanguage['id'] = $item['id'];
                    $itemLanguage['language'] = $language;
                    //                    $itemLanguage['meta_id'] = $this->meta[$language]['meta']->save();
                    $itemLanguage['title'] = $this->frm->getField('title_' . $language)->getValue();
                    $itemLanguage['description'] = $this->frm->getField('description_' . $language)->getValue();
                    $itemLanguage['summary'] = $this->frm->getField('summary_' . $language)->getValue();
                    $itemLanguage['url'] = BackendCatalogModel::getURLForCategoryLanguage($this->frm->getField('title_' . $language)->getValue(), null, $language);
                    $itemLanguage['balltext'] = $this->frm->getField('balltext_' . $language)->getValue();

                    BackendCatalogModel::insertCategoryLanguage($itemLanguage);
                }

                // trigger event
                BackendModel::triggerEvent(
                    $this->getModule(), 'after_add_category', array('item' => $item)
                );

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createURLForAction('categories') . '&report=added-category&var=' . urlencode($this->frm->getField('title_nl')->getValue()) . '&highlight=row-' . $item['id']
                );
            }
        }
    }

    protected function parse()
    {
        parent::parse();

        //        $this->tpl->assign('meta', $this->meta);
        $this->tpl->assign('fieldLanguages', $this->fieldLanguages);
    }
}