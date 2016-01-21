<?php
namespace Frontend\Modules\Mailengine\Actions;

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Form as FrontendForm;
use Frontend\Core\Engine\Language as FL;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Mailengine\Engine\Model as FrontendMailengineModel;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the Subscribe-action, it will display the overview of mailengine posts
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class MailengineSubscribe extends FrontendBaseBlock
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->loadForm();
        $this->validateForm();
        $this->loadTemplate();
        $this->parse();
    }

    /**
     * Load the form
     */
    protected function loadForm()
    {
        $this->frm = new FrontendForm('subscribe', null, 'post', null, false);

        $this->frm->addText('email', null, 255, 'form-control');
    }

    /**
     * Validate the form
     */
    protected function validateForm()
    {
        if ($this->frm->isSubmitted()) {
            $fields = $this->frm->getFields();

            if ($fields['email']->isEmail(FL::err('EmailIsInvalid'))) ;
            {
                if (FrontendMailengineModel::isSubscribed($fields['email']->getValue())) {
                    $fields['email']->addError(FL::err('AlreadySubscribed'));
                }
            }

            if ($this->frm->isCorrect()) {

                //--Subscribe
                $id = FrontendMailengineModel::subscribe($fields['email']->getValue());

                //--Get the default group
                $defaultGroup = FrontendModel::getModuleSetting($this->module, 'default_group');

                if ($defaultGroup > 0) {
                    $data = array();
                    $data['user_id'] = $id;
                    $data['group_id'] = $defaultGroup;

                    //--Add user to group
                    FrontendMailengineModel::insertUserToGroup($data);
                }

                // redirect
                $this->redirect(FrontendNavigation::getURLForBlock('Mailengine', 'MailengineSubscribe') . '?sent=true#subscribe');
            }
        }

        $this->frm->parse($this->tpl);
    }

    /**
     * Parse the page
     */
    protected function parse()
    {

        // form was sent?
        if ($this->URL->getParameter('sent') == 'true') {
            // show message
            $this->tpl->assign('subscribeIsSuccess', true);

            // hide form
            $this->tpl->assign('subscribeHideForm', true);
        }
    }
}