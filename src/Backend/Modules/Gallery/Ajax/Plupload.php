<?php

namespace Backend\Modules\Gallery\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Engine\Language as BL;
use Backend\Modules\Gallery\Engine\Model as BackendGalleryModel;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Backend\Core\Engine\Template;

/**
 * This is an ajax handler
 *
 * @author Waldo Cosman <waldo@comsa.be>
 * @author Nick Vandevenne <nick@comsa.be>
 */
class Plupload extends BackendBaseAJAXAction
{
    /**
     * @var $id
     */
    private $id;

    /**
     * Execute the action
     */
    public function execute()
    {

        parent::execute();

        //--Set post var to check submit
        $_POST["form"] = "add_image";

        // get parameters
        $this->id = \SpoonFilter::getPostValue('id', null, '', 'int');

        //--Load form
        $this->loadForm();

        //--Validate form
        $this->validateForm();
    }

    private function loadForm()
    {
        //--Create form instance
        $this->frm = new BackendForm('add_image');

        //--Add file upload to the add_image form
        $this->frm->addImage('images');
    }

    /**
     * Validate the form add image
     *
     * @return void
     */
    private function validateForm()
    {
        //--Check if the add-image form is submitted
        if ($this->frm->isSubmitted())
        {

            //--Clean up fields in the form (NOT ALLOWED: fields from plupload like name are deleted)
            //$this->frm->cleanupFields();

            //--Get image field
            $filImage = $this->frm->getField('images');

            //--Check if the field is filled in
            if ($filImage->isFilled())
            {
                //--Image extension and mime type
                $filImage->isAllowedExtension(array('jpg', 'png', 'gif', 'jpeg'), BL::err('JPGGIFAndPNGOnly'));
                $filImage->isAllowedMimeType(array('image/jpg', 'image/png', 'image/gif', 'image/jpeg'), BL::err('JPGGIFAndPNGOnly'));

                //--Check if there are no errors.
                $strError = $filImage->getErrors();

                if ($strError === null)
                {

                    //--Get the filename
                    $strFilename = BackendGalleryModel::checkFilename(substr($_REQUEST["name"], 0, 0 - (strlen($filImage->getExtension()) + 1)), $filImage->getExtension());

                    //--Fill in the item
                    $item = array();
                    $item["album_id"] = (int)$this->id;
                    $item["user_id"] = BackendAuthentication::getUser()->getUserId();
                    $item["language"] = BL::getWorkingLanguage();
                    $item["filename"] = $strFilename;
                    $item["description"] = "";
                    $item["publish_on"] = BackendModel::getUTCDate();
                    $item["hidden"] = "N";
                    $item["sequence"] = BackendGalleryModel::getMaximumImageSequence($this->id) + 1;
                    //--the image path
                    $imagePath = FRONTEND_FILES_PATH . '/Gallery/Images';

                    //--create folders if needed
                    $resolutions = $this->get('fork.settings')->get("Gallery", 'resolutions', false);
                    if($resolutions != null)
                    {
                        foreach ($resolutions as $res)
                        {
                            if (!\SpoonDirectory::exists($imagePath . '/' . $res))
                            {
                                \SpoonDirectory::create($imagePath . '/' . $res);
                                // Create filesystem object
                                $filesystem = new Filesystem();

                                // Create var dir for ease of use
                                $dir = $imagePath;

                                // Check if dir exists
                                if ($filesystem->exists($dir . '/Source/'))
                                {
                                    // Create Finder object for the files
                                    $finderFiles = new Finder();

                                    // Get all the files in the source-dir
                                    $files = $finderFiles->files()->in($dir . '/Source/');

                                    // Check if $files is not empty
                                    if (!empty($files))
                                    {
                                        // Explode the dir-name
                                        $chunks = explode("x", $res, 2);

                                        // Create folder array
                                        $folder = array();
                                        $folder['width'] = ($chunks[0] != '') ? (int)$chunks[0] : null;
                                        $folder['height'] = ($chunks[1] != '') ? (int)$chunks[1] : null;

                                        // Loop all the files
                                        foreach ($files as $file)
                                        {

                                            set_time_limit(150);

                                            // Check if the file exists
                                            if (!$filesystem->exists($imagePath . '/' . $res . '/' . $file->getBasename()))
                                            {

                                                // generate the thumbnail
                                                $thumbnail = new \SpoonThumbnail($dir . '/Source/' . $file->getBasename(), $folder['width'], $folder['height']);
                                                $thumbnail->setAllowEnlargement(true);

                                                // if the width & height are specified we should ignore the aspect ratio
                                                if ($folder['width'] !== null && $folder['height'] !== null)
                                                {
                                                    $thumbnail->setForceOriginalAspectRatio(false);
                                                }
                                                $thumbnail->parseToFile($imagePath . '/' . $res . '/' . $file->getBasename());
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if (!\SpoonDirectory::exists($imagePath . '/Source'))
                    {
                        \SpoonDirectory::create($imagePath . '/Source');
                    }
                    if (!\SpoonDirectory::exists($imagePath . '/128x128'))
                    {
                        \SpoonDirectory::create($imagePath . '/128x128');
                    }
                    if (!\SpoonDirectory::exists($imagePath . '/800x'))
                    {
                        \SpoonDirectory::create($imagePath . '/800x');
                    }
                    if (!\SpoonDirectory::exists($imagePath . '/200x'))
                    {
                        \SpoonDirectory::create($imagePath . '/200x');
                    }
                    if (!\SpoonDirectory::exists($imagePath . '/400x300'))
                    {
                        \SpoonDirectory::create($imagePath . '/400x300');
                    }

                    //--image provided?
                    if ($filImage->isFilled())
                    {
                        //--upload the image & generate thumbnails
                        $filImage->generateThumbnails($imagePath, $item["filename"]);
                    }

                    //--Add item to the database
                    $idInsert = BackendGalleryModel::insert($item);

                    $item['id'] = $idInsert;

                    //--Create html for ajax
                    $tpl = new Template();

                    $txtDescription = $this->frm->addTextarea("description_" . $idInsert, $item['description']);

                    $item['field_description'] = $txtDescription->setAttribute('style', 'resize: none;')->parse();
                    //--Parse filename to get name
                    $path_parts = pathinfo(FRONTEND_FILES_PATH . '/Gallery/Images/Source/' . $item['filename']);
                    $item['name'] = $path_parts['filename'];
                    $folders = BackendModel::getThumbnailFolders(FRONTEND_FILES_PATH . '/Gallery/Images', true);

                    foreach ($folders as $folder)
                    {
                        $item['image_' . $folder['dirname']] = $folder['url'] . '/' . $folder['dirname'] . '/' . $item['filename'];
                    }

                    $tpl->assign('images', array($item));

                    $html = $tpl->getContent(BACKEND_MODULES_PATH . '/Gallery/Layout/Templates/Ajax/Image.tpl');
                    //Send html (ajax response)
                    $this->output(self::OK, $html, BL::msg('Success'));
                }
            }
        }
    }
}
