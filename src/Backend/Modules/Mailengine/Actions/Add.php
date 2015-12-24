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
class Add extends BackendBaseActionAdd
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

		$rbtShowWebsiteValues[] = array('label' => BL::lbl('Yes'), 'value' => 'Y');
		$rbtShowWebsiteValues[] = array('label' => BL::lbl('No'), 'value' => 'N');

		$templates = BackendMailengineModel::getAllTemplatesForDropdown();

		$this->frm = new BackendForm('add');
		$this->frm->addText('subject', null, null, 'inputText title', 'inputTextError title');
		$this->frm->addEditor('text');
		$this->frm->addRadiobutton('hidden', $rbtHiddenValues, 'N');
		$this->frm->addRadiobutton('show_on_website', $rbtShowWebsiteValues, 'Y');
		$this->frm->addDropdown("template_id", $templates);

		$this->meta = new BackendMeta($this->frm, null, 'subject', true);
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		parent::parse();

		// assign the url for the detail page
		$url = BackendModel::getURLForBlock($this->URL->getModule(), 'detail');
		$url404 = BackendModel::getURL(404);
		if($url404 != $url) $this->tpl->assign('detailURL', SITE_URL . $url);
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
			$fields['subject']->isFilled(BL::err('FieldIsRequired'));
			$fields['text']->isFilled(BL::err('FieldIsRequired'));

			$this->meta->validate();

			if($this->frm->isCorrect())
			{
				$item['meta_id'] = $this->meta->save();
				$item['template_id'] = $fields['template_id']->getValue();;
				$item['subject'] = $fields['subject']->getValue();
				$item['text'] = $fields['text']->getValue();
				$item['hidden'] = $fields['hidden']->getValue();
				$item['show_on_website'] = $fields['show_on_website']->getValue();
				$item['language'] = BL::getWorkingLanguage();

				$item['id'] = BackendMailengineModel::insert($item);

				BackendSearchModel::saveIndex($this->getModule(), $item['id'], array('title' => $item['subject'], 'text' => $item['text']));

				BackendModel::triggerEvent($this->getModule(), 'after_add', $item);
				$this->redirect(BackendModel::createURLForAction('index') . '&report=added&highlight=row-' . $item['id']);
			}
		}
	}
}
