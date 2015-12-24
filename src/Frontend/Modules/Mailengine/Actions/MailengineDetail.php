<?php
namespace Frontend\Modules\Mailengine\Actions;

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Modules\Mailengine\Engine\Model as FrontendMailengineModel;
/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the Detail-action, it will display the overview of mailengine posts
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class MailengineDetail extends FrontendBaseBlock
{
	/**
	 * The record data
	 *
	 * @var array
	 */
	private $record;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		//--Get the id
		$this->id = \SpoonFilter::getGetValue('id', null, 0, 'int');

		//--Check if mailing exist
		if(!FrontendMailengineModel::extistSend($this->id))
		{
			$this->redirect(FrontendNavigation::getURL(404));
		}

		$this->loadTemplate();
		$this->loadData();
		$this->parse();
	}

	/**
	 * Load the data
	 */
	protected function loadData()
	{
		//--Get the send mailing
		$this->record = FrontendMailengineModel::getSend($this->id);

		//--Create iframe
		$iframe = FrontendNavigation::getURLForBlock('Mailengine', 'MailenginePreview') . "?id=" . $this->id;
		$this->tpl->assign('iframe', $iframe);
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		$this->tpl->assign('item', $this->record);

	}
}
