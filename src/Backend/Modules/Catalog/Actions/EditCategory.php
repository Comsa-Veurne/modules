<?php

namespace Backend\Modules\Catalog\Actions;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Catalog\Engine\Model as BackendCatalogModel;
use Backend\Modules\Media\Engine\Helper as BackendMediaHelper;
use Symfony\Component\Filesystem\Filesystem;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit category action, it will display a form to edit an existing category.
 *
 * @author Tim van Wolfswinkel <tim@webleads.nl>
 */
class EditCategory extends BackendBaseActionEdit
{
    private $fieldLanguages = array();
    private $media;

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        $this->getData();
        $this->loadForm();
        $this->validateForm();

        $this->header->addJsData("Media", "mediaModule", (string)$this->getModule());
        $this->header->addJsData("Media", "mediaAction", (string)$this->getAction());
        $this->header->addJsData("Media", "mediaId", (int)$this->id);
        $this->header->addJsData("Media", "mediaType", (string)'category');
        $this->parse();
        $this->display();
    }

    /**
     * Get the data
     */
    private function getData()
    {
        $this->id = $this->getParameter('id', 'int');
        if ($this->id == null || !BackendCatalogModel::existsCategory($this->id)) {
            $this->redirect(
                BackendModel::createURLForAction('categories') . '&error=non-existing'
            );
        }

        $this->record = BackendCatalogModel::getCategory($this->id);
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        // create form
        $this->frm = new BackendForm('editCategory');
        $this->frm->addImage('image');
        $this->frm->addCheckbox('delete_image');
        $this->frm->addDropdown('ballcolor', BackendCatalogModel::$ballColorArray, $this->record['ballcolor']);

        //hidden values
        $categories = BackendCatalogModel::getCategories(true);

        $this->frm->addDropdown('parent_id', $categories, $this->record['parent_id']);

        foreach ((array)BackendModel::get('fork.settings')->get('Core', 'languages') as $key => $language) {
            $catalogLanguage = BackendCatalogModel::getCategoryLanguage($this->id, $language);

            $fieldTitle = $this->frm->addText("title_" . strtolower($language), empty($catalogLanguage) ? "" : $catalogLanguage['title']);
            $fieldDescription = $this->frm->addEditor("description_" . strtolower($language), empty($catalogLanguage) ? "" : $catalogLanguage['description']);
            $fieldSummary = $this->frm->addEditor("summary_" . strtolower($language), empty($catalogLanguage) ? "" : $catalogLanguage['summary']);
            $fieldBalltext = $this->frm->addText("balltext_" . strtolower($language), empty($catalogLanguage) ? "" : $catalogLanguage['balltext']);

            $this->fieldLanguages[$key]["key"] = $key;
            $this->fieldLanguages[$key]["language"] = $language;
            $this->fieldLanguages[$key]["title"] = $fieldTitle->parse();
            $this->fieldLanguages[$key]["description"] = $fieldDescription->parse();
            $this->fieldLanguages[$key]["summary"] = $fieldSummary->parse();
            $this->fieldLanguages[$key]["balltext"] = $fieldBalltext->parse();
        }

        $this->meta = new BackendMeta($this->frm, $this->record['meta_id'], 'title_nl', true);
        $this->meta->setUrlCallback('Backend\Modules\Catalog\Engine\Model', 'getURLForCategory', array($this->record['id']));
        //--Media
        $this->media = new BackendMediaHelper($this->frm, (string)$this->getModule(), (int)$this->id, (string)$this->getAction(), 'category');
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

        // is category allowed to be deleted?
        if (BackendCatalogModel::isCategoryAllowedToBeDeleted($this->id)) {
            $this->tpl->assign('showDelete', true);
        }
        //--Add media
        $this->tpl->assign('mediaItems', $this->media->getMediaItems());
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $this->frm->cleanupFields();

            $recordId = $this->record['id'];
            $newParent = $this->frm->getField('parent_id')->getValue();

            if ($recordId == $newParent) {
                $this->frm->getField('parent_id')->setError(BL::err('SameCategory'));
            }

            // validate fields

            if ($this->frm->getField('image')->isFilled()) {
                $this->frm->getField('image')->isAllowedExtension(array('jpg', 'png', 'gif', 'jpeg'), BL::err('JPGGIFAndPNGOnly'));
                $this->frm->getField('image')->isAllowedMimeType(array('image/jpg', 'image/png', 'image/gif', 'image/jpeg'), BL::err('JPGGIFAndPNGOnly'));
            }

            $this->meta->validate();

            //--Validate Media
            $this->media->validate();

            if ($this->frm->isCorrect()) {
                // build item
                $item['id'] = $this->id;
                $item['language'] = $this->record['language'];
                $item['extra_id'] = $this->record['extra_id'];
                $item['title'] = $this->frm->getField('title_nl')->getValue();
                $item['parent_id'] = $this->frm->getField('parent_id')->getValue();
                $item['meta_id'] = $this->meta->save(true);
                $item['ballcolor'] = $this->frm->getField('ballcolor')->getValue();

                // the image path
                $imagePath = FRONTEND_FILES_PATH . '/' . $this->getModule() . '/categories';

                // create folders if needed
                $fs = new Filesystem();

                if (!$fs->exists($imagePath . '/150x150/')) {
                    $fs->mkdir($imagePath . '/150x150/');
                }

                if (!$fs->exists($imagePath . '/source/')) {
                    $fs->mkdir($imagePath . '/source/');
                }
                if (!$fs->exists($imagePath . '/800x')) {
                    $fs->mkdir($imagePath . '/800x');
                }
                if (!$fs->exists($imagePath . '/400x480')) {
                    $fs->mkdir($imagePath . '/400x480');
                }

                if ($this->frm->getField('delete_image')->isChecked()) {
                    BackendModel::deleteThumbnails($imagePath, $this->record['image']);
                    $item['image'] = null;
                }

                // image provided?
                if ($this->frm->getField('image')->isFilled()) {
                    // build the image name
                    $item['image'] = $this->meta->getUrl() . '.' . $this->frm->getField('image')->getExtension();

                    // upload the image & generate thumbnails
                    $this->frm->getField('image')->generateThumbnails($imagePath, $item['image']);
                }

                // update the item
                BackendCatalogModel::updateCategory($item);

                //--Add the languages
                foreach ((array)BackendModel::get('fork.settings')->get('Core', 'languages') as $key => $language) {
                    $itemLanguage = array();
                    $itemLanguage['id'] = $item['id'];
                    $itemLanguage['language'] = $language;
                    $itemLanguage['title'] = $this->frm->getField('title_' . $language)->getValue();
                    $itemLanguage['description'] = $this->frm->getField('description_' . $language)->getValue();
                    $itemLanguage['summary'] = $this->frm->getField('summary_' . $language)->getValue();
                    $itemLanguage['url'] = BackendCatalogModel::getURLForCategoryLanguage($this->frm->getField('title_' . $language)->getValue(), $item['id'], $language);
                    $itemLanguage['balltext'] = $this->frm->getField('balltext_' . $language)->getValue();

                    BackendCatalogModel::updateCategoryLanguage($itemLanguage);
                }

                // trigger event
                BackendModel::triggerEvent(
                    $this->getModule(), 'after_edit_category', array('item' => $item)
                );

                // everything is saved, so redirect to the overview
                $this->redirect(
                    BackendModel::createURLForAction('categories') . '&report=edited-category&var=' . urlencode($this->frm->getField('title_nl')->getValue()) . '&highlight=row-' . $item['id']
                );
            }
        }
    }
}