<?php
namespace Frontend\Modules\Mailengine\Actions;

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Mailengine\Engine\Model as FrontendMailengineModel;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the Click-action, it will display the overview of mailengine posts
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class MailengineClick extends FrontendBaseBlock
{

	/**
	 * Execute the action
	 */
	public function execute()
	{

		parent::execute();

		//--Get the id
		$id = $this->URL->getParameter(1);

		//--check if the id is not empty
		if(empty($id))
		{
			$this->redirect(FrontendNavigation::getURL(404));
		}

		//--Explode the id
		$ids = explode("-", $id);

		//--check if the id contains 2 elements
		if(count($ids) != 2)
		{
			$this->redirect(FrontendNavigation::getURL(404));
		}

		//--Get the ids and decrypt
		$link_id = (int)FrontendMailengineModel::decryptId($ids[0]);
		$user_id = (int)FrontendMailengineModel::decryptId($ids[1]);

		//--check if the ids are integers
		if($link_id <= 0)
		{
			//--Redirect to 404
			$this->redirect(FrontendNavigation::getURL(404));
		}

		//--Only if userid > 0 (because of the preview on the frontend)
		if($user_id > 0)
		{
			$data = array();
			$data["link_id"] = $link_id;
			$data["user_id"] = $user_id;

			//--Add click-link to the database
			FrontendMailengineModel::insertLinkClicked($data);
		}

		//--Redirect the page
		$this->redirectLink($link_id);

		//--Stop the script
		die();
	}

	/*
	*
	* Get the link and redirect
	*
	*/
	protected function redirectLink($link_id)
	{
		//--Get the link
		$link = FrontendMailengineModel::getLink($link_id);

		//--Check if the link is empty -> redirect 404
		if(empty($link))
		{
			$this->redirect(FrontendNavigation::getURL(404));
		}

		//--Redirect
		$this->redirect($link["url"]);
	}
}