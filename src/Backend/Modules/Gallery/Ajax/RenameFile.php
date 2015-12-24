<?php

namespace Backend\Modules\Gallery\Ajax;

use Backend\Core\Engine\Base\AjaxAction;
use Backend\Modules\Gallery\Engine\Model as BackendGalleryModel;
use Backend\Core\Engine\Model as BackendModel;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * Rename image
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

        //--Get the ids and split them
        $id = \SpoonFilter::getPostValue('id', null, '', 'string');
        //--Get new name for image
        $nameGet = \SpoonFilter::getPostValue('name', null, '', 'string');

        //--Check if the id is not empty
        if(!empty($id))
        {
            //--Get image
            $image = BackendGalleryModel::get($id);
            //--Clean new name for file
            $name = preg_replace("([^\w\s\d\-_~,;:\[\]\(\).])", '', $nameGet);
            //--Get all image folders defined by sizes
            $folders = BackendModel::getThumbnailFolders(FRONTEND_FILES_PATH . '/Gallery/Images', true);
            //--Create filesystem for file actions
            $fs = new Filesystem();
            //--Get extention
            $extension = pathinfo($image['filename'], PATHINFO_EXTENSION);
            //--Get path to files
            $path = FRONTEND_FILES_PATH . '/Gallery/Images/';

            //--If old and new name is not the same -> do rename
            if($image['filename'] != $name . '.' . $extension)
            {
                //--Rename files on disk
                if(!$fs->exists($path . '/Source/' . $name . '.' . $extension)){
                    if ($fs->exists($path . '/Source/' . $image['filename']))
                    {
                        $fs->rename($path . '/Source/' . $image['filename'], $path . '/Source/' . $name . '.' . $extension);
                    }

                    foreach ($folders as $folder)
                    {
                        if ($fs->exists($path . $folder['dirname'] . '/' . $image['filename']))
                        {
                            $fs->rename($path . $folder['dirname'] . '/' . $image['filename'], $path . $folder['dirname'] . '/' . $name . '.' . $extension);
                        }
                    }

                    //--Rename file
                    $image['filename'] = $name . '.' . $extension;

                    BackendGalleryModel::update($image);
                    $this->output(self::OK, null, 'file renamed');
                }else{
                    $this->output(self::ERROR, null, 'file name already exists');
                }
            }else{
                $this->output(self::OK, null, 'file name is the same');
            }
        }
    }
}
