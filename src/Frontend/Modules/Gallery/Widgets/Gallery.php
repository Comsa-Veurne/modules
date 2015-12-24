<?php

namespace Frontend\Modules\Gallery\Widgets;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */
use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Modules\Gallery\Engine\Model as FrontendGalleryModel;

/**
 * This is a frontend widget
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Gallery extends FrontendBaseWidget
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
		$this->header->addCSS('/src/Frontend/Modules/' . $this->getModule() . '/Layout/Css/colorbox.css');

		//--Add javascript
		$this->header->addJS('/src/Frontend/Modules/' . $this->getModule() . '/Js/Jquery.colorbox-min.js');
		$this->header->addJS('/src/Frontend/Modules/' . $this->getModule() . '/Js/Jquery.cycle.all.js');

		$this->loadTemplate();
		$this->loadData();

		$this->parse();
	}

	/**
	 * Load the data
	 */
	private function loadData()
	{
        $this->album = FrontendGalleryModel::getAlbumById($this->data['id']);
        $this->record = FrontendGalleryModel::getImagesForAlbum($this->data['id']);
	}

	/**
	 * Parse the widget
	 */
	protected function parse()
	{
	        if(count($this->record) > 4)
	        {
	            $this->tpl->assign('showMoreButton', true);
	        }
		$this->tpl->assign('widgetGallery', $this->record);
        $this->tpl->assign('item', $this->album);
    }
}
