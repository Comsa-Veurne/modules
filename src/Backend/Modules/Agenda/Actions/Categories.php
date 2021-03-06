<?php

namespace Backend\Modules\Agenda\Actions;

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\DataGridDB as BackendDataGridDB;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Agenda\Engine\Model as BackendAgendaModel;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the categories action, it will display the overview of Agenda categories.
 * @author Tim van Wolfswinkel <tim@webleads.nl>
 * @author Bram De Smyter <bram@bubblefish.be>
 */
class Categories extends BackendBaseActionIndex
{
    /**
     * Deny the use of multiple categories
     *
     * @param bool
     */
    private $multipleCategoriesAllowed;

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
     * Loads the dataGrid
     */
    private function loadDataGrid()
    {
        // are multiple categories allowed?
        $this->multipleCategoriesAllowed = BackendModel::get('fork.settings')->getForModule('Agenda', 'allow_multiple_categories', true);

        // create dataGrid
        $this->dataGrid = new BackendDataGridDB(BackendAgendaModel::QRY_DATAGRID_BROWSE_CATEGORIES,
            BL::getWorkingLanguage());
        $this->dataGrid->setHeaderLabels(array('num_items' => ucfirst(BL::lbl('Amount'))));
        if ($this->multipleCategoriesAllowed) {
            $this->dataGrid->enableSequenceByDragAndDrop();
        } else {
            $this->dataGrid->setColumnsHidden(array('sequence'));
        }
        $this->dataGrid->setRowAttributes(array('id' => '[id]'));
        $this->dataGrid->setPaging(false);

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Index')) {
            $this->dataGrid->setColumnFunction(array(__CLASS__, 'setClickableCount'),
                array('[num_items]', BackendModel::createURLForAction('index') . '&amp;category=[id]'), 'num_items',
                true);
        }

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('EditCategory')) {
            $this->dataGrid->setColumnURL('title', BackendModel::createURLForAction('edit_category') . '&amp;id=[id]');
            $this->dataGrid->addColumn('edit', null, BL::lbl('Edit'),
                BackendModel::createURLForAction('edit_category') . '&amp;id=[id]', BL::lbl('Edit'));
        }
    }

    /**
     * Parse & display the page
     */
    protected function parse()
    {
        parent::parse();

        $this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('AddCategory') && $this->multipleCategoriesAllowed) {
            $this->tpl->assign('showAgendaAddCategory', true);
        } else {
            $this->tpl->assign('showAgendaAddCategory', false);
        }
    }

    /**
     * Convert the count in a human readable one.
     *
     * @param int $count
     * @param string $link
     * @return string
     */
    public static function setClickableCount($count, $link)
    {
        // redefine
        $count = (int)$count;
        $link = (string)$link;

        // return link in case of more than one item, one item, other
        if ($count > 1) {
            return '<a href="' . $link . '">' . $count . ' ' . BL::getLabel('Agenda') . '</a>';
        }
        if ($count == 1) {
            return '<a href="' . $link . '">' . $count . ' ' . BL::getLabel('Agenda') . '</a>';
        } else {
            return BL::getLabel('NoAgenda');
        }
    }
}