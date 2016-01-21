<?php

namespace Backend\Modules\Addresses\Actions;

use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
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
 * This is the index-action (default), it will display the overview of addresses posts
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Index extends BackendBaseActionIndex
{
    /**
     * Filter variables.
     *
     * @var    array
     */
    private $filter;

    /**
     * Form.
     *
     * @var BackendForm
     */
    private $frm;

    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        $this->setFilter();
        $this->loadForm();
        $this->loadDataGrid();
        $this->parse();
        $this->display();
    }

    /**
     * Load the dataGrid
     */
    protected function loadDataGrid()
    {
        list($query, $parameters) = $this->buildQuery();

        // create datagrid
        $this->dataGrid = new BackendDataGridDB($query, $parameters);

        // overrule default URL
        $this->dataGrid->setURL(BackendModel::createURLForAction(null, null, null, array('offset' => '[offset]', 'order' => '[order]', 'sort' => '[sort]', 'email' => $this->filter['email'], 'name' => $this->filter['name'], 'group' => $this->filter['group']), false));

        // sorting columns
        $this->dataGrid->setSortingColumns(array('city', 'name'));
        $this->dataGrid->setSortParameter('asc');

//		$this->dataGrid->setColumnFunction(array('BackendDataGridFunctions', 'getLongDate'), array('[created_on]'), 'created_on', true);


        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Edit')) {
            $this->dataGrid->setColumnURL('name', BackendModel::createURLForAction('edit') . '&amp;id=[id]&amp;id=[id]');
            $this->dataGrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::lbl('Edit'));
            $this->dataGrid->addColumn('delete', null, BL::lbl('Delete'), BackendModel::createURLForAction('delete') . '&amp;id=[id]', BL::lbl('Delete'));
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

    /**
     * Sets the filter based on the $_GET array.
     */
    private function setFilter()
    {
        $this->filter['email'] = $this->getParameter('email');
        $this->filter['name'] = $this->getParameter('name');
        $this->filter['group'] = $this->getParameter('group');
    }

    /**
     * Load the form.
     */
    private function loadForm()
    {
        // create form
        $this->frm = new BackendForm('filter', BackendModel::createURLForAction(), 'get');

        // add fields
        $this->frm->addText('email', $this->filter['email']);
        $this->frm->addText('name', $this->filter['name']);

        // get categories
        $groups = BackendAddressesModel::getAllGroupsForDropdown();
        $groups = array("0" => "") + $groups;

        // multiple categories?
        if (count($groups) > 1) {
            // create element
            $this->frm->addDropdown('group', $groups, $this->filter["group"]);
            $this->frm->getField('group')->setDefaultElement('');
        }

        // manually parse fields
        $this->frm->parse($this->tpl);
    }

    /**
     * Builds the query for this datagrid.
     *
     * @return array        An array with two arguments containing the query and its parameters.
     */
    private function buildQuery()
    {
        //--init var
        $parameters = array();

        //--construct the query in the controller instead of the model as an allowed exception for data grid usage
        $query = 'SELECT i.id, company AS name, i.email, i.city FROM addresses AS i';
        $where = array();

        //--add name
        if (isset($this->filter['name'])) {
            $where[] = 'i.company LIKE ? ';
            $parameters[] = '%' . $this->filter['name'] . '%';
        }

        //--add email
        if (isset($this->filter['email'])) {
            $where[] = 'i.email LIKE ?';
            $parameters[] = '%' . $this->filter['email'] . '%';
        }

        //--add group
        if (isset($this->filter['group'])) {
            $query .= ' INNER JOIN addresses_in_group AS ag ON ag.address_id = i.id';
            $where[] .= 'ag.group_id = ?';
            $parameters[] = $this->filter['group'];
        }

        //-- query
        if (!empty($where)) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }

        //--group by profile (might have doubles because of the join on groups_rights)
        $query .= ' GROUP BY i.id';

        //--query with matching parameters
        return array($query, $parameters);
    }
}