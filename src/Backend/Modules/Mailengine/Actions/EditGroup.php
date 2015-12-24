<?php
namespace Backend\Modules\Mailengine\Actions;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Modules\Mailengine\Engine\Model as BackendMailengineModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;
use Backend\Modules\Users\Engine\Model as BackendUsersModel;

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
		if($this->id == null || !BackendMailengineModel::existsGroup($this->id))
		{
			$this->redirect(BackendModel::createURLForAction('groups') . '&error=non-existing');
		}

		$this->record = BackendMailengineModel::getGroup($this->id);
	}

	/**
	 * Load the form
	 */
	protected function loadForm()
	{
		$userCheckboxes = array();

		// create form
		$this->frm = new BackendForm('edit');
		$this->frm->addText('title', $this->record['title']);

		//--Get all the users
		$users = BackendMailengineModel::getAllUsers();

		//--Check if there are users
		if(!empty($users))
		{
			//--Loop all the users
			foreach($users as $key => &$user)
			{
				$userCheckboxes[] = array("label" => $user["email"], "value" => $user["id"]);
			}

			//--Get the users from the group
			$usersGroup = BackendMailengineModel::getUsersForGroup($this->id);

			//--Create a selected-array
			$userCheckboxesSelected = count($usersGroup) > 0 ? array_keys($usersGroup) : null;

			//--Add multicheckboxes to form
			$this->frm->addMultiCheckbox("users", $userCheckboxes, $userCheckboxesSelected);
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
			$fields['title']->isFilled(BL::err('FieldIsRequired'));

			if($this->frm->isCorrect())
			{
				$item['title'] = $fields['title']->getValue();

				BackendMailengineModel::updateGroup($this->id, $item);
				$item['id'] = $this->id;

				//--Delete users from the group
				BackendMailengineModel::deleteUserFromGroup($this->id);

				//--Check if there are users
				if(isset($fields["users"]))
				{
					//--Get all the users
					$users = $fields["users"]->getValue();
					foreach($users as $key => $value)
					{
						$userGroup = array();
						$userGroup["group_id"] = $this->id;
						$userGroup["user_id"] = $value;

						//--Add user to the group
						BackendMailengineModel::insertUserToGroup($userGroup);
					}
				}

				BackendModel::triggerEvent($this->getModule(), 'after_edit_group', $item);
				$this->redirect(BackendModel::createURLForAction('groups') . '&report=edited&highlight=row-' . $item['id']);
			}
		}
	}
}
