<?php

namespace Backend\Modules\Blocks\Actions;

use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\Base\ActionIndex;
use Backend\Core\Engine\DataGridDB;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Model;
use Backend\Modules\Blocks\Engine\Model as BackendBlocksModel;

/**
 * This is the categories-action, it will display the overview of categories
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Categories extends ActionIndex
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
    private function loadDataGrid()
    {
        $this->dataGrid = new DataGridDB(
            BackendBlocksModel::QRY_DATAGRID_BROWSE_CATEGORIES, Language::getWorkingLanguage()
        );

        // check if this action is allowed
        if (Authentication::isAllowedAction('EditCategory')) {
            $this->dataGrid->addColumn(
                'edit', null, Language::lbl('Edit'), Model::createURLForAction('EditCategory') . '&amp;id=[id]', Language::lbl('Edit')
            );

            $this->dataGrid->setColumnURL(
                'title', Model::createURLForAction('EditCategory') . '&amp;id=[id]'
            );

        }

        // sequence
        $this->dataGrid->enableSequenceByDragAndDrop();
        $this->dataGrid->setAttributes(array('data-action' => 'sequence_categories'));
    }

    /**
     * Parse & display the page
     */
    protected function parse()
    {
        $this->tpl->assign('dataGrid', (string)$this->dataGrid->getContent());
    }
}