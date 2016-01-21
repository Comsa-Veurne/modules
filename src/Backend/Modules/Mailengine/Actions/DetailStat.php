<?php
namespace Backend\Modules\Mailengine\Actions;

use Backend\Core\Engine\Base\Action as BackendBaseAction;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Mailengine\Engine\Model as BackendMailengineModel;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the DetailStat action
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class DetailStat extends BackendBaseAction
{
	protected $mailsOpenedByDayChart = null;
	protected $mailsOpenedByHourChart = null;
	protected $linksClickedTotalChart = null;
	protected $linksClickedByDayChart = null;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		$this->loadData();
		$this->parse();
		$this->display();
	}

	protected function loadData()
	{
		$this->id = $this->getParameter('id', 'int', null);
		if($this->id == null || !BackendMailengineModel::existsStatsMail($this->id))
		{
			$this->redirect(BackendModel::createURLForAction('Stats') . '&error=non-existing');
		}

		$this->header->addJS('highcharts.js', 'Core',false);


		//--Get mail stats
		$this->record = BackendMailengineModel::getStatsMail($this->id);

		//--Get the opened mails by date
		$this->mailsOpenedByDayChart = BackendMailengineModel::getStatsMailOpenedByDay($this->id);

		//--Get the opened mails by date
		$this->mailsOpenedByHourChart = BackendMailengineModel::getStatsMailOpenedByHour($this->id);

		//--Get the clicked links
		$this->linksClickedTotalChart = BackendMailengineModel::getStatsLinksClickedTotal($this->id);

		//--Get the clicked links by day
		$this->linksClickedByDayChart = BackendMailengineModel::getStatsLinksClickedByDay($this->id);
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		$this->tpl->assign("record", $this->record);
		$this->tpl->assign("mailsOpenedByDayChart", $this->mailsOpenedByDayChart);
		$this->tpl->assign("mailsOpenedByHourChart", $this->mailsOpenedByHourChart);
		$this->tpl->assign("linksClickedTotalChart", $this->linksClickedTotalChart);
		$this->tpl->assign("linksClickedByDayChart", $this->linksClickedByDayChart);

		$this->tpl->assign('iframe', BackendModel::createURLForAction('OverlayStat') . "&id=" . $this->id);

	}
}