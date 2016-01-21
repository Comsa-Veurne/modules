<?php

namespace Backend\Modules\Blocks\Actions;

use Backend\Core\Engine\Base\ActionEdit;
use Backend\Core\Engine\Form;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Meta;
use Backend\Core\Engine\Model;
use Backend\Modules\Blocks\Engine\Model as BackendBlocksModel;
use Backend\Modules\Pages\Engine\Model as BackendPagesModel;

/**
 * This is the edit-action, it will display a form with the item data to edit
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Edit extends ActionEdit
{
    /**
     * Execute the action
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
        $this->id = $this->getParameter('id', 'int', null);
        if ($this->id == null || !BackendBlocksModel::exists($this->id)) {
            $this->redirect(
                Model::createURLForAction('Index') . '&error=non-existing'
            );
        }

        $this->record = BackendBlocksModel::get($this->id);
    }

    /**
     * Load the form
     */
    protected function loadForm()
    {
        // create form
        $this->frm = new Form('edit');

        $this->frm->addText('title', $this->record['title'], null, 'inputText title', 'inputTextError title');
        $this->frm->addEditor('text', $this->record['text']);
        //$this->frm->addText('link', $this->record['link']);
        $this->frm->addText('linktext', $this->record['linktext']);
        $this->frm->addImage('image');

        // build array with options for the hidden Radiobutton
        $RadiobuttonHiddenValues[] = array('label' => Language::lbl('Hidden'), 'value' => 'Y');
        $RadiobuttonHiddenValues[] = array('label' => Language::lbl('Published'), 'value' => 'N');
        $this->frm->addRadioButton('hidden', $RadiobuttonHiddenValues, $this->record['hidden']);

        // get categories
        $categories = BackendBlocksModel::getCategories();
        $this->frm->addDropdown('category_id', $categories, $this->record['category_id']);

        // meta
        $this->meta = new Meta($this->frm, $this->record['meta_id'], 'title', true);
        $this->meta->setUrlCallBack('Backend\Modules\Blocks\Engine\Model', 'getUrl', array($this->record['id']));

        // redirect
        $redirectValue = 'none';
        if (isset($this->record['page_id'])) {
            $redirectValue = 'internal';
        }
        if (isset($this->record['link'])) {
            $redirectValue = 'external';
        }
        $redirectValues = array(
            array('value' => 'none', 'label' => \SpoonFilter::ucfirst(Language::lbl('None'))),
            array(
                'value' => 'internal',
                'label' => \SpoonFilter::ucfirst(Language::lbl('InternalLink')),
                'variables' => array('isInternal' => true)
            ),
            array(
                'value' => 'external',
                'label' => \SpoonFilter::ucfirst(Language::lbl('ExternalLink')),
                'variables' => array('isExternal' => true)
            ),
        );
        $this->frm->addRadiobutton('redirect', $redirectValues, $redirectValue);
        $this->frm->addDropdown(
            'internal_redirect',
            BackendPagesModel::getPagesForDropdown(),
            ($redirectValue == 'internal') ? $this->record['page_id'] : null
        );
        $this->frm->addText(
            'external_redirect',
            ($redirectValue == 'external') ? $this->record['link'] : null,
            null,
            null,
            null,
            true
        );
    }

    /**
     * Parse the page
     */
    protected function parse()
    {
        parent::parse();

        // get url
        $url = Model::getURLForBlock($this->URL->getModule(), 'Detail');
        $url404 = Model::getURL(404);

        // parse additional variables
        if ($url404 != $url) {
            $this->tpl->assign('detailURL', SITE_URL . $url);
        }
        $this->record['url'] = $this->meta->getURL();


        $this->tpl->assign('item', $this->record);
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

            $fields['title']->isFilled(Language::err('FieldIsRequired'));

            // validate meta
            $this->meta->validate();

            // validate redirect
            $redirectValue = $this->frm->getField('redirect')->getValue();
            if ($redirectValue == 'internal') {
                $this->frm->getField('internal_redirect')->isFilled(
                    Language::err('FieldIsRequired')
                );
            }
            if ($redirectValue == 'external') {
                $this->frm->getField('external_redirect')->isURL(Language::err('InvalidURL'));
            }

            if ($this->frm->isCorrect()) {
                $item['id'] = $this->id;
                $item['language'] = Language::getWorkingLanguage();

                $item['title'] = $fields['title']->getValue();
                $item['text'] = $fields['text']->getValue();
                //$item['link'] = $fields['link']->getValue();
                $item['linktext'] = $fields['linktext']->getValue();

                if ($redirectValue == 'internal') {
                    $item['link'] = null;
                    $item['page_id'] = $this->frm->getField('internal_redirect')->getValue();
                }
                if ($redirectValue == 'external') {
                    $item['link'] = $this->frm->getField('external_redirect')->getValue();
                    $item['page_id'] = null;
                }

                // the image path
                $imagePath = FRONTEND_FILES_PATH . '/' . $this->getModule() . '/image';

                // create folders if needed
                if (!\SpoonDirectory::exists($imagePath . '/800x')) {
                    \SpoonDirectory::create($imagePath . '/800x');
                }
                if (!\SpoonDirectory::exists($imagePath . '/x800')) {
                    \SpoonDirectory::create($imagePath . '/x800');
                }
                if (!\SpoonDirectory::exists($imagePath . '/450x250')) {
                    \SpoonDirectory::create($imagePath . '/450x250');
                }
                if (!\SpoonDirectory::exists($imagePath . '/600x300')) {
                    \SpoonDirectory::create($imagePath . '/600x300');
                }
                if (!\SpoonDirectory::exists($imagePath . '/128x128')) {
                    \SpoonDirectory::create($imagePath . '/128x128');
                }
                if (!\SpoonDirectory::exists($imagePath . '/source')) {
                    \SpoonDirectory::create($imagePath . '/source');
                }

                // image provided?
                if ($fields['image']->isFilled()) {
                    // build the image name
                    $item['image'] = $this->meta->getUrl() . '.' . $fields['image']->getExtension();

                    // upload the image & generate thumbnails
                    $fields['image']->generateThumbnails($imagePath, $item['image']);
                }
                $item['hidden'] = $fields['hidden']->getValue();
                $item['category_id'] = $this->frm->getField('category_id')->getValue();

                $item['extra_id'] = $this->record['extra_id'];
                $item['meta_id'] = $this->meta->save();

                BackendBlocksModel::update($item);
                $item['id'] = $this->id;

                Model::triggerEvent(
                    $this->getModule(), 'after_edit', $item
                );
                $this->redirect(
                    Model::createURLForAction('Index') . '&report=edited&highlight=row-' . $item['id']
                );
            }
        }
    }
}