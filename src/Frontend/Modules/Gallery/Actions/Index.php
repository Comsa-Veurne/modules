<?php

namespace Frontend\Modules\Gallery\Actions;

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Modules\Gallery\Engine\Model as FrontendGalleryModel;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the Index-action, it will display the overview of galleria posts
 *
 * @author John Poelman <john.poelman@bloobz.be>
 */
class Index extends FrontendBaseBlock
{
	/**
	 * The record data
	 *
	 * @var array
	 */
	private $record;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->loadTemplate();
		$this->loadData();

        //--Add css
        $this->header->addCSS('/src/Frontend/Modules/' . $this->getModule() . '/Layout/Css/Gallery.css');
        //$this->header->addCSS('/src/Frontend/Modules/' . $this->getModule() . '/Layout/Css/Colorbox.css');

        //--Add javascript
        //$this->header->addJS('/src/Frontend/Modules/' . $this->getModule() . '/Js/Jquery.colorbox-min.js');

		$this->parse();
	}

	/**
	 * Load the data
	 */
	protected function loadData()
	{
		$this->record = FrontendGalleryModel::getAlbumsForOverview();
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		$this->tpl->assign('items', $this->record);
	}
}