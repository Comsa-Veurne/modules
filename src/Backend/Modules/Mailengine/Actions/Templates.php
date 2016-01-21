<?php
namespace Backend\Modules\Mailengine\Actions;

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Base\Action as BackendBaseAction;
use Backend\Core\Engine\DataGridDB as BackendDataGridDB;
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
 * This is the Templates action
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Templates extends BackendBaseAction
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
     * Load the datagrid
     */
    protected function loadDataGrid()
    {
        $this->dataGrid = new BackendDataGridDB(BackendMailengineModel::QRY_DATAGRID_BROWSE_TEMPLATES);

        // sorting columns
        $this->dataGrid->setSortingColumns(array('title'));
        $this->dataGrid->setSortParameter('asc');

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('EditTemplate')) {
            $this->dataGrid->setColumnURL('title', BackendModel::createURLForAction('edit_template') . '&amp;id=[id]');
            $this->dataGrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit_template') . '&amp;id=[id]', BL::lbl('Edit'));
        }
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