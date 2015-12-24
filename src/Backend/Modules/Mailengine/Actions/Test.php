<?php
namespace Backend\Modules\Mailengine\Actions;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

use Backend\Core\Engine\Base\Action as BackendBaseAction;
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
 * This is the Test action
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Test extends BackendBaseAction
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
		if($this->id == null || !BackendMailengineModel::exists($this->id))
		{
			$this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
		}
	}

	/**
	 * Load the form
	 */
	protected function loadForm()
	{
		// create form
		$this->frm = new BackendForm('add');
		$this->frm->addText('email');
	}

	/**
	 * Validate the form
	 */
	protected function validateForm()
	{
		if($this->frm->isSubmitted())
		{
			$this->frm->cleanupFields();

			$fields = $this->frm->getFields();
			$fields['email']->isFilled(BL::err('FieldIsRequired'));

			if($this->frm->isCorrect())
			{
				//--Get the mail
				$mailing = BackendMailengineModel::get($this->id);

				//--Get the template
				$template = BackendMailengineModel::getTemplate($mailing['template_id']);

				//--Create basic mail
				$text = BackendMailengineModel::createMail($mailing, $template);



				$mailing['from_email'] = $template['from_email'];
				$mailing['from_name'] = html_entity_decode($template['from_name']);
				$mailing['reply_email'] = $template['reply_email'];
				$mailing['reply_name'] = html_entity_decode($template['reply_name']);

				$emails = explode(',', $fields['email']->getValue());

				if(!empty($emails))
				{

					foreach($emails as $email)
					{
						$email = trim($email);

						if(\SpoonFilter::isEmail($email))
						{

							//--Send test mailing
							BackendMailengineModel::sendMail(html_entity_decode($mailing['subject']), $text, $email, 'Test Recepient', $mailing);
						}
					}
				}

				//--Redirect
				\SpoonHTTP::redirect(BackendModel::createURLForAction('index', $this->module) . "&id=" . $this->id . "&report=TestEmailSend");
			}
		}
		$this->frm->parse($this->tpl);
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		parent::parse();
	}

}
