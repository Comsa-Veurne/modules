<?php
namespace Backend\Modules\Mailengine\Actions;

use Backend\Core\Engine\Base\Action as BackendBaseAction;
use Backend\Core\Engine\Csv as BackendCSV;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Modules\Mailengine\Engine\Model as BackendMailengineModel;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the ImportUsers action
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class ImportUsers extends BackendBaseAction
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
    private function loadForm()
    {
        //--Create form
        $this->frm = new BackendForm('import');

        //--Create file
        $this->frm->addFile('csv');

        //--Dropdown for languages
        $this->frm->addDropdown('languages', BL::getWorkingLanguages(), BL::getWorkingLanguage());

        //--Get all the users
        $groups = BackendMailengineModel::getAllGroups();

        //--Loop all the groups
        $groupCheckboxes = array();
        foreach ($groups as &$group) {
            $groupCheckboxes[] = array("label" => $group["title"], "value" => $group["id"]);
        }

        //--Add multicheckboxes to form
        $this->frm->addMultiCheckbox("groups", $groupCheckboxes);

        $this->frm->parse($this->tpl);
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {

        if ($this->frm->isSubmitted()) {
            //--Clean form
            $this->frm->cleanupFields();

            //--Get fields
            $fields = $this->frm->getFields();

            //--Field required
            $fields['csv']->isFilled(BL::err('CSVIsRequired'));

            //--Check if form is correct
            if ($fields['csv']->isFilled()) {
                // convert the CSV file to an array
                $csv = BackendCSV::fileToArray($fields['csv']->getTempFileName(), array("email", "name"), null, ';');

                //--check if the csv is correct
                if ($csv === false || empty($csv) || !isset($csv[0])) {
                    $fields['csv']->addError(BL::err('InvalidCSV'));
                }

                //--Get all the groups
                $groups = $fields["groups"]->getValue();
                $language = $fields["languages"]->getValue();

                //--Process CSV
                $return = $this->processCsv($csv, $groups, $language);

                if (is_array($return)) {
                    $this->tpl->assign('hideForm', true);
                    $this->tpl->assign('return', $return);
                }
            }
        }
    }

    /**
     *
     * Process CSV file
     *
     * @param $csv
     * @param $groups
     *
     * @return array()
     */
    private function processCsv($csv, $groups, $language)
    {

        $errorEmail = 0;
        $errorAlreadyExists = 0;
        $successInserted = 0;

        foreach ($csv as $row) {

            set_time_limit(30);

            if (filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {

                //--Get user from e-mail
                $user = BackendMailengineModel::getUserFromEmail($row['email']);

                if (empty($user)) {
                    $data = array();
                    $data['email'] = $row['email'];
                    $data['name'] = !isset($row['name']) || $row['name'] == '' ? $row['email'] : $row['name'];
                    $data['language'] = $language;

                    //--Add user
                    $user = array();
                    $user['id'] = BackendMailengineModel::insertUser($data);

                    //--Add count for ok
                    $successInserted++;
                } else {
                    //--Add count for already exists
                    $errorAlreadyExists++;
                }

                //--Loop all the groups and add the user to the group
                foreach ($groups as $value) {
                    //--Check if user is already linked to the group
                    if (!BackendMailengineModel::existsUserGroup($user['id'], $value)) {
                        $groupUser = array();
                        $groupUser["user_id"] = $user['id'];
                        $groupUser["group_id"] = $value;

                        //--Add user to the group
                        BackendMailengineModel::insertUserToGroup($groupUser);
                    }
                }
            } else {
                $errorEmail++;
            }
        }

        $return = array('errorEmail' => $errorEmail, 'errorAlreadyExists' => $errorAlreadyExists, 'successInserted' => $successInserted);

        return $return;
    }
}