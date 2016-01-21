<?php

namespace Backend\Modules\Blocks\Installer;

use Backend\Core\Installer\ModuleInstaller;

/**
 * Installer for the Blocks module
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Installer extends ModuleInstaller
{
    public function install()
    {
        // import the sql
        $this->importSQL(dirname(__FILE__) . '/Data/install.sql');

        // install the module in the database
        $this->addModule('Blocks');

        // install the locale, this is set here beceause we need the module for this
        $this->importLocale(dirname(__FILE__) . '/Data/locale.xml');

        $this->setModuleRights(1, 'Blocks');

        $this->setActionRights(1, 'Blocks', 'Index');
        $this->setActionRights(1, 'Blocks', 'Add');
        $this->setActionRights(1, 'Blocks', 'Edit');
        $this->setActionRights(1, 'Blocks', 'Delete');
        $this->setActionRights(1, 'blocks', 'Sequence');
        $this->setActionRights(1, 'Blocks', 'Categories');
        $this->setActionRights(1, 'Blocks', 'AddCategory');
        $this->setActionRights(1, 'Blocks', 'EditCategory');
        $this->setActionRights(1, 'Blocks', 'DeleteCategory');
        $this->setActionRights(1, 'Blocks', 'SequenceCategories');

        // add extra's
        $navigationModulesId = $this->setNavigation(null, 'Modules');
        $navigationBlocksId = $this->setNavigation($navigationModulesId, 'Blocks');
        $this->setNavigation(
            $navigationBlocksId, 'Blocks', 'blocks/index',
            array('blocks/add', 'blocks/edit')
        );
        $this->setNavigation(
            $navigationBlocksId, 'Categories', 'blocks/categories',
            array('blocks/add_category', 'blocks/edit_category')
        );

    }
}
