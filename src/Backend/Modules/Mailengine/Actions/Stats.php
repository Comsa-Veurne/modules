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
use Backend\Core\Engine\DataGridDB as BackendDataGridDB;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the Stats action
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Stats extends BackendBaseAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		$this->loadDataGrid();
		$this->parse();
		$this->display();
	}

	/**
	 * Load the dataGrid
	 */
	protected function loadDataGrid()
	{
		$this->dataGrid = new BackendDataGridDB(BackendMailengineModel::QRY_DATAGRID_BROWSE_MAIL_STATS);

		// sorting columns
		$this->dataGrid->setSortingColumns(array('date', 'subject', 'date', 'users'));
		$this->dataGrid->setSortParameter('desc');

		$this->dataGrid->setColumnFunction(array(new BackendDataGridFunctions(), 'getLongDate'), array('[date]'), 'date', true);

		// check if this action is allowed
		$this->dataGrid->setColumnURL('subject', BackendModel::createURLForAction('detail_stat') . '&amp;id=[id]');
		$this->dataGrid->setColumnURL('date', BackendModel::createURLForAction('detail_stat') . '&amp;id=[id]');
		$this->dataGrid->setColumnURL('users', BackendModel::createURLForAction('detail_stat') . '&amp;id=[id]');
//		$this->dataGrid->setColumnURL('opened', BackendModel::createURLForAction('detail_stat') . '&amp;id=[id]');
//		$this->dataGrid->setColumnURL('percentage', BackendModel::createURLForAction('detail_stat') . '&amp;id=[id]');
		$this->dataGrid->addColumn('detail', null, ucfirst(BL::lbl('View')), BackendModel::createURLForAction('detail_stat') . '&amp;id=[id]', ucfirst(BL::lbl('View')));

	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		// parse the dataGrid if there are results
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
	}
}
