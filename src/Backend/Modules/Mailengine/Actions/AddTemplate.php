<?php
namespace Backend\Modules\Mailengine\Actions;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionAdd;
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
 * This is the add-action, it will display a form to create a new item
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class AddTemplate extends BackendBaseActionAdd
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
		$rbtHiddenValues[] = array('label' => BL::lbl('Published'), 'value' => 'N');
		$rbtHiddenValues[] = array('label' => BL::lbl('Hidden'), 'value' => 'Y');

		$this->frm = new BackendForm('add');
		$this->frm->addText('title', null, null, 'inputText title', 'inputTextError title');
		//--Get from
		$from = BackendModel::get('fork.settings')->get('Core', 'mailer_from');
		//--Get reply
		$replyTo = BackendModel::get('fork.settings')->get('Core', 'mailer_reply_to');
		$this->frm->addText("from_email", $from['email']);
		$this->frm->addText("from_name", $from['name']);
		$this->frm->addText("reply_email", $replyTo['email']);
		$this->frm->addText("reply_name", $replyTo['name']);
		$this->frm->addEditor("template",'[[MAIL]]');
		$this->frm->addTextarea("css");
		$this->frm->addRadiobutton('hidden', $rbtHiddenValues, 'N');

		$this->meta = new BackendMeta($this->frm, null, 'title', true);
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

				$item['id'] = BackendMailengineModel::insertTemplate($item);

				BackendModel::triggerEvent($this->getModule(), 'after_add_template', $item);
				$this->redirect(BackendModel::createURLForAction('templates') . '&report=added&highlight=row-' . $item['id']);
			}
		}
	}
}
