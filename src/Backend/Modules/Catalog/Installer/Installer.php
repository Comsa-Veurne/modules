<?php

namespace Backend\Modules\Catalog\Installer;

use Backend\Core\Installer\ModuleInstaller;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer for the Catalog module
 *
 * @author Tim van Wolfswinkel <tim@webleads.nl>
 */
class Installer extends ModuleInstaller
{
    /**
     * @var    int
     */
    private $defaultCategoryId;

    /**
     * @var    int
     */
    private $defaultBrandId;

    /**
     * Add a category for a language
     *
     * @param string $language
     * @param string $title
     * @param string $url
     * @return int
     */
    private function addCategory($language, $title, $url, $parentId)
    {
        // build array
        $item['meta_id'] = $this->insertMeta($title, $title, $title, $url);
        $item['language'] = (string)$language;
        $item['title'] = (string)$title;
        $item['created_on'] = gmdate('Y-m-d H:i:00');
        $item['edited_on'] = gmdate('Y-m-d H:i:00');
        $item['parent_id'] = (int)$parentId;
        $item['image'] = '';
        $item['sequence'] = 1;

        return (int)$this->getDB()->insert('catalog_categories', $item);
    }

    /**
     * Add a default brand
     *
     * @param string $language
     * @param string $title
     * @param string $url
     * @return int
     */
    private function addBrand($language, $title, $url)
    {
        // build array
        $item['meta_id'] = $this->insertMeta($title, $title, $title, $url);
        $item['language'] = (string)$language;
        $item['title'] = (string)$title;
        $item['created_on'] = gmdate('Y-m-d H:i:00');
        $item['edited_on'] = gmdate('Y-m-d H:i:00');
        $item['sequence'] = 1;

        return (int)$this->getDB()->insert('catalog_brands', $item);
    }

    /**
     * Fetch the id of the first category in this language we come across
     *
     * @param string $language
     * @return int
     */
    private function getCategory($language)
    {
        return (int)$this->getDB()->getVar(
            'SELECT id
			 FROM catalog_categories
			 WHERE language = ?',
            array((string)$language));
    }

