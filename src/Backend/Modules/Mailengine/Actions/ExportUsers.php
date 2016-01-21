<?php
namespace Backend\Modules\Mailengine\Actions;

use Backend\Core\Engine\Base\Action as BackendBaseAction;
use Backend\Modules\Mailengine\Engine\Model as BackendMailengineModel;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the ExportUsers action
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class ExportUsers extends BackendBaseAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		//--Get the id
		$id = \SpoonFilter::getGetValue('id', null, 0);

		//--Export users
		BackendMailengineModel::exportUsers($id);


	}


}