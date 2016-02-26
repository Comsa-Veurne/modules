<?php

namespace Backend\Modules\Media\Ajax;

use Backend\Core\Engine\Base\AjaxAction;
use Backend\Modules\Media\Engine\Model as BackendMediaModel;
use Backend\Core\Engine\Model as BackendModel;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * Delete mediaitems
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
        //--Create filesystem for file actions
        $fs = new Filesystem();
        //--Get all image folders defined by sizes
        $folders = BackendModel::getThumbnailFolders(FRONTEND_FILES_PATH . '/Media/Images', true);

        //--Check if the id is not empty
        if (!empty($ids))
        {
            foreach ($ids as $id)
            {
                //--Get media link from id
                $mediaModule = BackendMediaModel::getMediaModule($id);
                //--Delete link from mediaitem to item
                BackendMediaModel::deleteLink($id);
                //--Check if there are any other links to the mediaitem
                if(!BackendMediaModel::existsMediaModules($id))
                {
                    //--Get mediaitem
                    $media = BackendMediaModel::get($mediaModule['media_id']);
                    //--Delete files
                    if($media['filetype'] == 1){

                        if($fs->exists(FRONTEND_FILES_PATH . '/Media/Images/Source/' . $media['filename']))
                            $fs->remove(FRONTEND_FILES_PATH . '/Media/Images/Source/' . $media['filename']);

                        foreach($folders as $folder)
                        {
                            if($fs->exists(FRONTEND_FILES_PATH . '/Media/Images/' . $folder['dirname'] . '/' . $media['filename']))
                                $fs->remove(FRONTEND_FILES_PATH . '/Media/Images/' . $folder['dirname'] . '/' . $media['filename']);
                        }
                    }else if($media['filetype'] == 2)
                    {
                        if($fs->exists(FRONTEND_FILES_PATH . '/Media/Files/' . $media['filename']))
                            $fs->remove(FRONTEND_FILES_PATH . '/Media/Files/' . $media['filename']);
                    }
                    //--Delete mediaitem
                    BackendMediaModel::delete($mediaModule['media_id']);
                }
            }
        }

        // success output
        $this->output(self::OK, null, 'files deleted');
    }
}