    public function install()
    {
        // load install.sql
        $this->importSQL(dirname(__FILE__) . '/Data/install.sql');

        // add 'catalog' as a module
        $this->addModule('Catalog');

        // import locale
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        // general settings
        $this->setSetting('Catalog', 'allow_comments', true);
        $this->setSetting('Catalog', 'requires_akismet', true);
        $this->setSetting('Catalog', 'spamfilter', false);
        $this->setSetting('Catalog', 'moderation', true);
        $this->setSetting('Catalog', 'overview_num_items', 10);
        $this->setSetting('Catalog', 'recent_products_full_num_items', 3);
        $this->setSetting('Catalog', 'allow_multiple_categories', true);

        $this->setSetting('Catalog', 'width1', (int)400);
        $this->setSetting('Catalog', 'height1', (int)300);
        $this->setSetting('Catalog', 'allow_enlargment1', true);
        $this->setSetting('Catalog', 'force_aspect_ratio1', true);

        $this->setSetting('Catalog', 'width2', (int)800);
        $this->setSetting('Catalog', 'height2', (int)600);
        $this->setSetting('Catalog', 'allow_enlargment2', true);
        $this->setSetting('Catalog', 'force_aspect_ratio2', true);

        $this->setSetting('Catalog', 'width3', (int)1600);
        $this->setSetting('Catalog', 'height3', (int)1200);
        $this->setSetting('Catalog', 'allow_enlargment3', true);
        $this->setSetting('Catalog', 'force_aspect_ratio3', true);

        $this->makeSearchable('Catalog');

        // module rights
        $this->setModuleRights(1, 'Catalog');

        // products and index
        $this->setActionRights(1, 'Catalog', 'Index');
        $this->setActionRights(1, 'Catalog', 'Add');
        $this->setActionRights(1, 'Catalog', 'Edit');
        $this->setActionRights(1, 'Catalog', 'Delete');

        // categories
        $this->setActionRights(1, 'Catalog', 'Categories');
        $this->setActionRights(1, 'Catalog', 'AddCategory');
        $this->setActionRights(1, 'Catalog', 'EditCategory');
        $this->setActionRights(1, 'Catalog', 'DeleteCategory');
        $this->setActionRights(1, 'Catalog', 'SequenceCategories');

        // specifications
        $this->setActionRights(1, 'Catalog', 'Specifications');
        $this->setActionRights(1, 'Catalog', 'EditSpecification');
        $this->setActionRights(1, 'Catalog', 'DeleteSpecification');
        $this->setActionRights(1, 'Catalog', 'SequenceSpecifications');

        // media
        $this->setActionRights(1, 'Catalog', 'MassMediaAction');
        $this->setActionRights(1, 'Catalog', 'Media');

        // images
        $this->setActionRights(1, 'Catalog', 'AddImage');
        $this->setActionRights(1, 'Catalog', 'EditImage');
        $this->setActionRights(1, 'Catalog', 'DeleteImage');
        $this->setActionRights(1, 'Catalog', 'SequenceMediaImages');

        // files
        $this->setActionRights(1, 'Catalog', 'AddFile');
        $this->setActionRights(1, 'Catalog', 'EditFile');
        $this->setActionRights(1, 'Catalog', 'DeleteFile');
        //$this->setActionRights(1, 'Catalog', 'SequenceFiles');

        // videos
        $this->setActionRights(1, 'Catalog', 'AddVideo');
        $this->setActionRights(1, 'Catalog', 'EditVideo');
        $this->setActionRights(1, 'Catalog', 'DeleteVideo');
        //$this->setActionRights(1, 'Catalog', 'SequenceVideos');

        // comments
        $this->setActionRights(1, 'Catalog', 'Comments');
        $this->setActionRights(1, 'Catalog', 'EditComment');
        $this->setActionRights(1, 'Catalog', 'DeleteSpam');
        $this->setActionRights(1, 'Catalog', 'MassCommentAction');

        // orders
        $this->setActionRights(1, 'Catalog', 'Orders');
        $this->setActionRights(1, 'Catalog', 'EditOrder');
        $this->setActionRights(1, 'Catalog', 'DeleteCompleted');
        $this->setActionRights(1, 'Catalog', 'MassOrderAction');

        // settings
        $this->setActionRights(1, 'Catalog', 'Settings');

        // categories
        $this->setActionRights(1, 'Catalog', 'Brands');
        $this->setActionRights(1, 'Catalog', 'AddBrand');
        $this->setActionRights(1, 'Catalog', 'EditBrand');
        $this->setActionRights(1, 'Catalog', 'DeleteBrand');
        $this->setActionRights(1, 'Catalog', 'SequenceBrands');

        // add extra's
        $catalogId = $this->insertExtra('Catalog', 'block', 'Catalog', null, null, 'N', 1000);
        $this->insertExtra('Catalog', 'widget', 'Categories', 'Categories', null, 'N', 1004);
        $this->insertExtra('Catalog', 'widget', 'ShoppingCart', 'ShoppingCart', null, 'N', 1005);
        $this->insertExtra('Catalog', 'widget', 'RecentProducts', 'RecentProducts', null, 'N', 1006);
        $this->insertExtra('Catalog', 'widget', 'Brands', 'Brands', null, 'N', 1007);

        // set navigation
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $navigationCatalogId = $this->setNavigation($navigationModulesId, 'Catalog');
        $this->setNavigation($navigationCatalogId, 'Products', 'catalog/index', array('catalog/add', 'catalog/edit', 'catalog/media', 'catalog/add_image', 'catalog/edit_image', 'catalog/add_file', 'catalog/edit_file', 'catalog/add_video', 'catalog/edit_video'));
        $this->setNavigation($navigationCatalogId, 'Categories', 'catalog/categories', array('catalog/add_category', 'catalog/edit_category'));
        $this->setNavigation($navigationCatalogId, 'Specifications', 'catalog/specifications', array('catalog/add_specification', 'catalog/edit_specification'));
        $this->setNavigation($navigationCatalogId, 'Comments', 'catalog/comments', array('catalog/edit_comment'));
        $this->setNavigation($navigationCatalogId, 'Orders', 'catalog/orders', array('catalog/edit_order'));
        $this->setNavigation($navigationCatalogId, 'Brands', 'catalog/brands', array('catalog/add_brand', 'catalog/edit_brand'));

        // settings navigation
        $navigationSettingsId = $this->setNavigation(null, 'Settings');
        $navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
        $this->setNavigation($navigationModulesId, 'Catalog', 'catalog/settings');
    }
}