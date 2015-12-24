<?php

namespace Backend\Modules\Media\Ajax;

use Backend\Core\Engine\Base\AjaxAction;
use Backend\Modules\Media\Engine\Model as BackendMediaModel;
use Backend\Core\Engine\Model as BackendModel;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * Delete mediaitem
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
        //--Create filesystem for file actions
        $fs = new Filesystem();
        //--Get all image folders defined by sizes
        $folders = BackendModel::getThumbnailFolders(FRONTEND_FILES_PATH . '/Media/Images', true);

        $path = FRONTEND_FILES_PATH . '/Media/';
        //--Check if the id is not empty
        if(!empty($id))
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

                    if($fs->exists($path .'Images/Source/' . $media['filename']))
                        $fs->remove($path .'Images/Source/' . $media['filename']);

                    foreach($folders as $folder)
                    {
                        if($fs->exists($path .'Images/' . $folder['dirname'] . '/' . $media['filename']))
                            $fs->remove($path .'Images/' . $folder['dirname'] . '/' . $media['filename']);
                    }
                }else
                {
                    if($fs->exists($path .'Files/' . $media['filename']))
                        $fs->remove($path .'Files/' . $media['filename']);
                }
                //--Delete mediaitem
                BackendMediaModel::delete($mediaModule['media_id']);
            }
        }

        // success output
        $this->output(self::OK, null, 'file deleted');
    }
}
