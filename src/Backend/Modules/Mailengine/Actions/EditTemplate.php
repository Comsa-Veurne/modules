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
class EditTemplate extends BackendBaseActionEdit
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
		if($this->id == null || !BackendMailengineModel::existsTemplate($this->id))
		{
			$this->redirect(BackendModel::createURLForAction('templates') . '&error=non-existing');
		}

		$this->record = BackendMailengineModel::getTemplate($this->id);
	}

	/**
	 * Load the form
	 */
	protected function loadForm()
	{

		$rbtHiddenValues[] = array('label' => BL::lbl('Published'), 'value' => 'N');
		$rbtHiddenValues[] = array('label' => BL::lbl('Hidden'), 'value' => 'Y');

		$this->frm = new BackendForm('edit');
		$this->frm->addText('title', $this->record['title'], null, 'inputText title', 'inputTextError title');
		$this->frm->addText("from_email", $this->record['from_email']);
		$this->frm->addText("from_name", $this->record['from_name']);
		$this->frm->addText("reply_email", $this->record['reply_email']);
		$this->frm->addText("reply_name", $this->record['reply_name']);
		$this->frm->addEditor("template", $this->record['template'],"inputEditorNewsletter");
		$this->frm->addTextarea("css", $this->record['css']);
		$this->frm->addRadiobutton('hidden', $rbtHiddenValues, $this->record['hidden']);
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
			$fields['from_email']->isFilled(BL::err('FieldIsRequired'));
			$fields['from_name']->isFilled(BL::err('FieldIsRequired'));
			$fields['reply_email']->isFilled(BL::err('FieldIsRequired'));
			$fields['reply_name']->isFilled(BL::err('FieldIsRequired'));
			$fields['template']->isFilled(BL::err('FieldIsRequired'));

			$fields['from_email']->isEmail(BL::err('EmailIsInvalid'));
			$fields['reply_email']->isEmail(BL::err('EmailIsInvalid'));

			if($this->frm->isCorrect())
			{
				$item['title'] = $fields['title']->getValue();
				$item['from_email'] = $fields['from_email']->getValue();
				$item['from_name'] = $fields['from_name']->getValue();
				$item['reply_email'] = $fields['reply_email']->getValue();
				$item['reply_name'] = $fields['reply_name']->getValue();
				$item['template'] = $fields['template']->getValue();
				$item['css'] = $fields['css']->getValue();
				$item['hidden'] = $fields['hidden']->getValue();

				BackendMailengineModel::updateTemplate($this->id, $item);
				$item['id'] = $this->id;


				BackendModel::triggerEvent($this->getModule(), 'after_edit_template', $item);
				$this->redirect(BackendModel::createURLForAction('templates') . '&report=edited&highlight=row-' . $item['id']);
			}
		}
	}
}