<?php

namespace Backend\Modules\Media\Ajax;

use Backend\Core\Engine\Base\AjaxAction;
use Backend\Modules\Media\Engine\Model as BackendMediaModel;
use Backend\Core\Engine\Model as BackendModel;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * Rename file
 *
 * @author Nick Vandevenne <nick@comsa.be>
 */
class RenameFile extends AjaxAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        //--Get the id of the link to mediaitem
        $id = \SpoonFilter::getPostValue('id', null, '', 'string');
        //--Get new name for file
        $nameGet = \SpoonFilter::getPostValue('name', null, '', 'string');

        //--Check if the id is not empty
        if(!empty($id))
        {
            //--Get link to mediaitem
            $mediaModule = BackendMediaModel::getMediaModule($id);
            //--Get mediaitem
            $media = BackendMediaModel::get($mediaModule['media_id']);
            //--Clean new name for file
            $name = preg_replace("([^\w\s\d\-_~,;:\[\]\(\).])", '', $nameGet);
            //--Get all image folders defined by sizes
            $folders = BackendModel::getThumbnailFolders(FRONTEND_FILES_PATH . '/Media/Images', true);
            //--Create filesystem for file actions
            $fs = new Filesystem();
            //--Get path to files
            $path = FRONTEND_FILES_PATH . '/Media/';

            //--If old and new name is not the same -> do rename
            if($media['filename'] != $name . '.' . $media['extension'])
            {
                //--Rename files on disk
                if ($media['filetype'] == 1)
                {

                    if ($fs->exists($path . 'Images/Source/' . $media['filename']))
                    {
                        $fs->rename($path . 'Images/Source/' . $media['filename'], FRONTEND_FILES_PATH . '/Media/Images/Source/' . $name . '.' . $media['extension']);
                    }

                    foreach ($folders as $folder)
                    {
                        if ($fs->exists($path . 'Images/' . $folder['dirname'] . '/' . $media['filename']))
                        {
                            $fs->rename($path . 'Images/' . $folder['dirname'] . '/' . $media['filename'], FRONTEND_FILES_PATH . '/Media/Images/' . $folder['dirname'] . '/' . $name . '.' . $media['extension']);
                        }
                    }
                }
                else
                {
                    if ($fs->exists($path . 'Files/' . $media['filename']))
                    {
                        $fs->rename($path . 'Files/' . $media['filename'], FRONTEND_FILES_PATH . '/Media/Files/' . $name . '.' . $media['extension']);
                    }
                }
                //--Set new name on mediaitem
                $media['filename'] = $name . '.' . $media['extension'];
                //--Update mediaitem
                BackendMediaModel::update($mediaModule['media_id'], $media);
                //--Create url to new file for ajax
                $url = FRONTEND_FILES_URL . '/Media/Files/' . $media['filename'];
                //--Return the new URL -> replaces the old url of the media on page
                $this->output(self::OK, $url, 'file renamed');
            }else{
                $this->output(self::OK, null, 'file name is the same');
            }
        }
        // success output
    }
}
