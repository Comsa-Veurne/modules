<?php

namespace Backend\Modules\Addresses\Actions;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Addresses\Engine\Model as BackendAddressesModel;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit-action, it will display a form with the item data to edit
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class EditGroup extends BackendBaseActionEdit
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
        if ($this->id == null || !BackendAddressesModel::existsGroup($this->id)) {
            $this->redirect(BackendModel::createURLForAction('groups') . '&error=non-existing');
        }

        $this->record = BackendAddressesModel::getGroup($this->id);
    }

    /**
     * Load the form
     */
    protected function loadForm()
    {
        $addressCheckboxes = array();

        // create form
        $this->frm = new BackendForm('edit');
        $this->frm->addText('title', $this->record['title']);
        $this->frm->addHidden("id", $this->id);

        $groups = BackendAddressesModel::getAllGroupsForDropdown(false);
        array_unshift($groups, "");
        $this->frm->addDropdown('group', $groups, $this->record['parent_id']);

        //--Get all the users
        $addresses = BackendAddressesModel::getAllAddresses($this->id);

        if (!empty($addresses)) {
            //--Loop all the users
            foreach ($addresses as &$address) {
                $strAddress = $address["name"] != "" ? " (" . $address["name"] . " " . $address["firstname"] . ")" : "";
                $addressCheckboxes[] = array("label" => $address["company"] . $strAddress, "value" => $address["id"]);
            }

            //--Get the users from the group
            $addressesGroup = BackendAddressesModel::getAddressesForGroup($this->id);

            //--Create a selected-array
            $addressesCheckboxesSelected = count($addressesGroup) > 0 ? array_keys($addressesGroup) : null;

            //--Add multicheckboxes to form
            $this->frm->addMultiCheckbox("addresses", $addressCheckboxes, $addressesCheckboxesSelected);
        }
        // meta
        $this->meta = new BackendMeta($this->frm, $this->record['meta_id'], 'title', true);
        $this->meta->setUrlCallback('Backend\Modules\Addresses\Engine\Model', 'getUrlForGroup', array($this->record['id']));
    }

    /**
     * Parse the page
     */
    protected function parse()
    {
        parent::parse();

        $this->header->addJS('edit.js');

        $this->tpl->assign('item', $this->record);

        // get url
        $url = BackendModel::getURLForBlock($this->URL->getModule(), 'Group');
        $url404 = BackendModel::getURL(404);
        if ($url404 != $url) $this->tpl->assign('detailURL', SITE_URL . $url);
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
            $fields['title']->isFilled(BL::err('FieldIsRequired'));

            $this->meta->validate();

            if ($this->frm->isCorrect()) {
                $item['meta_id'] = $this->meta->save();
                $item['title'] = $fields['title']->getValue();
                $item['parent_id'] = $fields['group']->getValue() != 0 ? $fields['group']->getValue() : null;

                BackendAddressesModel::updateGroup($this->id, $item);
                $item['id'] = $this->id;
                if (isset($fields["addresses"])) {
                    //--Delete addresses for that group
                    BackendAddressesModel::deleteAddressesFromGroup($this->id);

                    //--Get all the users
                    $addresses = $fields["addresses"]->getValue();
                    foreach ($addresses as $value) {
                        $addressGroup = array();
                        $addressGroup["group_id"] = $this->id;
                        $addressGroup["address_id"] = $value;

                        //--Add user to the group
                        BackendAddressesModel::insertAddressToGroup($addressGroup);
                    }
                }
                BackendModel::triggerEvent($this->getModule(), 'after_edit_group', $item);
                $this->redirect(BackendModel::createURLForAction('groups') . '&report=edited&highlight=row-' . $item['id']);
            }
        }
    }
}