<?php
namespace Backend\Modules\Gallery\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */
use Backend\Core\Installer\ModuleInstaller;

/**
 * Installer for the galleria module
 *
 * @author John Poelman <john.poelman@bloobz.be>
 */
class Installer extends ModuleInstaller
{
	public function install()
	{
		// import the sql
		$this->importSQL(dirname(__FILE__) . '/Data/Install.sql');

		// install the module in the database
		$this->addModule('Gallery');

		// install the locale, this is set here beceause we need the module for this
		$this->importLocale(dirname(__FILE__) . '/Data/Locale.xml');
		
		// modulerights
		$this->setModuleRights(1, 'gallery');

		// actionrights
		$this->setActionRights(1, 'Gallery', 'albums');
		$this->setActionRights(1, 'Gallery', 'add_album');
		$this->setActionRights(1, 'Gallery', 'edit_album');
		$this->setActionRights(1, 'Gallery', 'delete_album');
		$this->setActionRights(1, 'Gallery', 'categories');
		$this->setActionRights(1, 'Gallery', 'add_category');
		$this->setActionRights(1, 'Gallery', 'edit_category');
		$this->setActionRights(1, 'Gallery', 'delete_category');
		$this->setActionRights(1, 'Gallery', 'add');
		$this->setActionRights(1, 'Gallery', 'edit');
		$this->setActionRights(1, 'Gallery', 'delete');
		$this->setActionRights(1, 'Gallery', 'settings');

		// add extra's
//		$this->insertExtra('Gallery', 'widget', 'Slideshow', 'slideshow');
//		$this->insertExtra('Gallery', 'widget', 'Gallery', 'gallery');
		$GalleryID = $this->insertExtra('Gallery', 'block', 'Gallery', null, null, 'N', 1000);
				
		// module navigation
		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$navigationGalleryId = $this->setNavigation($navigationModulesId, 'Gallery', 'gallery/albums');
		
		$this->setNavigation($navigationGalleryId, 'Albums', 'gallery/albums', array(
				'gallery/add_album',
				'gallery/edit_album',
				'gallery/delete_album',
				'gallery/add',
				'gallery/edit',
				'gallery/delete'
		));
		
		$this->setNavigation($navigationGalleryId, 'Categories', 'gallery/categories', array(
				'gallery/add_category',
				'gallery/edit_category',
				'gallery/delete_category'
		));
		
		// settings navigation
		$navigationSettingsId = $this->setNavigation(null, 'Settings');
		$navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
		$this->setNavigation($navigationModulesId, 'Gallery', 'gallery/settings');
		
		// loop languages
		foreach($this->getLanguages() as $language)
		{
			// check if a page for galleria already exists in this language
			// @todo refactor this if statement
			if((int) $this->getDB()->getVar('SELECT COUNT(id)
					FROM pages AS p
					INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
					WHERE b.extra_id = ? AND p.language = ?',
					array($GalleryID, $language)) == 0)
			{
				// insert galleria page
				$this->insertPage(
						array(
								'title' => 'Gallery',
								'type' => 'root',
								'language' => $language
						),
						null,
						array('extra_id' => $GalleryID, 'position' => 'main'));
			}
		}
	}
}
