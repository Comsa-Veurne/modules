<?php
namespace Backend\Modules\Media\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer for the media module
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */

use Backend\Core\Installer\ModuleInstaller;

class Installer extends ModuleInstaller
{
	public function install()
	{
		// import the sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// install the module in the database
		$this->addModule('Media');

		// install the locale, this is set here beceause we need the module for this
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		$this->setModuleRights(1, 'Media');

		//$this->setActionRights(1, 'media', 'index');

		// add extra's
		//$mediaID = $this->insertExtra('media', 'block', 'Media', null, null, 'N', 1000);

//		$navigationModulesId = $this->setNavigation(null, 'Modules');
//		$navigationMediaId = $this->setNavigation($navigationModulesId, 'Media', 'media/index');
	}
}
