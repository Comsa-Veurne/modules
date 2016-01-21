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
class AddUser extends BackendBaseActionAdd
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
		$this->frm->addText('name');
		$this->frm->addText('email');

		//--Get all the users
		$groups = BackendMailengineModel::getAllGroups();

		//--Check if there are groups
		if(!empty($groups))
		{

			//--Loop all the group
			foreach($groups as $key => &$group)
			{
				$groupCheckboxes[] = array("label" => $group["title"], "value" => $group["id"]);
			}

			//--Add multicheckboxes to form
			$this->frm->addMultiCheckbox("groups", $groupCheckboxes);
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
			$fields['name']->isFilled(BL::err('FieldIsRequired'));
			$fields['email']->isFilled(BL::err('FieldIsRequired'));
			$fields['email']->isEmail(BL::err('EmailIsInvalid'));

			if($this->frm->isCorrect())
			{
				$item['name'] = $fields['name']->getValue();
				$item['email'] = $fields['email']->getValue();
				$item['language'] = BL::getWorkingLanguage();

				$item['id'] = BackendMailengineModel::insertUser($item);

				//--Check if there are groups
				if(isset($fields['groups']))
				{

					//--Get all the groups
					$groups = $fields["groups"]->getValue();
					foreach($groups as $key => $value)
					{
						$groupUser = array();
						$groupUser["user_id"] = $item['id'];
						$groupUser["group_id"] = $value;

						//--Add user to the group
						BackendMailengineModel::insertUserToGroup($groupUser);
					}
				}

				BackendModel::triggerEvent($this->getModule(), 'after_add_user', $item);
				$this->redirect(BackendModel::createURLForAction('users') . '&report=added&highlight=row-' . $item['id']);
			}
		}
	}
}