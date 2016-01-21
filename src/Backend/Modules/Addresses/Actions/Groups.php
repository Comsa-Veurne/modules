<?php

namespace Backend\Modules\Addresses\Actions;

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Base\Action as BackendBaseAction;
use Backend\Core\Engine\DataGridDB as BackendDataGridDB;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Addresses\Engine\Model as BackendAddressesModel;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the Groups action
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Groups extends BackendBaseAction
{
    /**
     * The category where is filtered on
     *
     * @var    array
     */
    private $group;

    /**
     * The id of the category where is filtered on
     *
     * @var    int
     */
    private $groupId;

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        $this->groupId = \SpoonFilter::getGetValue('group', null, null, 'int');
        if ($this->groupId == 0) {
            $this->groupId = null;
        } else {
            // get category
            $this->group = BackendAddressesModel::getGroup($this->groupId);

            // reset
            if (empty($this->group)) {
                // reset GET to trick Spoon
                $_GET['group'] = null;

                // reset
                $this->groupId = null;
            }
        }

        $this->loadDataGrid();
        $this->loadFilterForm();
        $this->parse();
        $this->display();
    }

    /**
     * Load the dataGrid
     */
    protected function loadDataGrid()
    {

        if ($this->groupId != null) {
            // create datagrid
            $this->dataGrid = new BackendDataGridDB(BackendAddressesModel::QRY_DATAGRID_BROWSE_GROUPS_WITH_GROUPID, array($this->groupId));

            // set the URL
            $this->dataGrid->setSortingColumns(array('title'));

            $this->dataGrid->setURL('&amp;category=' . $this->groupId, true);
        } else {
            $this->dataGrid = new BackendDataGridDB(BackendAddressesModel::QRY_DATAGRID_BROWSE_GROUPS);
            $this->dataGrid->setSortingColumns(array('title', 'parent_title'));
        }

        // sorting columns
        $this->dataGrid->setSortParameter('asc');

        $this->dataGrid->enableSequenceByDragAndDrop();

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('EditGroup')) {
            $this->dataGrid->setColumnURL('title', BackendModel::createURLForAction('edit_group') . '&amp;id=[id]');
            //$this->dataGrid->setColumnURL('addresses', BackendModel::createURLForAction('edit_group') . '&amp;id=[id]#tabAddresses');
            $this->dataGrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit_group') . '&amp;id=[id]', BL::lbl('Edit'));
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

    private function loadFilterForm()
    {
        // get categories
        $groups = BackendAddressesModel::getAllGroupsTree();
        //$groups = array('') + $groups;

        // multiple categories?
        if (count($groups) > 1) {
            // create form
            $frm = new BackendForm('filter', null, 'get', false);

            // create element
            $frm->addDropdown('group', $groups, $this->groupId);
//			$frm->getField('category')->setDefaultElement('');

            // parse the form
            $frm->parse($this->tpl);
        }

        // parse category
        if (!empty($this->category)) {
            $this->tpl->assign('filterGroup', $this->group);
        }
    }
}