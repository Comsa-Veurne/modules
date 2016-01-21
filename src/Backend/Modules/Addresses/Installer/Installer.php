<?php
namespace Backend\Modules\Addresses\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer for the addresses module
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
		$this->addModule('Addresses');

		// install the locale, this is set here beceause we need the module for this
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		$this->setModuleRights(1, 'Addresses');

		$this->setActionRights(1, 'Addresses', 'add');
		$this->setActionRights(1, 'Addresses', 'edit');
		$this->setActionRights(1, 'Addresses', 'delete');
		$this->setActionRights(1, 'Addresses', 'groups');
		$this->setActionRights(1, 'Addresses', 'add_group');
		$this->setActionRights(1, 'Addresses', 'edit_group');
		$this->setActionRights(1, 'Addresses', 'delete_group');
		$this->setActionRights(1, 'Addresses', 'index');
		$this->setActionRights(1, 'Addresses', 'update_lat_lng');

		// add extra's
		$addressesID = $this->insertExtra('Addresses', 'block', 'Addresses', null, null, 'N', 1000);

		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$navigationAddressesId = $this->setNavigation($navigationModulesId, 'Addresses', 'addresses/index');
		$this->setNavigation($navigationAddressesId, 'Addresses', 'addresses/index', array('addresses/add',	'addresses/edit',	'addresses/send', 'update_lat_lng'));
		$this->setNavigation($navigationAddressesId, 'Groups', 'addresses/groups', array('addresses/add_group',	'addresses/edit_group'));
	}
}
