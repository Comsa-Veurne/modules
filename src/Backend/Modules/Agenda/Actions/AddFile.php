<?php

namespace Backend\Modules\Agenda\Actions;

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Agenda\Engine\Model as BackendAgendaModel;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the add action, it will display a form to add an file to a item.
 *
 * @author Tim van Wolfswinkel <tim@webleads.nl>
 */
class AddFile extends BackendBaseActionAdd
{
    /**
     * The allowed file extensions
     *
     * @var    array
     */
    private $allowedExtensions = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pps', 'ppsx', 'zip');

    /**
     * The item record
     *
     * @var    array
     */
    private $item;

    /**
     * Id of the item
     *
     * @var    array
     */
    private $id;

    /**
     * Execute the action
     */
    public function execute()
    {
        $this->id = $this->getParameter('agenda_id', 'int');

        if ($this->id !== null && BackendAgendaModel::exists($this->id)) {
            parent::execute();

            $this->getData();
            $this->loadForm();
            $this->validateForm();
            $this->parse();
            $this->display();
        } // the project does not exist
        else {
            $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
        }
    }

    /**
     * Get the necessary data
     */
    private function getData()
    {
        $this->item = BackendAgendaModel::get($this->getParameter('agenda_id', 'int'));
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        $this->frm = new BackendForm('addFile');
        $this->frm->addText('title');
        $this->frm->addFile('file');
        $this->frm->getField('file')->setAttribute('extension', implode(', ', $this->allowedExtensions));
    }

    /**
     * Parses stuff into the template
     */
    protected function parse()
    {
        parent::parse();
        $this->tpl->assign('item', $this->item);
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            // cleanup the submitted fields, ignore fields that were added by hackers
            $this->frm->cleanupFields();

            // validate fields
            $file = $this->frm->getField('file');

            $this->frm->getField('title')->isFilled(BL::err('NameIsRequired'));
            $file->isFilled(BL::err('FieldIsRequired'));

            // validate the file
            if ($this->frm->getField('file')->isFilled()) {
                // file extension
                $this->frm->getField('file')->isAllowedExtension($this->allowedExtensions,
                    BL::err('FileExtensionNotAllowed'));
            }

            // no errors?
            if ($this->frm->isCorrect()) {
                // build file record to insert
                $item['agenda_id'] = $this->item['id'];
                $item['title'] = $this->frm->getField('title')->getValue();

                // the file path
                $filePath = FRONTEND_FILES_PATH . '/' . $this->getModule() . '/' . $item['agenda_id'] . '/source';

                // create folders if needed
                if (!\SpoonDirectory::exists($filePath)) {
                    \SpoonDirectory::create($filePath);
                }

                // file provided?
                if ($file->isFilled()) {
                    // build the file name
                    $item['filename'] = time() . '.' . $file->getExtension();

                    // upload the file
                    $file->moveFile($filePath . '/' . $item['filename']);
                }

                $item['sequence'] = BackendAgendaModel::getMaximumFilesSequence($item['agenda_id']) + 1;

                // insert it
                $item['id'] = BackendAgendaModel::saveFile($item);

                // trigger event
                BackendModel::triggerEvent($this->getModule(), 'after_add_file', array('item' => $item));

                // everything is saved, so redirect to the overview
                $this->redirect(BackendModel::createURLForAction('media') . '&agenda_id=' . $item['agenda_id'] . '&report=added&var=' . urlencode($item['title']) . '&highlight=row-' . $item['id'] . '#tabFiles');
            }
        }
    }
}