<?php

namespace Frontend\Modules\Media\Engine;

use Frontend\Core\Engine\Model as FrontendModel;

/*
*
* This class will handle (media)uploads on the Frontend
*
*/
class Helper
{
	/**
	 * Field
	 *
	 * @var field
	 */
	protected $field;

	/**
	 * fieldTypeImage
	 *
	 * @var int
	 */
	private $fieldTypeImage = 1;

	/**
	 * fieldTypeFile
	 *
	 * @var int
	 */
	private $fieldTypeFile = 2;

	/**
	 * Get all the mediaitems linked to the module
	 *
	 * @param int module
	 * @param int id
	 *
	 * @return array
	 */
	public static function getFromModule($module, $id, $identifier = 0, $limit = 0, $type = "")
	{
		//--Check the limit
		$limitQuery = $limit > 0 ? 'LIMIT ' . $limit : '';

		$records = FrontendModel::getContainer()->get('database')->getRecords("SELECT m.id, filename, mm.text FROM media AS m
																INNER JOIN media_modules AS mm ON mm.media_id = m.id
															WHERE mm.module = ? AND mm.other_id = ? AND mm.identifier = ? AND mm.type = ? AND m.filetype = 1
															ORDER BY mm.sequence ASC " . $limitQuery, array($module, $id, $identifier, $type));

		//--Loop records
		if(!empty($records))
		{
			//--Get the thumbnail-folders
			//$folders = FrontendModel::getThumbnailFolders(FRONTEND_FILES_PATH . '/Media/Images', false);
			$folders = array();

			//--Create the image-links to the thumbnail folders
			foreach($records as &$row)
			{
				foreach($folders as $folder)
				{
					$row['image_' . $folder['dirname']] = $folder['url'] . '/' . $folder['dirname'] . '/' . $row['filename'];
				}

				$row['text'] = nl2br($row['text']);
			}

			//--Check if limit is 1, so return a normal array instead of assosiative
			if($limit == 1)
			{
				return $records[0];
			}

			return $records;
		}

		return array();
	}

	/**
	 * Add file
	 *
	 * @param        $field
	 * @param string $module
	 * @param null $other_id
	 * @param string $identifier
	 *
	 * @return int
	 */
	public static function addFile($field, $module = "", $other_id = null, $identifier = "")
	{
		self::$field = $field;

		//--Upload file
		$id = self::uploadFile($field);

		//--Check if there is a module and id available
		if($module != "" && !is_null($other_id))
		{
			self::linkMediaToModule($id, $module, $other_id, $identifier);
		}

		return $id;
	}

	/***
	 * Upload a file
	 *
	 * @return int
	 */
	private static function uploadFile()
	{
		//--Check if the file is an image or file
		if(self::isImage())
		{
			// the image path
			$path = FRONTEND_FILES_PATH . '/media/images';
		}
		else
		{
			// the file path
			$path = FRONTEND_FILES_PATH . '/media/files';
		}

		// create folders if needed
		if(!SpoonDirectory::exists($path . '/source')) SpoonDirectory::create($path . '/source');
		if(!SpoonDirectory::exists($path . '/128x128')) SpoonDirectory::create($path . '/128x128');

		// build the filename
		$filename = self::checkFilename();

		$item = array();
		$item["filename"] = $filename;
		$item["extension"] = self::$field->getExtension();
		$item["created_on"] = FrontendModel::getUTCDate('Y-m-d H:i:s');
		$item["filesize"] = self::$field->getFileSize("b");

		$data = array();

		//--Check if file is an image to specify data
		if(self::isImage())
		{
			$item["filetype"] = self::$fieldTypeImage;
			$data["width"] = self::$field->getWidth();
			$data["height"] = self::$field->getHeight();

			// upload the image & generate thumbnails
			self::$field->generateThumbnails($path, $filename);
		}
		else
		{
			$item["filetype"] = self::$fieldTypeFile;

			// move the source file
			self::$field->moveFile($path . "/" . $filename);
		}

		//--Serialize data
		$item["data"] = serialize($data);

		// get db
		$db = FrontendModel::getDB(true);

		//--Insert into media
		return $db->insert("media", $item);
	}

	/***
	 * Check if the field is an image
	 *
	 * @return boolean
	 */
	protected static function isImage()
	{
		//--Array with image-extensions
		$arrImages = array("jpg", "jpeg", "gif", "png");

		//--Check if the file is an image or file
		if(in_array(self::$field->getExtension(), $arrImages))
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
	protected static function checkFilename($filename = "", $extension = "", $try = 0)
	{
		//--Check if filename is empty
		if($filename == "")
		{
			$filename = substr(self::$field->getFilename(), 0, 0 - (strlen(self::$field->getExtension()) + 1));
		}

		//--Check if extension is empty
		if($extension == "")
		{
			$extension = self::$field->getExtension();
		}

		if($try > 0)
		{
			$filename_full = $filename . $try . "." . $extension;
		}
		else
		{
			//--Get filename
			$filename_full = $filename . "." . $extension;
		}

		$db = FrontendModel::getContainer()->get('database');
		$record = $db->getRecord("SELECT filename FROM media WHERE filename = ?", array($filename_full));
		if(is_null($record))
		{
			return $filename_full;
		}
		else
		{
			//--Get new filename
			return self::checkFilename($filename, $extension, $try + 1);
		}
	}

	/***
	 * Link media to module
	 *
	 * @param $id
	 * @param $module
	 * @param $other_id
	 * @param string $identifier
	 *
	 * @return int
	 */
	protected static function linkMediaToModule($media_id, $module, $other_id, $identifier = "")
	{
		$db = FrontendModel::getDB(true);

		//--Calculate sequence
		$sequence = (int)$db->getVar('SELECT MAX(i.sequence)
			 FROM media_modules AS i
			 WHERE i.module = ? AND other_id = ? and identifier = ?', array((int)$module, $other_id, $identifier));
		$sequence += 1;

		$insert = array();
		$insert["media_id"] = $media_id;
		$insert["module"] = $module;
		$insert["other_id"] = $other_id;
		$insert["identifier"] = $identifier;
		$insert["sequence"] = $sequence;
		$insert["language"] = FRONTEND_LANGUAGE;
		$insert["text"] = "";
		$insert["linktype"] = 0;

		//--Add record to db
		return $db->insert("media_modules", $insert);
	}

	/***
	 * Get the media-record
	 *
	 * @param int $id
	 *
	 * @return mixed
	 */
	public static function get($id)
	{
		$db = FrontendModel::getContainer()->get('database');

		return $db->getRecord("SELECT * FROM media WHERE id = ?", array($id));
	}
}