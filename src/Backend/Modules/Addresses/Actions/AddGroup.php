<?php

namespace Backend\Modules\Addresses\Actions;

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
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
 * This is the add-action, it will display a form to create a new item
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class AddGroup extends BackendBaseActionAdd
{
    /**
     * Execute the actions
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

        $this->frm = new BackendForm('add');
        $this->frm->addText('title');

        $groups = BackendAddressesModel::getAllGroupsForDropdown(false);
        array_unshift($groups, "");
        $this->frm->addDropdown('group', $groups);

        //--Get all the users
        $addresses = BackendAddressesModel::getAllAddresses();

        if (!empty($addresses)) {
            //--Loop all the users
            foreach ($addresses as &$address) {
                $strAddress = $address["name"] != "" ? " (" . $address["name"] . " " . $address["firstname"] . ")" : "";
                $addressCheckboxes[] = array("label" => $address["company"] . $strAddress, "value" => $address["id"]);
            }
            //--Add multicheckboxes to form
            $this->frm->addMultiCheckbox("addresses", $addressCheckboxes);
        }

        $this->meta = new BackendMeta($this->frm, null, 'title', true);
    }

    /**
     * Parse the page
     */
    protected function parse()
    {
        parent::parse();
        // assign the url for the detail page
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

                $item['id'] = BackendAddressesModel::insertGroup($item);

                if (isset($fields["addresses"])) {

                    //--Get all the users
                    $addresses = $fields["addresses"]->getValue();
                    foreach ($addresses as $value) {
                        $userGroup = array();
                        $userGroup["group_id"] = $item['id'];
                        $userGroup["address_id"] = $value;

                        //--Add user to the group
                        BackendAddressesModel::insertAddressToGroup($userGroup);
                    }
                }

                BackendModel::triggerEvent($this->getModule(), 'after_add_group', $item);
                $this->redirect(BackendModel::createURLForAction('groups') . '&report=added&highlight=row-' . $item['id']);
            }
        }
    }
}