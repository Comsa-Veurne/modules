<?php
namespace Backend\Modules\Mailengine\Actions;

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Mailengine\Engine\Model as BackendMailengineModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;

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
class Edit extends BackendBaseActionEdit
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

		$this->record = BackendMailengineModel::get($this->id);
	}

	/**
	 * Load the form
	 */
	protected function loadForm()
	{
		// set hidden values
		$rbtHiddenValues[] = array('label' => BL::lbl('Published'), 'value' => 'N');
		$rbtHiddenValues[] = array('label' => BL::lbl('Hidden'), 'value' => 'Y');

		$rbtShowWebsiteValues[] = array('label' => BL::lbl('Yes'), 'value' => 'Y');
		$rbtShowWebsiteValues[] = array('label' => BL::lbl('No'), 'value' => 'N');

		$templates = BackendMailengineModel::getAllTemplatesForDropdown();

		// create form
		$this->frm = new BackendForm('edit');
		$this->frm->addText('subject', $this->record['subject'], null, 'inputText title', 'inputTextError title');
		$this->frm->addEditor('text', $this->record['text']);
		$this->frm->addRadiobutton('hidden', $rbtHiddenValues, $this->record['hidden']);
		$this->frm->addDropdown("template_id", $templates, $this->record['template_id']);
		$this->frm->addRadiobutton('show_on_website', $rbtShowWebsiteValues, $this->record['show_on_website']);

		// meta
		$this->meta = new BackendMeta($this->frm, $this->record['meta_id'], 'subject', true);
		$this->meta->setUrlCallback('Backend\Modules\Mailengine\Engine\Model', 'getUrl', array($this->record['id']));
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{

		parent::parse();
		$this->tpl->assign('item', $this->record);
		$this->tpl->assign('iframe', BackendModel::createURLForAction('preview') . "&id=" . $this->id);
		// get url
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
				$item['subject'] = $fields['subject']->getValue();
				$item['text'] = $fields['text']->getValue();
				$item['hidden'] = $fields['hidden']->getValue();
				$item['language'] = BL::getWorkingLanguage();
				$item['template_id'] = $fields['template_id']->getValue();
				$item['show_on_website'] = $fields['show_on_website']->getValue();

				BackendMailengineModel::update($this->id, $item);
				$item['id'] = $this->id;

				BackendSearchModel::saveIndex($this->getModule(), $item['id'], array('title' => $item['subject'], 'text' => $item['text']));

				BackendModel::triggerEvent($this->getModule(), 'after_edit', $item);
				$this->redirect(BackendModel::createURLForAction('index') . '&report=edited&highlight=row-' . $item['id']);
			}
		}
	}
}