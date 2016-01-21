<?php
namespace Backend\Modules\Mailengine\Actions;

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Base\ActionAdd as BackendBaseActionIndex;
use Backend\Core\Engine\DataGridDB as BackendDataGridDB;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
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
 * This is the index-action (default), it will display the overview of mailengine posts
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Index extends BackendBaseActionIndex
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
        $this->dataGrid = new BackendDataGridDB(BackendMailengineModel::QRY_DATAGRID_BROWSE_MAILS);

        // sorting columns
        $this->dataGrid->setSortingColumns(array('subject'));
        $this->dataGrid->setSortParameter('asc');

        $this->dataGrid->setColumnFunction(array(new BackendDataGridFunctions(), 'getLongDate'), array('[date]'), 'date', true);

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Edit')) {
            $this->dataGrid->setColumnURL('subject', BackendModel::createURLForAction('edit') . '&amp;id=[id]');
            $this->dataGrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::lbl('Edit'));
            $this->dataGrid->addColumn('detail', null, BL::lbl('Preview'), BackendModel::createURLForAction('edit') . '&amp;id=[id]#tabPreview', BL::lbl('Preview'));
            $this->dataGrid->addColumn('test', null, BL::lbl('SendTest'), BackendModel::createURLForAction('test') . '&amp;id=[id]', BL::lbl('SendTest'));
            $this->dataGrid->addColumn('approve', null, BL::lbl('Send'), BackendModel::createURLForAction('send') . '&amp;id=[id]', BL::lbl('Send'));
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