<?php

namespace Backend\Modules\Media\Engine;

use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BackendLanguage;

/**
 * In this file we store all generic functions that we will be using in the media module
 *
 * @author Waldo Cosman <waldo@comsa.be>
 * @author Nick Vandevenne <nick@comsa.be>
 */
class Helper
{
    /**
     * The image upload field
     *
     * @var field
     */
    private $field;

    /**
     * FieldTypeFile
     *
     * @var int
     */
    private $fieldTypeFile = 2;

    /**
     * FieldTypeImage
     *
     * @var int
     */
    private $fieldTypeImage = 1;

    /**
     * The form instance
     *
     * @var Backendform
     */
    public $frm;

    /**
     * The id of the module-item
     *
     * @var int
     */
    private $id;

    /**
     * The media-items
     *
     * @var array
     */
    private $mediaItems;

    /**
     * The linked module
     *
     * @var string
     */
    private $module;

    /**
     * The linked action
     *
     * @var string
     */

    private $action;

    /**
     * The type
     *
     * @var
     */
    private $type;

    /**
     * @var mediaitem
     */
    public $item;

    /**
     * Contstructor
     *
     * @param BackendForm $form
     * @param $module
     * @param $id
     */
    public function __construct(BackendForm $form, $module, $id, $action = "", $type = "")
    {
        //--Set form instance
        $this->frm = $form;

        //--Set module
        $this->module = $module;

        //--Set module
        $this->action = $action;

        //--Set the type
        $this->type = $type;

        //--Set the id
        $this->id = (int)$id;

        //--Load the data
        $this->loadData();

        //--load the form
        $this->loadForm();
    }

    /**
     * Validate function
     */
    public function validate()
    {
        //--Validate form
        $this->validateForm();
    }

    /***
     * Get the media-record
     *
     * @return mixed
     */
    private function get($id)
    {
        return BackendModel::getContainer()->get('database')->getRecord("SELECT * FROM media WHERE id = ?", array($id));
    }

    /**
     * Get all the mediaitems linked to the module
     *
     */
    private function getFromModule()
    {
        $records = BackendModel::getContainer()->get('database')->getRecords(
            "SELECT mm.id, mm.media_id, filename, mm.text, m.filetype, m.extension FROM media AS m
															INNER JOIN media_modules AS mm ON mm.media_id = m.id
														WHERE mm.module = ? AND mm.other_id = ? AND mm.type = ?
														ORDER BY mm.sequence ASC", array($this->module, $this->id, $this->type)
        );
        $recordsImages = $recordsFiles = array();
        //--Loop records
        if (!empty($records))
        {
            //--Get the thumbnail-folders
            $folders = BackendModel::getThumbnailFolders(FRONTEND_FILES_PATH . '/Media/Images', true);

            //--Create the image-links to the thumbnail folders
            foreach ($records as &$row)
            {
                if ($row['filetype'] == 1)
                {
                    //--Get name without extention
                    $path_parts = pathinfo(FRONTEND_FILES_PATH . '/Media/Images/Source/' . $row['filename']);
                    $row['name'] = $path_parts['filename'];

                    foreach ($folders as $folder)
                    {
                        $row['image_' . $folder['dirname']] = $folder['url'] . '/' . $folder['dirname'] . '/' . $row['filename'];
                    }
                    $recordsImages[] = $row;
                }
                else
                {
                    //--Get name without extention
                    $path_parts = pathinfo(FRONTEND_FILES_PATH . '/Media/Files/' . $row['filename']);
                    $row['url'] = FRONTEND_FILES_URL . '/Media/Files/' . $row['filename'];
                    $row['name'] = $path_parts['filename'];
                    $recordsFiles[] = $row;
                }
            }
        }

        $this->mediaItems['images'] = $recordsImages;
        $this->mediaItems['files'] = $recordsFiles;
    }

    /**
     * Load the data
     */
    private function loadData()
    {
        $this->getFromModule();
    }

