<?php

namespace Backend\Modules\Media\Ajax;

use Backend\Core\Engine\Base\AjaxAction;
use Backend\Core\Engine\Form AS BackendForm;
use Backend\Modules\Media\Engine\Helper as BackendMediaHelper;
use Frontend\Core\Engine\Language AS FrontendLanguage;
use Backend\Core\Engine\Template;
use Backend\Core\Engine\Model as BackendModel;

/**
 * This is an ajax handler
 *
 * @author Nick Vandevenne <nick@comsa.be>
 */
class Plupload extends AjaxAction
{
	/**
	 * @var BackendMediaHelper
	 */
	private $media;

	/**
	 * Contstructor
	 *
	 */
	public function execute()
	{
		parent::execute();

		//--Set post var to check submit
		$_POST["form"] = "add_image";
		//--Set module
        $module = (string)\SpoonFilter::getPostValue('mediaModule', null, '', 'string');
        //--Set action
        $action = (string)\SpoonFilter::getPostValue('mediaAction', null, '', 'string');
        //--Set the id
        $id = (int) \SpoonFilter::getPostValue('mediaId', null, '', 'int');
        //--Set the type
        $type = (string) \SpoonFilter::getPostValue('mediaType', null, '', 'string');
		//--Create media helper
		$this->media = new BackendMediaHelper(new BackendForm('add_image',null,'post',false), $module, $id, $action, $type);
		//--Validate media -> upload file
		$this->media->validate();

        //--File is image
        if($this->media->item['filetype'] == 1)
        {
            //Create html
            $tpl = new Template();

            $this->media->item['txtText'] = $this->media->frm->addTextarea("text-" . $this->media->item["id"], $this->media->item['text'])->setAttribute('style', 'resize: none;')->parse();
            //--Get file info (ext, filename, path)
            $path_parts = pathinfo(FRONTEND_FILES_PATH . '/Media/Images/Source/' . $this->media->item['filename']);
            $this->media->item['name'] = $path_parts['filename'];
            $folders = BackendModel::getThumbnailFolders(FRONTEND_FILES_PATH . '/Media/Images', true);

            foreach ($folders as $folder)
            {
                $this->media->item['image_' . $folder['dirname']] = $folder['url'] . '/' . $folder['dirname'] . '/' . $this->media->item['filename'];
            }

            $tpl->assign('mediaItems', array('images' => array($this->media->item)));

            $html = $tpl->getContent(BACKEND_MODULES_PATH . '/Media/Layout/Templates/Ajax/Image.tpl');
        //--File is file
        }else{
            //Create html
            $tpl = new Template();

            $this->media->item['txtText'] = $this->media->frm->addTextarea("text-" . $this->media->item["id"], $this->media->item['text'])->setAttribute('style', 'resize: none;')->parse();
            //--Get file info (ext, filename, path)
            $path_parts = pathinfo(FRONTEND_FILES_PATH . '/Media/Files/' . $this->media->item['filename']);
            $this->media->item['url'] = FRONTEND_FILES_URL . '/Media/Files/' . $this->media->item['filename'];
            $this->media->item['name'] = $path_parts['filename'];

            $tpl->assign('mediaItems', array('files' => array($this->media->item)));

            $html = $tpl->getContent(BACKEND_MODULES_PATH . '/Media/Layout/Templates/Ajax/File.tpl');
        }
		// output (filetype, html)
		$this->output(self::OK, array($this->media->item['filetype'], $html), FrontendLanguage::msg('Success'));
	}
}