<?php

namespace Backend\Modules\Media\Ajax;

use Backend\Core\Engine\Base\AjaxAction;
use Backend\Modules\Media\Engine\Model as BackendMediaModel;
use Backend\Core\Engine\Form AS BackendForm;
use Backend\Modules\Media\Engine\Helper as BackendMediaHelper;

/**
 * Add link mediaitem to item
 *
 * @author Nick Vandevenne<nick@comsa.be>
 */
class AddLinks extends AjaxAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        //--Get the ids as array
        $ids = \SpoonFilter::getPostValue('ids', null, '', 'array');
        //--Set module
        $module = (string)\SpoonFilter::getPostValue('mediaModule', null, '', 'string');
        //--Set action
        $action = (string)\SpoonFilter::getPostValue('mediaAction', null, '', 'string');
        //--Set the id
        $id = (int) \SpoonFilter::getPostValue('mediaId', null, '', 'int');
        //--Set the type
        $type = (string) \SpoonFilter::getPostValue('mediaType', null, '', 'string');
        //--Create media object
        $media = new BackendMediaHelper(new BackendForm('add_image',null,'post',false), $module, $id, $action, $type);

        //--Check if the ids is not empty
        if (!empty($ids))
        {
            foreach ($ids as $id)
            {
                //--Link mediaitem with id to item
                $media->linkMediaToModule($id);
            }
        }

        // success output
        $this->output(self::OK, null, 'files added');
    }
}
