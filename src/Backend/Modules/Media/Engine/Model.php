<?php

namespace Backend\Modules\Media\Engine;

use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Language;

/**
 * In this file we store all generic functions that we will be using in the media module
 *
 * @author Waldo Cosman <waldo@comsa.be>
 * @author Nick Vandevenne <nick@comsa.be>
 */
class Model
{
    /**
     * Delete a certain item
     *
     * @param int $id
     */
	public static function delete($id)
	{
		BackendModel::get('database')->delete('media', 'id = ?', (int) $id);
	}

	/**
	 * Checks if a certain item exists
	 *
	 * @param int $id
	 * @return bool
	 */
	public static function exists($id)
	{
		return (bool) BackendModel::get('database')->getVar(
			'SELECT 1
			 FROM media AS i
			 WHERE i.id = ?
			 LIMIT 1',
			array((int) $id)
		);
	}

	/**
	 * Fetches a certain item
	 *
	 * @param int $id
	 * @return array
	 */
	public static function get($id)
	{
		return (array) BackendModel::get('database')->getRecord(
			'SELECT i.*
			 FROM media AS i
			 WHERE i.id = ?',
			array((int) $id)
		);
	}

	/**
	 * Retrieve the unique url for an item
	 *
	 * @param string $url
	 * @param int[optional] $id
	 * @return string
	 */
	public static function getUrl($url, $id = null)
	{
		// redefine Url
		$url = \SpoonFilter::urlise((string) $url);

		// get db
		$db = BackendModel::get('database');

		// new item
		if($id === null)
		{
			$numberOfItems = (int) $db->getVar(
				'SELECT 1
				 FROM media AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ?
				 LIMIT 1',
				array(Language::getWorkingLanguage(), $url));

			// already exists
			if($numberOfItems != 0)
			{
				// add number
				$url = BackendModel::addNumber($url);

				// try again
				return self::getUrl($url);
			}
		}
		// current item should be excluded
		else
		{
			$numberOfItems = (int) $db->getVar(
				'SELECT 1
				 FROM media AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ? AND i.id != ?
				 LIMIT 1',
				array(Language::getWorkingLanguage(), $url, $id));

			// already exists
			if($numberOfItems != 0)
			{
				// add number
				$url = BackendModel::addNumber($url);

				// try again
				return self::getUrl($url, $id);
			}
		}

		// return the unique Url!
		return $url;
	}

	/**
	 * Insert an item in the database
	 *
	 * @param array $data
	 * @return int
	 */
	public static function insert(array $data)
	{
		$data['created_on'] = BackendModel::getUTCDate();

		return (int) BackendModel::get('database')->insert('media', $data);
	}

	/**
	 * Updates an item
	 *
	 * @param int $id
	 * @param array $data
	 */
	public static function update($id, array $data)
	{
		//$data['edited_on'] = BackendModel::getUTCDate();

		BackendModel::get('database')->update(
			'media', $data, 'id = ?', (int) $id
		);
	}

    /**
     * Fetches a certain item
     *
     * @param int $id
     * @return array
     */
    public static function getMediaModule($id)
    {
        return (array) BackendModel::get('database')->getRecord(
            'SELECT i.*
			 FROM media_modules AS i
			 WHERE i.id = ?',
            array((int) $id)
        );
    }

	/**
	 * Checks if a certain item exists
	 *
	 * @param int $id
	 * @return bool
	 */
	public static function existsMediaModules($id)
	{
		return (bool) BackendModel::get('database')->getVar(
			'SELECT 1
			 FROM media_modules AS i
			 WHERE i.id = ?
			 LIMIT 1',
			array((int) $id)
		);
	}

	/**
	 * Updates an item
	 *
	 * @param int $id
	 * @param array $data
	 */
	public static function updateMediaModules($id, array $data)
	{

		BackendModel::get('database')->update(
			'media_modules', $data, 'id = ?', (int) $id
		);
	}

    /**
     * Delete a certain link to mediaitem
     *
     * @param int $id
     */
    public static function deleteLink($id){
        BackendModel::get('database')->delete("media_modules", "id=?", array($id));
    }
}
