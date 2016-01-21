<?php
namespace Backend\Modules\Mailengine\Actions;

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
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

			//--Add multicheckboxes to form
			$this->frm->addMultiCheckbox("users", $userCheckboxes);
		}
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		parent::parse();
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

				$item['id'] = BackendMailengineModel::insertGroup($item);

				//--Check if there are users
				if(isset($fields["users"]))
				{
					//--Get all the users
					$users = $fields["users"]->getValue();
					foreach($users as $key => $value)
					{
						$userGroup = array();
						$userGroup["group_id"] = $item['id'];
						$userGroup["user_id"] = $value;

						//--Add user to the group
						BackendMailengineModel::insertUserToGroup($userGroup);
					}
				}
				BackendModel::triggerEvent($this->getModule(), 'after_add_group', $item);
				$this->redirect(BackendModel::createURLForAction('groups') . '&report=added&highlight=row-' . $item['id']);
			}
		}
	}
}