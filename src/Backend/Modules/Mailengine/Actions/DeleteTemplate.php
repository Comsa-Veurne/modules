<?php
namespace Backend\Modules\Mailengine\Actions;

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Mailengine\Engine\Model as BackendMailengineModel;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the elete-action, it deletes an item
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class DeleteTemplate extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id !== null && BackendMailengineModel::existsTemplate($this->id))
		{
			parent::execute();
			$this->record = (array)BackendMailengineModel::getTemplate($this->id);

			BackendMailengineModel::deleteTemplate($this->id);

			BackendModel::triggerEvent($this->getModule(), 'after_delete_template', array('id' => $this->id));

			$this->redirect(BackendModel::createURLForAction('templates') . '&report=deleted&var=' . urlencode($this->record['title']));
		}
		else $this->redirect(BackendModel::createURLForAction('templates') . '&error=non-existing');
	}
}