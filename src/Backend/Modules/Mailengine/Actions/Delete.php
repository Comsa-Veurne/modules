<?php
namespace Backend\Modules\Mailengine\Actions;

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Mailengine\Engine\Model as BackendMailengineModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */
/**
 * This is the delete-action, it deletes an item
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Delete extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id !== null && BackendMailengineModel::exists($this->id))
		{
			parent::execute();
			$this->record = (array) BackendMailengineModel::get($this->id);

			BackendMailengineModel::delete($this->id);
			BackendSearchModel::removeIndex(
				$this->getModule(), $this->id
			);

			BackendModel::triggerEvent(
				$this->getModule(), 'after_delete',
				array('id' => $this->id)
			);

			$this->redirect(
				BackendModel::createURLForAction('index') . '&report=deleted&var=' . urlencode($this->record['subject'])
			);
		}
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}
}