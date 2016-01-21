<?php

namespace Backend\Modules\Gallery\Ajax;

use Backend\Core\Engine\Base\AjaxAction;
use Backend\Modules\Gallery\Engine\Model as BackendGalleryModel;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * Delete image
 *
 * @author Nick Vandevenne <nick@comsa.be>
 */
class DeleteFile extends AjaxAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        //--Get the id
        $id = \SpoonFilter::getPostValue('id', null, '', 'string');

        //--Check if the id is not empty
        if(!empty($id))
        {
            //--Delete file
            BackendGalleryModel::delete($id);
        }

        // success output
        $this->output(self::OK, null, 'image deleted');
    }
}