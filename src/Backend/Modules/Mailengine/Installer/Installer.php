<?php
namespace Backend\Modules\Mailengine\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer for the mailengine module
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
		$this->addModule('Mailengine');

		// install the locale, this is set here beceause we need the module for this
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		$this->setModuleRights(1, 'Mailengine');

		$this->setActionRights(1, 'Mailengine', 'users');
		$this->setActionRights(1, 'Mailengine', 'add_user');
		$this->setActionRights(1, 'Mailengine', 'edit_user');
		$this->setActionRights(1, 'Mailengine', 'delete_user');
		$this->setActionRights(1, 'Mailengine', 'groups');
		$this->setActionRights(1, 'Mailengine', 'add_group');
		$this->setActionRights(1, 'Mailengine', 'edit_group');
		$this->setActionRights(1, 'Mailengine', 'delete_group');
		$this->setActionRights(1, 'Mailengine', 'templates');
		$this->setActionRights(1, 'Mailengine', 'add_template');
		$this->setActionRights(1, 'Mailengine', 'edit_template');
		$this->setActionRights(1, 'Mailengine', 'delete_template');
		$this->setActionRights(1, 'Mailengine', 'add');
		$this->setActionRights(1, 'Mailengine', 'edit');
		$this->setActionRights(1, 'Mailengine', 'delete');
		$this->setActionRights(1, 'Mailengine', 'send');
		$this->setActionRights(1, 'Mailengine', 'stats');
		$this->setActionRights(1, 'Mailengine', 'detail_stat');
		$this->setActionRights(1, 'Mailengine', 'export_users');
		$this->setActionRights(1, 'Mailengine', 'import_users');
		$this->setActionRights(1, 'Mailengine', 'export_demo');
		$this->setActionRights(1, 'Mailengine', 'settings');
		$this->setActionRights(1, 'Mailengine', 'preview');
		$this->setActionRights(1, 'Mailengine', 'test');
		$this->setActionRights(1, 'Mailengine', 'overlay_stat');
		$this->setActionRights(1, 'Mailengine', 'index');

		// add extra's
		$this->insertExtra('Mailengine', 'widget', 'Mailings', 'Mailings');
		$this->insertExtra('Mailengine', 'widget', 'Subscribe', 'Subscribe');
		$mailengineID = $this->insertExtra('Mailengine', 'block', 'Mailengine', null, null, 'N', 1000);

		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$navigationMailengineId = $this->setNavigation($navigationModulesId, 'Mailengine', 'mailengine/index');

		$this->setNavigation($navigationMailengineId, 'Newsletters', 'mailengine/index', array('mailengine/add',	'mailengine/edit',	'mailengine/send','mailengine/test'));
		$this->setNavigation($navigationMailengineId, 'Templates', 'mailengine/templates', array('mailengine/add_template',	'mailengine/edit_template'));
		$this->setNavigation($navigationMailengineId, 'Stats', 'mailengine/stats', array('mailengine/detail_stat'));
		$this->setNavigation($navigationMailengineId, 'Users', 'mailengine/users', array('mailengine/add_user',	'mailengine/edit_user',	'mailengine/import_users'));
		$this->setNavigation($navigationMailengineId, 'Groups', 'mailengine/groups', array('mailengine/add_group',	'mailengine/edit_group'));
		$this->setNavigation($navigationMailengineId, 'Settings', 'mailengine/settings');
	}
}
