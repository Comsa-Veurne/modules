<?php
namespace Backend\Modules\Mailengine\Actions;

use Backend\Core\Engine\Base\Action as BackendBaseAction;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Mailengine\Engine\Model as BackendMailengineModel;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the Send action
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Send extends BackendBaseAction
{

    protected $frm_review = null;

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->loadData();

        if ($this->getParameter('ready', 'int', null)) {
            //--Parse Form Preview
            $this->parseReadyToSend();
        } else {
            $this->loadForm();
            $this->validateForm();
        }

        $this->parse();
        $this->display();
    }

    /**
     * Load the data
     */
    protected function loadData()
    {
        $this->id = $this->getParameter('id', 'int', null);
        if ($this->id == null || !BackendMailengineModel::exists($this->id)) {
            $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
        }

        $this->record = BackendMailengineModel::get($this->id);
    }

    /**
     * Load the form
     */
    protected function loadForm()
    {
        //--Create form
        $this->frm = new BackendForm("send_preview");

        //--Get the groups
        $groups = BackendMailengineModel::getAllGroupsForDropdown(true);

        if (empty($groups)) {
            \SpoonHTTP::redirect(BackendModel::createURLForAction('index', $this->module) . "&id=" . $this->id . "&error=GroupsNeededToSendMailing");
        }

        //--Loop all the users
        foreach ($groups as $key => $value) {
            $groupCheckboxes[] = array("label" => $value, "value" => $key);
        }
        //--Add multicheckboxes to form
        $this->frm->addMultiCheckbox("groups", $groupCheckboxes, null);
        $this->frm->addDate('start_date');
        $this->frm->addTime('start_time', null, 'inputText time');

        if (BackendModel::isModuleInstalled('profiles')) {
            $profiles = BackendMailengineModel::getAllProfiles();
            $profileGroups = BackendMailengineModel::getAllProfileGroupsForDropdown(true);
            $profileGroupCheckboxes = array();
            $this->tpl->assign("profilesCount", count($profiles));

            //--Loop all the users
            foreach ($profileGroups as $key => $value) {
                $profileGroupCheckboxes[] = array("label" => $value, "value" => $key);
            }

            if (!empty($profileGroups)) {
                $this->frm->addMultiCheckbox("profile_groups", $profileGroupCheckboxes, null);
            }

            $this->frm->addCheckbox("profiles_all");
        }

        //--Create review form
        $this->frm_review = new BackendForm("send_review");

        //--Add hidden field as dummy
        $this->frm_review->addHidden("profiles_all");
        $this->frm_review->addHidden("profile_groups");
        $this->frm_review->addHidden("groups");
        $this->frm_review->addDate("start_date");
        $this->frm_review->addTime("start_time");
    }

    /*
    *
    * Validate the form
    *
    */
    protected function validateForm()
    {
        if ($this->frm->isSubmitted()) {

            $this->frm->cleanupFields();

            // validation
            $fields = $this->frm->getFields();

            if ($this->frm->isCorrect()) {

                $groups = array();
                $profilesAll = 0;
                $profileGroups = array();
                $users = array();

                //--Get all the groups
                $groups = $fields["groups"]->getValue();

                //--Check if mailengine groups are selected
                if (!empty($groups)) {
                    //--Get the users for the groups
                    $usersTemp = BackendMailengineModel::getUniqueEmailsFromGroups($groups);

                    //--Add the groups
                    if (is_array($usersTemp)) {
                        $users = array_merge($users, $usersTemp);
                    }
                }

                //--Check if there are profile groups checked
                if (isset($fields["profile_groups"])) {
                    //--Get all the groups
                    $profileGroups = $fields["profile_groups"]->getValue();

                    if (!empty($profileGroups)) {

                        //--Get the users for the groups
                        $usersTemp = BackendMailengineModel::getUniqueEmailsFromProfileGroups($profileGroups);

                        //--Add the groups
                        if (is_array($usersTemp)) {
                            $users = array_merge($users, $usersTemp);
                        }
                    }
                }

                //--Check if all profiles is selected
                if (isset($fields["profiles_all"])) {
                    if ($fields['profiles_all']->getValue() == 1) {
                        $profilesAll = 1;

                        $usersTemp = BackendMailengineModel::getUniqueEmailsFromProfiles();

                        if (is_array($usersTemp)) {
                            $users = array_merge($users, $usersTemp);
                        }
                    }
                }

                //--Loop all the users and set the e-mail as key to remove duplicate e-mails
                $usersTemp = array();
                foreach ($users as $user) {
                    if (!isset($usersTemp[$user['email']])) {
                        $usersTemp[$user['email']] = $user;
                    }
                }

                //--Reset users-array to the unduplicate array
                $users = $usersTemp;

                //--Count users
                $countUsers = count($users);

                //--Create label
                $labelUsers = $countUsers == 1 ? BL::lbl("User") : BL::lbl("Users");

                $this->tpl->assign("users", $users);
                $this->tpl->assign("countUsers", $countUsers);
                $this->tpl->assign("labelUsers", $labelUsers);
                if ($countUsers == 0) {
                    $this->tpl->assign("errorUsers", true);
                    $this->tpl->assign("back", BackendModel::createURLForAction($this->action, $this->module) . "&id=" . $this->id);
                }

                //--Add hidden fields to form
                $this->frm_review->addHidden("groups", implode(",", $groups));
                $this->frm_review->addHidden("profiles_all", $profilesAll);
                $this->frm_review->addHidden("profile_groups", implode(",", $profileGroups));

                $this->frm_review->addHidden("start_date", $fields["start_date"]->getValue());
                $this->frm_review->addHidden("start_time", $fields["start_time"]->getValue());

                //--Parse Form Review
                $this->parseFormReview();
            } else {
                //--Parse Form Preview
                $this->parseFormPreview();
            }
        } elseif ($this->frm_review->isSubmitted()) {

            //--Check if form_review is submitted
            $fields = $this->frm_review->getFields();

            if ($this->frm_review->isCorrect()) {
                //--Insert mailing in ready-to-send-database
                $readyToSendId = BackendMailengineModel::insertMailingInReadyToSendDatabase($this->id, BackendModel::getUTCDate(null, BackendModel::getUTCTimestamp($fields['start_date'], $fields['start_time'])));

                //--Insert users in ready-to-send-database
                $groups = $fields["groups"]->getValue();
                $profilesAll = $fields["profiles_all"]->getValue();
                $profileGroups = $fields["profile_groups"]->getValue();

                BackendMailengineModel::insertUsersInReadyToSendDatabase($readyToSendId, $groups, $profileGroups, $profilesAll);

                //--Redirect
                \SpoonHTTP::redirect(BackendModel::createURLForAction($this->action, $this->module) . "&id=" . $this->id . "&ready=1");
            }
        } else {
            //--Parse Form Preview
            $this->parseFormPreview();
        }
    }

    /**
     * Parse the page
     */
    protected function parse()
    {
        parent::parse();

        $this->tpl->assign("item", $this->record);
    }

    /**
     * Parse the form Preview
     */
    protected function parseFormPreview()
    {
        //--Parse the preview form
        $this->frm->parse($this->tpl);
        $this->tpl->assign("form_preview", true);
    }

    /**
     * Parse the form Review
     */
    protected function parseFormReview()
    {
        $this->frm_review->parse($this->tpl);
        $this->tpl->assign("form_review", true);
    }

    protected function parseReadyToSend()
    {
        $this->tpl->assign("ready", true);
    }
}