    /**
     * Load the form
     */
    private function loadForm()
    {
        //--Add image field
        $this->field = $this->frm->addFile('images');

        //--Check if mediaItems is empty
        if (!empty($this->mediaItems['images']))
        {
            //--Loop the images and create checkbox
            foreach ($this->mediaItems['images'] as &$row)
            {
                //$row['chkDelete'] = $this->frm->addCheckbox("image-" . $row["id"])->parse();
                $row['txtText'] = $this->frm->addTextarea("text-" . $row["id"], $row['text'])->setAttribute('style', 'resize: none;')->parse();
            }
        }

        //--Check if mediaItems is empty
        if (!empty($this->mediaItems['files']))
        {
            //--Loop the images and create checkbox
            foreach ($this->mediaItems['files'] as &$row)
            {
                //$row['chkDelete'] = $this->frm->addCheckbox("file-" . $row["id"])->parse();
                $row['txtText'] = $this->frm->addTextarea("text-" . $row["id"], $row['text'])->setAttribute('style', 'resize: none;')->parse();
            }
        }
    }

    /**
     * Validate the form
     */
    private function validateForm()
    {
        //--is the form submitted?
        if ($this->frm->isSubmitted())
        {
            //--no errors?
            if ($this->frm->isCorrect())
            {
                //--Get the field
                $filImage = $this->frm->getField('images');

                //--Check if the field is filled in
                if ($filImage->isFilled())
                {
                    // image extension and mime type
                    //$filImage->isAllowedExtension(array('jpg', 'png', 'gif', 'jpeg'), BackendLanguage::err('JPGGIFAndPNGOnly'));
                    //$filImage->isAllowedMimeType(array('image/jpg', 'image/png', 'image/gif', 'image/jpeg'), BackendLanguage::err('JPGGIFAndPNGOnly'));

                    //--Add media to database
                    if (is_int($this->addFile()))
                    {
                        //--If media is added, redirect to the tabMedia
                        //SpoonHTTP::redirect(BackendModel::createURLForAction($this->action, $this->module) . "&id=" . $this->id . "&report=media-added#tabMedia");
                    }
                }

                //--Check if the image-array is not empty.
                if (!empty($this->mediaItems['images']))
                {

                    //--Get folders
                    $folders = BackendModel::getThumbnailFolders(FRONTEND_FILES_PATH . '/Media/Images', true);

                    //--Loop the images
                    foreach ($this->mediaItems['images'] as $row)
                    {
                        //--Check if the delete parameter is filled in.
                        if (\SpoonFilter::getPostValue("image-" . $row["id"], null, "") == "Y")
                        {
                            //--Delete images from the database
                            $db = BackendModel::getContainer()->get('database');
                            $db->delete("media_modules", "id=?", array($row["id"]));
                            /*
                            $image = $this->get((int)$row["id"]);

                            if(!empty($image))
                            {
                                foreach($folders as $folder)
                                {
                                    //--delete the image
                                    \SpoonFile::delete($folder['path'] . '/' . $row['filename']);
                                }

                                //--Delete images from the database
                                $db = BackendModel::getContainer()->get('database');
                                $db->delete("media_modules", "media_id=?", array($row["id"]));
                                $db->delete("media", "id=?", array($row["id"]));
                            }*/
                        }
                        //--Update text
                        if (\SpoonFilter::getPostValue("text-" . $row["id"], null, ""))
                        {
                            $value = array();
                            $value["text"] = \SpoonFilter::getPostValue("text-" . $row["id"], null, "");

                            $db = BackendModel::getContainer()->get('database');
                            $db->update("media_modules", $value, 'id=' . $row['id']);
                        }
                    }
                }
                if (!empty($this->mediaItems['files']))
                {

                    //--Get folders
                    $folder = FRONTEND_FILES_PATH . '/Media/Files';

                    //--Loop the images
                    foreach ($this->mediaItems['files'] as $row)
                    {
                        //--Check if the delete parameter is filled in.
                        if (\SpoonFilter::getPostValue("file-" . $row["id"], null, "") == "Y")
                        {
                            //--Delete images from the database
                            $db = BackendModel::getContainer()->get('database');
                            $db->delete("media_modules", "id=?", array($row["id"]));
                            /*
                            $image = $this->get((int)$row["id"]);

                            if(!empty($image))
                            {
                                foreach($folders as $folder)
                                {
                                    //--delete the image
                                    \SpoonFile::delete($folder['path'] . '/' . $row['filename']);
                                }

                                //--Delete images from the database
                                $db = BackendModel::getContainer()->get('database');
                                $db->delete("media_modules", "media_id=?", array($row["id"]));
                                $db->delete("media", "id=?", array($row["id"]));
                            }*/
                        }
                        //--Update text
                        if (\SpoonFilter::getPostValue("text-" . $row["id"], null, ""))
                        {
                            $value = array();
                            $value["text"] = \SpoonFilter::getPostValue("text-" . $row["id"], null, "");

                            $db = BackendModel::getContainer()->get('database');
                            $db->update("media_modules", $value, 'id=' . $row['id']);
                        }
                    }
                }
            }
        }
    }

