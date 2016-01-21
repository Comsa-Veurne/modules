<?php

namespace Frontend\Modules\Gallery\Widgets;

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Modules\Gallery\Engine\Model as FrontendGalleriaModel;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */
/**
 * This is a frontend widget
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Slideshow extends FrontendBaseWidget
{
	/**
	 * @var array
	 */
	private $record;

	/**
	 * Exceute the action
	 */
	public function execute()
	{
		parent::execute();

		//--Add css
		$this->header->addCSS('/src/Frontend/Modules/' . $this->getModule() . '/Layout/Css/Gallery.css');
		//$this->header->addCSS('/src/Frontend/Modules/' . $this->getModule() . '/Layout/Css/Colorbox.css');

		//--Add javascript (No we won't, add these to your theme...)
		//$this->header->addJS('/src/Frontend/Modules/' . $this->getModule() . '/Js/Jquery.colorbox-min.js');
        //$this->header->addJS('/src/Frontend/Modules/' . $this->getModule() . '/Js/jquery.cycle2.min.js');
        //$this->header->addJS('/src/Frontend/Modules/' . $this->getModule() . '/Js/jquery.cycle2.center.min.js');

		$this->loadTemplate();
		$this->loadData();

		$this->parse();
	}

	/**
	 * Load the data
	 */
	private function loadData()
	{
		$this->record = FrontendGalleriaModel::getImagesForAlbum($this->data['id']);;
	}

	/**
	 * Parse the widget
	 */
	protected function parse()
	{
		$this->tpl->assign('widgetSlideshow', $this->record);
	}
}
