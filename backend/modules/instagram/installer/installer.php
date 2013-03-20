<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer for the instagram module
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class InstagramInstaller extends ModuleInstaller
{
	public function install()
	{
		// import the sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// install the module in the database
		$this->addModule('instagram');

		// install the locale, this is set here beceause we need the module for this
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		$this->setModuleRights(1, 'instagram');

		$this->setActionRights(1, 'instagram', 'settings');
		$this->setActionRights(1, 'instagram', 'get_access_token');
		$this->setActionRights(1, 'instagram', 'settings');
		$this->setActionRights(1, 'instagram', 'index');

		// add extra's
		$this->insertExtra('instagram', 'widget', 'UserMediaRecent', 'user_media_recent');
		$instagramID = $this->insertExtra('instagram', 'block', 'Instagram', null, null, 'N', 1000);

		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$navigationInstagramId = $this->setNavigation($navigationModulesId, 'Instagram', 'instagram/index');
	}
}
