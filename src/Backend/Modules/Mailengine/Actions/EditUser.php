<?php
namespace Backend\Modules\Mailengine\Actions;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
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
 * This is the edit-action, it will display a form with the item data to edit
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class EditUser extends BackendBaseActionEdit
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
		if($this->id == null || !BackendMailengineModel::existsUser($this->id))
		{
			$this->redirect(BackendModel::createURLForAction('users') . '&error=non-existing');
		}

		$this->record = BackendMailengineModel::getUser($this->id);

		if(substr($this->record['unsubscribe_on'], 0, 10) == '0000-00-00')
		{
			unset($this->record['unsubscribe_on']);
		}
	}

	/**
	 * Load the form
	 */
	protected function loadForm()
	{
		$groupCheckboxes = array();

		// set hidden values
		$rbtActiveValues = array();
		$rbtActiveValues[] = array('label' => ucfirst(BL::lbl('Active')), 'value' => 'Y');
		$rbtActiveValues[] = array('label' => ucfirst(BL::lbl('NotActive')), 'value' => 'N');

		// create form
		$this->frm = new BackendForm('edit');
		$this->frm->addText('name', $this->record['name']);
		$this->frm->addText('email', $this->record['email']);
		$this->frm->addRadiobutton('active', $rbtActiveValues, $this->record['active']);

		//--Get all the groups
		$groups = BackendMailengineModel::getAllGroups();

		//--Check if there are groups
		if(!empty($groups))
		{

			//--Loop all the users
			foreach($groups as $key => &$group)
			{
				$groupCheckboxes[] = array("label" => $group["title"], "value" => $group["id"]);
			}

			//--Get the users from the group
			$groupsUser = BackendMailengineModel::getGroupsForUser($this->id);

			//--Create a selected-array
			$groupCheckboxesSelected = count($groupsUser) > 0 ? array_keys($groupsUser) : null;

			//--Add multicheckboxes to form
			$this->frm->addMultiCheckbox("groups", $groupCheckboxes, $groupCheckboxesSelected);
		}
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		parent::parse();
		$this->tpl->assign('item', $this->record);
	}

	/**
	 * Validate the form
	 */
	protected function validateForm()
	{
		if($this->frm->isSubmitted())
		{
			$this->frm->cleanupFields();

			// validation
			$fields = $this->frm->getFields();
			$fields['name']->isFilled(BL::err('FieldIsRequired'));
			$fields['email']->isFilled(BL::err('FieldIsRequired'));
			$fields['email']->isEmail(BL::err('EmailIsInvalid'));

			if($this->frm->isCorrect())
			{
				$item['name'] = $fields['name']->getValue();
				$item['email'] = $fields['email']->getValue();
				$item['active'] = $fields['active']->getValue();

				BackendMailengineModel::updateUser($this->id, $item);
				$item['id'] = $this->id;

				//--Delete users from the group
				BackendMailengineModel::deleteGroupFromUser($this->id);

				//--Check if there are groups
				if(isset($fields['groups']))
				{
					//--Get all the groups
					$groups = $fields["groups"]->getValue();
					foreach($groups as $key => $value)
					{
						$groupUser = array();
						$groupUser["user_id"] = $this->id;
						$groupUser["group_id"] = $value;

						//--Add user to the group
						BackendMailengineModel::insertUserToGroup($groupUser);
					}
				}

				BackendModel::triggerEvent($this->getModule(), 'after_edit_user', $item);
				$this->redirect(BackendModel::createURLForAction('users') . '&report=edited&highlight=row-' . $item['id']);
			}
		}
	}
}