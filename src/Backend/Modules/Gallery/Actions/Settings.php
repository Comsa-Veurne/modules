<?php
namespace Backend\Modules\Gallery\Actions;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Model as BackendModel;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit-action, it will display a form with the item data to edit
 *
 * @author John Poelman <john.poelman@bloobz.be>
 * @author Waldo Cosman <waldo@comsa.be>
 * @author Nick Vandevenne <nick@comsa.be>
 */
class Settings extends BackendBaseActionEdit
{

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
    protected function loadForm()
    {
        $this->frm = new BackendForm('settings');

        $resolutions = null;
        //--Check if settings is filled in
        if ($this->get('fork.settings')->get($this->URL->getModule(), 'resolutions', false))
        {
            //--Split the resolutions string to
            $resolutions = implode(",", $this->get('fork.settings')->get($this->URL->getModule(), 'resolutions', false));
        }

        $this->frm->addText(
            'resolutions', $resolutions, null, 'inputText tagBox', 'inputTextError tagBox'
        );

        $this->frm->addCheckbox('gallery', $this->get('fork.settings')->get($this->URL->getModule(), 'gallery', false));
        $this->frm->addCheckbox('slideshow', $this->get('fork.settings')->get($this->URL->getModule(), 'slideshow', false));
    }

    /**
     * Parse the form
     */
    protected function parse()
    {
        $this->header->addJS('Settings.js', null, false);

        parent::parse();
    }

    /**
     * Validates the settings form
     */
    private function validateForm()
    {
        if ($this->frm->isSubmitted())
        {
            if ($this->frm->isCorrect())
            {
                // redirect to the settings page
                $resolutions = explode(",", $this->frm->getField('resolutions')->getValue());
                $okres = array();
                foreach ($resolutions as &$res)
                {
                    $res = trim($res);
                    if (preg_match('/^\d{1,}[x]{1,1}\d{0,}$/', $res))
                    {
                        $okres[] = $res;
                    }
                }
                $this->get('fork.settings')->set($this->URL->getModule(), 'resolutions', $okres);

                $this->get('fork.settings')->set($this->URL->getModule(), 'gallery', (bool)$this->frm->getField('gallery')->getValue());
                $this->get('fork.settings')->set($this->URL->getModule(), 'slideshow', (bool)$this->frm->getField('slideshow')->getValue());

                BackendModel::triggerEvent($this->getModule(), 'after_saved_settings');

                $this->redirect(BackendModel::createURLForAction('settings') . '&report=saved');
            }
        }
    }
}