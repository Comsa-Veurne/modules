<?php

namespace Frontend\Modules\Blocks\Engine;

use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation;

/**
 * In this file we store all generic functions that we will be using in the Blocks module
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Model
{
    /**
     * Fetches a certain item
     *
     * @param string $URL
     * @return array
     */
    public static function get($URL)
    {
        $item = (array) FrontendModel::get('database')->getRecord(
            'SELECT i.*,
             m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
             m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
             m.title AS meta_title, m.title_overwrite AS meta_title_overwrite, m.url
             FROM blocks AS i
             INNER JOIN meta AS m ON i.meta_id = m.id
             WHERE m.url = ?',
            array((string) $URL)
        );

        // no results?
        if (empty($item)) {
            return array();
        }

        // create full url
        $item['full_url'] = Navigation::getURLForBlock('Blocks', 'Detail') . '/' . $item['url'];

        return $item;
    }

    public static function getById($id)
    {
        $item = (array) FrontendModel::get('database')->getRecord(
            'SELECT i.*,
             m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
             m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
             m.title AS meta_title, m.title_overwrite AS meta_title_overwrite, m.url
             FROM blocks AS i
              INNER JOIN meta AS m ON i.meta_id = m.id
             WHERE i.id = ?',
            array((string) $id)
        );

        if($item['page_id'] > 0){
            $item['link'] = Navigation::getURL($item['page_id']);
        }

        // no results?
        if (empty($item)) {
            return array();
        }

        // create full url
        $item['full_url'] = Navigation::getURLForBlock('Blocks', 'Detail') . '/' . $item['url'];

        return $item;
    }

    /**
     * Get all items (at least a chunk)
     *
     * @param int[optional] $limit The number of items to get.
     * @param int[optional] $offset The offset.
     * @return array
     */
    public static function getAll($limit = 10, $offset = 0)
    {
        $items = (array) FrontendModel::get('database')->getRecords(
            'SELECT i.*, m.url
             FROM blocks AS i
             INNER JOIN meta AS m ON i.meta_id = m.id
             WHERE i.language = ?
             ORDER BY i.sequence ASC, i.id DESC LIMIT ?, ?',
            array(FRONTEND_LANGUAGE, (int) $offset, (int) $limit));

        // no results?
        if (empty($items)) {
            return array();
        }

        // get detail action url
        $detailUrl = Navigation::getURLForBlock('Blocks', 'Detail');

        // prepare items for search
        foreach ($items as &$item) {
            if($item['page_id'] > 0){
                $item['link'] = Navigation::getURL($item['page_id']);
            }
            $item['full_url'] =  $detailUrl . '/' . $item['url'];
        }

        // return
        return $items;
    }

    /**
     * Get the number of items
     *
     * @return int
     */
    public static function getAllCount()
    {
        return (int) FrontendModel::get('database')->getVar(
            'SELECT COUNT(i.id) AS count
             FROM blocks AS i
             WHERE i.language = ?',
            array(FRONTEND_LANGUAGE)
        );
    }

    /**
     * Get all category items (at least a chunk)
     *
     * @param int $categoryId
     * @param int[optional] $limit The number of items to get.
     * @param int[optional] $offset The offset.
     * @return array
     */
    public static function getAllByCategory($categoryId, $limit = 10, $offset = 0)
    {
        $items = (array) FrontendModel::get('database')->getRecords(
            'SELECT i.*, m.url
             FROM blocks AS i
             INNER JOIN meta AS m ON i.meta_id = m.id
             WHERE i.category_id = ? AND i.language = ?
             ORDER BY i.sequence ASC, i.id DESC LIMIT ?, ?',
            array($categoryId, FRONTEND_LANGUAGE, (int) $offset, (int) $limit));


        // no results?
        if (empty($items)) {
            return array();
        }

        // get detail action url
        $detailUrl = Navigation::getURLForBlock('Blocks', 'Detail');

        // prepare items for search
        foreach ($items as &$item) {
            if($item['page_id'] > 0){
                $item['link'] = Navigation::getURL($item['page_id']);
            }
            $item['full_url'] = $detailUrl . '/' . $item['url'];
        }

        // return
        return $items;
    }
    /**
     * Get all categories used
     *
     * @return array
     */
    public static function getAllCategories()
    {
        $return = (array) FrontendModel::get('database')->getRecords(
            'SELECT c.id, c.title AS label, m.url, COUNT(c.id) AS total, m.data AS meta_data
             FROM blocks_categories AS c
             INNER JOIN blocks AS i ON c.id = i.category_id AND c.language = i.language
             INNER JOIN meta AS m ON c.meta_id = m.id
             GROUP BY c.id
             ORDER BY c.sequence ASC',
            array(), 'id'
        );

        // loop items and unserialize
        foreach ($return as &$row) {
            if (isset($row['meta_data'])) {
                $row['meta_data'] = @unserialize($row['meta_data']);
            }
        }

        return $return;
    }

    /**
     * Fetches a certain category
     *
     * @param string $URL
     * @return array
     */
    public static function getCategory($URL)
    {
        $item = (array) FrontendModel::get('database')->getRecord(
            'SELECT i.*,
             m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
             m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
             m.title AS meta_title, m.title_overwrite AS meta_title_overwrite, m.url
             FROM blocks_categories AS i
             INNER JOIN meta AS m ON i.meta_id = m.id
             WHERE m.url = ?',
            array((string) $URL)
        );

        // no results?
        if (empty($item)) {
            return array();
        }

        // create full url
        $item['full_url'] = Navigation::getURLForBlock('Blocks', 'category') . '/' . $item['url'];

        return $item;
    }

    /**
     * Fetches a certain category
     *
     * @param string $URL
     * @return array
     */
    public static function getCategoryById($id)
    {
        $item = (array) FrontendModel::get('database')->getRecord(
            'SELECT i.*,
             m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
             m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
             m.title AS meta_title, m.title_overwrite AS meta_title_overwrite, m.url
             FROM blocks_categories AS i
             INNER JOIN meta AS m ON i.meta_id = m.id
             WHERE i.id = ?',
            array((string) $id)
        );

        // no results?
        if (empty($item)) {
            return array();
        }

        // create full url
        $item['full_url'] = Navigation::getURLForBlock('Blocks', 'category') . '/' . $item['url'];

        return $item;
    }


    /**
     * Get the number of items in a category
     *
     * @param int $categoryId
     * @return int
     */
    public static function getCategoryCount($categoryId)
    {
        return (int) FrontendModel::get('database')->getVar(
            'SELECT COUNT(i.id) AS count
             FROM blocks AS i
             WHERE i.category_id = ?',
            array((int) $categoryId)
        );
    }

}
