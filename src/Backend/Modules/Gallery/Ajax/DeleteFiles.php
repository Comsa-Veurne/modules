<?php

namespace Backend\Modules\Gallery\Ajax;

use Backend\Core\Engine\Base\AjaxAction;
use Backend\Modules\Gallery\Engine\Model as BackendGalleryModel;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * Delete images
 *
 * @author Nick Vandevenne <nick@comsa.be>
 */
class DeleteFiles extends AjaxAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        //--Get the ids as array
        $ids = \SpoonFilter::getPostValue('ids', null, '', 'array');

        //--Check if the id is not empty
        if (!empty($ids))
        {
            foreach ($ids as $id)
            {
                //--Delete file
                BackendGalleryModel::delete($id);
            }
        }

        // success output
        $this->output(self::OK, null, 'images deleted');
    }
}
