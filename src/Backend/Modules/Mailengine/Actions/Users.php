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
 * This is the Users action
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Users extends BackendBaseAction
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
		$this->dataGrid->setSortingColumns(array('created_on', 'email', 'name', 'active'));
		$this->dataGrid->setSortParameter('desc');

		$this->dataGrid->setColumnFunction(array(new BackendDataGridFunctions(), 'getLongDate'), array('[created_on]'), 'created_on', true);


		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('EditUser'))
		{
			$this->dataGrid->setColumnURL('email', BackendModel::createURLForAction('edit_user') . '&amp;id=[id]');
			$this->dataGrid->setColumnURL('name', BackendModel::createURLForAction('edit_user') . '&amp;id=[id]');
			$this->dataGrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit_user') . '&amp;id=[id]', BL::lbl('Edit'));
		}
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		// parse the dataGrid if there are results
		if(isset($this->filter['group']))
		{
			$this->tpl->assign('id', $this->filter['group']);
		}
		else
		{
			$this->tpl->assign('id', 0);
		}
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
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
		$groups = BackendMailengineModel::getAllGroupsForDropdown();
		$groups = array("0" => "") + $groups;

		// multiple categories?
		if(count($groups) > 1)
		{
			// create element
			$this->frm->addDropdown('group', $groups, $this->filter["group"]);
			$this->frm->getField('group')->setDefaultElement('');
		}

		// manually parse fields
		$this->frm->parse($this->tpl);
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
	 * Builds the query for this datagrid.
	 *
	 * @return array        An array with two arguments containing the query and its parameters.
	 */
	private function buildQuery()
	{
		//--init var
		$parameters = array();

		//--construct the query in the controller instead of the model as an allowed exception for data grid usage
		$query = 'SELECT i.id, i.email, i.name,i.active, UNIX_TIMESTAMP(i.created_on) as created_on FROM mailengine_users AS i';
		$where = array();

		//--add name
		if(isset($this->filter['name']))
		{
			$where[] = 'i.name LIKE ?';
			$parameters[] = '%' . $this->filter['name'] . '%';
		}

		//--add email
		if(isset($this->filter['email']))
		{
			$where[] = 'i.email LIKE ?';
			$parameters[] = '%' . $this->filter['email'] . '%';
		}

		//--add group
		if(isset($this->filter['group']))
		{
			$query .= ' INNER JOIN mailengine_users_group AS mug ON mug.user_id = i.id';
			$where[] .= 'mug.group_id = ?';
			$parameters[] = $this->filter['group'];
		}

		//-- query
		if(!empty($where))
		{
			$query .= ' WHERE ' . implode(' AND ', $where);
		}

		//--group by profile (might have doubles because of the join on groups_rights)
		$query .= ' GROUP BY i.id';

		//--query with matching parameters
		return array($query, $parameters);
	}
}