    //--Getter for mediaItems
    public function getMediaItems()
    {
        if (empty($this->mediaItems))
        {
            return array();
        }

        return $this->mediaItems;
    }

    /**
     * Add file
     *
     * @return int
     */
    private function addFile()
    {
        //--Upload file
        $id = $this->uploadFile();

        $this->item['media_id'] = $id;
        $this->item["text"] = "";

        //--Link the
        $this->item['id'] = $this->linkMediaToModule($id);

        return $id;
    }

    /***
     * Upload a file
     *
     */
    private function uploadFile()
    {
        //--Check if the file is an image or file
        if ($this->isImage())
        {
            // the image path
            $path = FRONTEND_FILES_PATH . '/Media/Images';
            if (!\SpoonDirectory::exists($path . '/Source'))
            {
                \SpoonDirectory::create($path . '/Source');
            }
        }
        else
        {
            // the file path
            $path = FRONTEND_FILES_PATH . '/Media/Files';
        }
        // create folders if needed

        // build the filename
        $filename = $this->checkFilename();

        $item = array();
        $item["filename"] = $filename;
        $item["extension"] = $this->field->getExtension();
        $item["created_on"] = BackendModel::getUTCDate('Y-m-d H:i:s');
        $item["filesize"] = $this->field->getFileSize("b");

        $data = array();

        //--Check if file is an image to specify data
        if ($this->isImage())
        {
            $item["filetype"] = $this->fieldTypeImage;
            //--Put file on disk
            $this->field->moveFile($path . "/Source/" . $filename);

            // create folders if needed
            if (!\SpoonDirectory::exists($path . '/128x128'))
            {
                \SpoonDirectory::create($path . '/128x128');
            }

            //--Create all tumbs/resizes of file
            $thumbnail = new \SpoonThumbnail($path . "/Source/" . $filename);
            $thumbnail->setAllowEnlargement(true);
            \Common\Core\Model::generateThumbnails($path, $path . '/Source/' . $filename);
        }
        else
        {
            $item["filetype"] = $this->fieldTypeFile;

            // move the source file
            $this->field->moveFile($path . "/" . $filename);
        }

        //--Serialize data
        $item["data"] = serialize($data);
        //--Store item so we can access it
        $this->item = $item;
        //--Insert into media
        return BackendModel::getContainer()->get('database')->insert("media", $item);
    }

    /***
     * Check if the field is an image
     *
     * @return boolean
     */
    private function isImage()
    {
        //--Array with image-extensions
        $arrImages = array("jpg", "jpeg", "gif", "png");

        //--Check if the file is an image or file
        if (in_array($this->field->getExtension(), $arrImages))
        {
            return true;
        }

        return false;
    }

