<?php

namespace Backend\Modules\Addresses\Engine;

use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the addresses module
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Model
{
    const QRY_DATAGRID_BROWSE_ADDRESSES = 'SELECT i.id, i.name, i.firstname
		 FROM addresses AS i';

    const QRY_DATAGRID_BROWSE_GROUPS = 'SELECT i.id, i.title/*, COUNT(m.address_id) AS addresses*/, a.title as parent_title, i.sequence
										FROM addresses_groups AS i
										LEFT JOIN addresses_groups as a ON i.parent_id = a.id
										LEFT JOIN addresses_in_group AS m ON m.group_id = i.id
										GROUP BY i.id';

    const QRY_DATAGRID_BROWSE_GROUPS_WITH_GROUPID = 'SELECT i.id, i.title, COUNT(m.address_id) AS addresses, /*a.title as parent_title,*/ i.sequence
										FROM addresses_groups AS i
										LEFT JOIN addresses_in_group AS m ON m.group_id = i.id
										WHERE i.parent_id = ?
										GROUP BY i.id';


    /**
     * Delete a certain item
     *
     * @param int $id
     */
    public static function delete($id)
    {
        $address = self::get($id);

        BackendModel::getContainer()->get('database')->delete('addresses', 'id = ?', (int)$id);
        BackendModel::getContainer()->get('database')->delete('addresses_lang', 'id = ?', (int)$id);

        BackendModel::getContainer()->get('database')->delete('meta', 'id = ?', (int)$address['meta_id']);
    }

    /**
     * Checks if a certain item exists
     *
     * @param int $id
     *
     * @return bool
     */
    public static function exists($id)
    {
        return (bool)BackendModel::getContainer()->get('database')->getVar('SELECT 1
			 FROM addresses AS i
			 WHERE i.id = ?
			 LIMIT 1', array((int)$id));
    }

    /**
     * Fetches a certain item
     *
     * @param int $id
     *
     * @return array
     */
    public static function get($id)
    {
        return (array)BackendModel::getContainer()->get('database')->getRecord('SELECT i.*,  m.url
			 FROM addresses AS i
			 INNER JOIN meta AS m on m.id = i.meta_id
			 WHERE i.id = ?', array((int)$id));
    }

    /**
     * Fetches a certain item
     *
     * @param int $id
     * @return array
     */
    public static function getLanguage($id, $lang)
    {
        return (array)BackendModel::get('database')->getRecord(
            'SELECT l.*
                         FROM addresses AS c
                         INNER JOIN addresses_lang AS l ON l.id = c.id AND l.language = ?
                         WHERE c.id = ?', array($lang, (int)$id)
        );
    }

    /**
     * Updates an item
     *
     * @param array $item
     */
    public static function updateLanguage(array $item)
    {

        BackendModel::get('database')->update(
            'addresses_lang', $item, 'id = ? AND language = ?', array((int)$item['id'], (string)$item['language'])
        );
    }

    /**
     * Retrieve the unique url for an item
     *
     * @param string $url
     * @param int [optional] $id
     *
     * @return string
     */
    public static function getUrl($url, $id = null)
    {
        // redefine Url
        $url = \SpoonFilter::urlise((string)$url);

        // get db
        $db = BackendModel::getContainer()->get('database');

        // new item
        if ($id === null) {
            $numberOfItems = (int)$db->getVar('SELECT 1
				 FROM addresses AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ?
				 LIMIT 1', array(BL::getWorkingLanguage(), $url));

            // already exists
            if ($numberOfItems != 0) {
                // add number
                $url = BackendModel::addNumber($url);

                // try again
                return self::getUrl($url);
            }
        } // current item should be excluded
        else {
            $numberOfItems = (int)$db->getVar('SELECT 1
				 FROM addresses AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ? AND i.id != ?
				 LIMIT 1', array(BL::getWorkingLanguage(), $url, $id));

            // already exists
            if ($numberOfItems != 0) {
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
     * Retrieve the unique url for an item
     *
     * @param string $url
     * @param int [optional] $id
     *
     * @return string
     */
    public static function getUrlForGroup($url, $id = null)
    {
        // redefine Url
        $url = \SpoonFilter::urlise((string)$url);

        // get db
        $db = BackendModel::getContainer()->get('database');

        // new item
        if ($id === null) {
            $numberOfItems = (int)$db->getVar('SELECT 1
				 FROM addresses_groups AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ?
				 LIMIT 1', array(BL::getWorkingLanguage(), $url));

            // already exists
            if ($numberOfItems != 0) {
                // add number
                $url = BackendModel::addNumber($url);

                // try again
                return self::getUrl($url);
            }
        } // current item should be excluded
        else {
            $numberOfItems = (int)$db->getVar('SELECT 1
				 FROM addresses_groups AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ? AND i.id != ?
				 LIMIT 1', array(BL::getWorkingLanguage(), $url, $id));

            // already exists
            if ($numberOfItems != 0) {
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
     *
     * @return int
     */
    public static function insert(array $data)
    {
        $data['created_on'] = BackendModel::getUTCDate();

        return (int)BackendModel::getContainer()->get('database')->insert('addresses', $data);
    }

    /**
     * Insert an item in the database
     *
     * @param array $data
     *
     * @return int
     */
    public static function insertLanguage(array $data)
    {
        return (int)BackendModel::getContainer()->get('database')->insert('addresses_lang', $data);
    }

    /**
     * Insert meta for an item in the database
     *
     * @param array $data
     *
     * @return int
     */
    public static function insertMeta(array $data)
    {

        //--Replace special characters
        $data["url"] = strtr($data["url"], "ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ", "SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy");
        $data["url"] = str_replace(".", "", \SpoonFilter::urlise($data["url"]));

        //--Replace the values with utf8
        foreach ($data as &$value) {
            $value = utf8_encode($value);
        }

        $data['url'] = BackendAddressesModel::checkUrl($data['url']);

        return (int)BackendModel::getContainer()->get('database')->insert('meta', $data);
    }

    public static function checkUrl($url)
    {
        $numberOfItems = (int)BackendModel::getContainer()->get('database')->getVar('SELECT 1
				 FROM  meta AS m
				 WHERE m.url = ?
				 LIMIT 1', array($url));
        // already exists
        if ($numberOfItems != 0) {
            // add number
            $url = BackendModel::addNumber($url);

            // try again
            return self::checkUrl($url);
        }

        return $url;
    }

    /**
     * Updates an item
     *
     * @param int $id
     * @param array $data
     */
    public static function update($id, array $data)
    {
        $data['edited_on'] = BackendModel::getUTCDate();

        BackendModel::getContainer()->get('database')->update('addresses', $data, 'id = ?', (int)$id);
    }

    /**
     * Delete a certain item
     *
     * @param int $id
     */
    public static function deleteGroup($id)
    {
        $db = BackendModel::getContainer()->get('database');

        $group = self::getGroup($id);

        // build extra
        $extra = array('id' => $group['extra_id'], 'module' => 'Addresses', 'type' => 'widget', 'action' => 'ShowAddresses');

        // delete extra
        $db->delete('modules_extras', 'id = ? AND module = ? AND type = ? AND action = ?', array($extra['id'], $extra['module'], $extra['type'], $extra['action']));

        BackendModel::getContainer()->get('database')->delete('addresses_groups', 'id = ?', (int)$id);

        BackendModel::getContainer()->get('database')->delete('meta', 'id = ?', (int)$group['meta_id']);
    }

    /**
     * Checks if a certain item exists
     *
     * @param int $id
     *
     * @return bool
     */
    public static function existsGroup($id)
    {
        return (bool)BackendModel::getContainer()->get('database')->getVar('SELECT 1
			 FROM addresses_groups AS i
			 WHERE i.id = ?
			 LIMIT 1', array((int)$id));
    }

    /**
     * Fetches a certain item
     *
     * @param int $id
     *
     * @return array
     */
    public static function getGroup($id)
    {
        return (array)BackendModel::getContainer()->get('database')->getRecord('SELECT i.*, m.url
			 FROM addresses_groups AS i
			 INNER JOIN meta AS m on m.id = i.meta_id
			 WHERE i.id = ?', array((int)$id));
    }

    /**
     * Insert an item in the database
     *
     * @param array $data
     *
     * @return int
     */
    public static function insertGroup(array $data)
    {
        $db = BackendModel::getContainer()->get('database');

        $data['created_on'] = BackendModel::getUTCDate();

        $data['id'] = (int)$db->insert('addresses_groups', $data);

        // build extra for the gallery-widget
        $extra = array('module' => 'Addresses', 'type' => 'widget', 'label' => 'Addresses', 'action' => 'ShowAddresses', 'data' => serialize(array('id' => $data['id'], 'extra_label' => "Addresses " . $data['title']/*, 'language' => $data['language']*/, 'edit_url' => BackendModel::createURLForAction('edit_group') . '&id=' . $data['id'])), 'hidden' => 'N', 'sequence' => $db->getVar(
            'SELECT MAX(i.sequence) + 1
				 FROM modules_extras AS i
				 WHERE i.module = ?', array('links')
        ));

        if (is_null($extra['sequence'])) {
            $extra['sequence'] = $db->getVar(
                'SELECT CEILING(MAX(i.sequence) / 1000) * 1000
			 FROM modules_extras AS i'
            );
        }
        // insert extra gallery-widget
        $data['extra_id'] = $db->insert('modules_extras', $extra);

        $update = $db->update('addresses_groups', $data, 'id = ?', array($data['id']));
        return $data['id'];
    }

    /**
     * Updates an item
     *
     * @param int $id
     * @param array $data
     */
    public static function updateGroup($id, array $data)
    {
        $db = BackendModel::getContainer()->get('database');

        if (isset($data['extra_id']) && $data['extra_id'] != 0) {
            // build extra
            $extra = array('id' => $data['extra_id'], 'module' => 'Addresses', 'type' => 'widget', 'label' => 'Addresses', 'action' => 'ShowAddresses', 'data' => serialize(array('id' => $data['id'], 'extra_label' => "Addresses " . $data['title']/*, 'language' => $data['language']*/, 'edit_url' => BackendModel::createURLForAction('edit_group') . '&id=' . $data['id'])), 'hidden' => 'N');

            // update extra
            $db->update('modules_extras', $extra, 'id = ? ', array($data['extra_id']));
        } elseif (!isset($data['extra_id']) || $data['extra_id'] == 0) {
            // build extra for the gallery-widget
            $extra = array('module' => 'Addresses', 'type' => 'widget', 'label' => 'Addresses', 'action' => 'ShowAddresses', 'data' => serialize(array('id' => $id, 'extra_label' => "Addresses " . $data['title']/*, 'language' => $data['language']*/, 'edit_url' => BackendModel::createURLForAction('edit_group') . '&id=' . $id)), 'hidden' => 'N', 'sequence' => $db->getVar(
                'SELECT MAX(i.sequence) + 1
				 FROM modules_extras AS i
				 WHERE i.module = ?', array('links')
            ));

            if (is_null($extra['sequence'])) {
                $extra['sequence'] = $db->getVar(
                    'SELECT CEILING(MAX(i.sequence) / 1000) * 1000
			 FROM modules_extras AS i'
                );
            }

            // insert extra gallery-widget
            $data['extra_id'] = $db->insert('modules_extras', $extra);
        }

        BackendModel::getContainer()->get('database')->update('addresses_groups', $data, 'id = ?', (int)$id);
    }

    /**
     *
     * Get all the users
     *
     * @param $group_id
     *
     * @return mixed
     */
    public static function getAllAddresses($group_id = 0)
    {
        if ($group_id > 0) {
            return BackendModel::getContainer()->get('database')->getRecords("SELECT i.id, i.firstname, i.name, i.company
													FROM addresses AS i
													LEFT JOIN addresses_in_group AS ag ON ag.address_id = i.id AND ag.group_id = ?
													WHERE i.hidden = ?
													ORDER BY COALESCE( ag.sequence ,99999999) ASC, i.name, i.firstname", array($group_id, 'N'));
        } else {
            return BackendModel::getContainer()->get('database')->getRecords("SELECT i.id, i.firstname, i.name, i.company
													FROM addresses AS i
													WHERE i.hidden = ?", array('N'));
        }
    }

    /**
     *
     * Add user to group
     *
     * @param $data
     *
     * @return int
     */
    public static function insertAddressToGroup($data)
    {

        $bool = (bool)BackendModel::getContainer()->get('database')->getVar('SELECT 1
													 FROM addresses_in_group AS i
													 WHERE i.group_id = ? AND i.address_id = ?
													 LIMIT 1', array((int)$data['group_id'], $data['address_id']));

        //--If record already exists, return false;
        if ($bool === true) {
            return true;
        }

        //--Get the last sequence
        $data['sequence'] = self::getMaximumAddressSequence($data["group_id"]) + 1;

        //--Add to Db
        return (int)BackendModel::getContainer()->get('database')->insert('addresses_in_group', $data);
    }

    /**
     *
     * Get all the users for a group
     *
     * @param $id
     *
     * @return array()
     */
    public static function getAddressesForGroup($id)
    {

        return BackendModel::getContainer()->get('database')->getPairs("SELECT i.address_id, CONCAT(a.name, ' ', a.firstname) AS name
													FROM addresses_in_group AS i
													INNER JOIN addresses AS a ON a.id = i.address_id
													WHERE i.group_id = ? AND a.hidden=?", array($id, 'N'));
    }

    /**
     *
     * Get all the groups for an address
     *
     * @param $id
     *
     * @return array()
     */
    public static function getGroupsForAddress($id)
    {

        $users = BackendModel::getContainer()->get('database')->getPairs("SELECT i.group_id, g.title
													FROM addresses_in_group AS i
													INNER JOIN addresses_groups AS g ON g.id = i.group_id
													WHERE i.address_id = ?", array($id));

        return $users;
    }

    /**
     * Get the maximum sequence for an album
     *
     * @param int $group_id
     *
     * @return int
     */
    public static function getMaximumAddressSequence($group_id)
    {
        return (int)BackendModel::getContainer()->get('database')->getVar('SELECT MAX(i.sequence)
			 FROM addresses_in_group AS i
			 WHERE group_id = ?', array($group_id));
    }

    /**
     *
     * Get all the groups
     *
     * @return mixed
     */
    public static function getAllGroups()
    {
        $groups = BackendModel::getContainer()->get('database')->getRecords("SELECT i.id, i.title
													FROM addresses_groups AS i");

        return $groups;
    }

    /**
     *
     * Get all the groups for a dropdown
     *
     * @param $count
     *
     * @return mixed
     */
    public static function getAllGroupsForDropdown($count = false)
    {
        if ($count == false) {
            $groups = BackendModel::getContainer()->get('database')->getPairs("SELECT i.id, i.title
														FROM addresses_groups AS i");
        } else {
            $groups = BackendModel::getContainer()->get('database')->getPairs("	SELECT i.id, CONCAT(i.title, ' (', count(u.address_id), ')') AS title
														FROM addresses_groups AS i
															INNER JOIN addresses_in_group AS g ON g.group_id = i.id
														GROUP BY i.id");
        }

        return $groups;
    }

    /**
     * Get all the categories
     *
     * @param bool [optional] $includeCount
     * @return array
     */
    public static function getAllGroupsTree()
    {
        $db = BackendModel::getContainer()->get('database');

        $allCategories = (array)$db->getRecords(
            'SELECT i.id, i.parent_id, CONCAT(i.title, " (", COUNT(g.id) ,")") AS title
				 FROM addresses_groups AS i
				 LEFT OUTER JOIN addresses_groups AS g ON g.parent_id = i.id
				 GROUP BY i.id
				 ORDER BY i.sequence'
        );

        $tree = array();

        $categoryTree = self::buildTree($tree, $allCategories);
        $categoryTree = array(ucfirst(BL::getLabel('None'))) + $categoryTree;

        return $categoryTree;
    }


    public static function getAllGroupsTreeArray()
    {
        $db = BackendModel::getContainer()->get('database');

        $allCategories = (array)$db->getRecords(
            'SELECT i.id, (CASE WHEN i.parent_id IS NULL THEN 0 ELSE i.parent_id END) as parent_id, i.title
				 FROM addresses_groups AS i
				 LEFT OUTER JOIN addresses_groups AS g ON g.parent_id = i.id
				 GROUP BY i.id
				 ORDER BY i.sequence'
        );

        $tree = array();

        $categoryTree = self::buildTreeArray($tree, $allCategories);

        return $categoryTree;
    }

    /**
     * Build the category tree
     *
     * @param $tree
     * @param array $categories
     * @param int $parentId
     * @param int $level
     * @return array
     */
    public static function buildTree(array &$tree, array $categories, $parentId = 0, $level = 0)
    {
        foreach ($categories as $category) {
            if ($category['parent_id'] == $parentId) {
                $tree[$category['id']] = str_repeat('-', $level) . $category['title'];

                $level++;
                $children = self::buildTree($tree, $categories, $category['id'], $level);
                $level--;
            }
        }
        return $tree;
    }

    /**
     * Build the category tree
     *
     * @param $tree
     * @param array $categories
     * @param int $parentId
     * @param int $level
     * @return array
     */
    public static function buildTreeArray(array &$tree, array $categories, $parentId = 0, $level = 0)
    {
        foreach ($categories as $category) {
            if ($category['parent_id'] == $parentId) {
                $tree['items'][$category['id']] = array('id' => $category['id'], 'title' => $category['title']);

                $level++;
                self::buildTreeArray($tree['items'][$category['id']], $categories, $category['id'], $level);
                $level--;
            }
        }
        return $tree;
    }


    /**
     * Updates an item
     *
     * @param int $address_id
     * @param int $group_id
     * @param array $data
     */
    public static function updateSequence($address_id, $group_id, array $data)
    {
        BackendModel::getContainer()->get('database')->update('addresses_in_group', $data, 'address_id = ? AND group_id = ?', array((int)$address_id, (int)$group_id));
    }

    /**
     *
     * Delete addresses from a group
     *
     * @param $id
     *
     * @return int
     */
    public static function deleteAddressesFromGroup($id)
    {
        BackendModel::getContainer()->get('database')->delete('addresses_in_group', 'group_id = ?', (int)$id);
    }

    /**
     *
     * Delete groups from a address
     *
     * @param $id
     *
     * @return int
     */
    public static function deleteGroupsFromAddress($id)
    {
        BackendModel::getContainer()->get('database')->delete('addresses_in_group', 'address_id = ?', (int)$id);
    }

    /**
     *
     * Get all the addresses without Lat/Lng
     *
     * @param int $limit
     */
    public static function getAddressesWithoutLatLng($limit = 9999999)
    {
        return BackendModel::getContainer()->get('database')->getRecords("SELECT i.id, i.company, i.firstname, i.name, i.address, i.zipcode, i.city, i.country
													FROM addresses AS i
													WHERE i.lat = '' OR i.lng = ''
													LIMIT 0, ?", array($limit));

    }
}