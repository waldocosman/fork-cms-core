<?php

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
class MediaInstaller extends ModuleInstaller
{
	public function install()
	{
		// import the sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// install the module in the database
		$this->addModule('media');

		// install the locale, this is set here beceause we need the module for this
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		$this->setModuleRights(1, 'media');

		//$this->setActionRights(1, 'media', 'index');

		// add extra's
		//$mediaID = $this->insertExtra('media', 'block', 'Media', null, null, 'N', 1000);

//		$navigationModulesId = $this->setNavigation(null, 'Modules');
//		$navigationMediaId = $this->setNavigation($navigationModulesId, 'Media', 'media/index');
	}
}