    /**
     * Build the filename
     *
     * @param $filename
     * @param $extension
     * @param $try
     *
     * @return string
     */
    private function checkFilename($filename = "", $extension = "", $try = 0)
    {
        //--Check if filename is empty
        if ($filename == "")
        {
            $filename = substr($this->field->getFilename(), 0, 0 - (strlen($this->field->getExtension()) + 1));
        }

        //--Check if extension is empty
        if ($extension == "")
        {
            $extension = $this->field->getExtension();
        }

        if ($try > 0)
        {
            $filename_full = $filename . $try . "." . $extension;
        }
        else
        {
            //--Get filename
            $filename_full = $filename . "." . $extension;
        }

        $record = BackendModel::getContainer()->get('database')->getRecord("SELECT filename FROM media WHERE filename = ?", array($filename_full));
        if (is_null($record))
        {
            return $filename_full;
        }
        else
        {
            //--Get new filename
            return $this->checkFilename($filename, $extension, $try + 1);
        }
    }

    /***
     * Link media to module
     *
     * @param $id
     *
     * @return int or boolean
     */
    public function linkMediaToModule($media_id)
    {

        $exists = (bool)BackendModel::get('database')->getVar(
            'SELECT 1
			 FROM media_modules AS i
			 WHERE i.module = ? AND other_id = ? AND type = ? AND i.media_id = ?
			 LIMIT 1', array((int)$this->module, $this->id, $this->type, $media_id)
        );

        if (!$exists)
        {
            if ($this->module != "" && $this->id > 0)
            {
                //--Calculate sequence
                $sequence = (int)BackendModel::getContainer()->get('database')->getVar(
                    'SELECT MAX(i.sequence)
			 FROM media_modules AS i
			 WHERE i.module = ? AND other_id = ? AND type = ?', array((int)$this->module, $this->id, $this->type)
                );
                $sequence += 1;

                $insert = array();
                $insert["media_id"] = $media_id;
                $insert["module"] = $this->module;
                $insert["other_id"] = $this->id;
                $insert["type"] = $this->type;
                $insert["identifier"] = 0;
                $insert["sequence"] = $sequence;
                $insert["language"] = BackendLanguage::getWorkingLanguage();
                $insert["title"] = "";
                $insert["linktype"] = 0;

                //--Add record to db
                return BackendModel::getContainer()->get('database')->insert("media_modules", $insert);
            }
        }

        return false;
    }

    /***
     * Get list of all mediaitems
     *
     * @return mixed
     */
    public static function getAllMediaItems()
    {
        $records = BackendModel::getContainer()->get('database')->getRecords(
            "SELECT m.id, filename, m.filetype, m.extension FROM media AS m"
        );
        $recordsImages = $recordsFiles = array();
        //--Loop records
        if (!empty($records))
        {
            //--Get the thumbnail-folders
            $folders = BackendModel::getThumbnailFolders(FRONTEND_FILES_PATH . '/Media/Images', true);

            //--Create the image-links to the thumbnail folders
            foreach ($records as &$row)
            {
                if ($row['filetype'] == 1)
                {
                    $path_parts = pathinfo(FRONTEND_FILES_PATH . '/Media/Images/Source/' . $row['filename']);
                    $row['name'] = $path_parts['filename'];

                    foreach ($folders as $folder)
                    {
                        $row['image_' . $folder['dirname']] = $folder['url'] . '/' . $folder['dirname'] . '/' . $row['filename'];
                    }
                    $recordsImages[] = $row;
                }
                else
                {
                    $path_parts = pathinfo(FRONTEND_FILES_PATH . '/Media/Files/' . $row['filename']);
                    $row['url'] = FRONTEND_FILES_URL . '/Media/Files/' . $row['filename'];
                    $row['name'] = $path_parts['filename'];
                    $recordsFiles[] = $row;
                }
            }
        }
        $all = array();
        $all['images'] = $recordsImages;
        $all['files'] = $recordsFiles;
        return $all;
    }
}