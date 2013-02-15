<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer for the utilities module
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class UtilitiesInstaller extends ModuleInstaller
{
	public function install()
	{
		// import the sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// install the module in the database
		$this->addModule('utilities');

		// install the locale, this is set here beceause we need the module for this
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		$this->setModuleRights(1, 'utilities');

		$this->setActionRights(1, 'utilities', 'settings');
		$this->setActionRights(1, 'utilities', 'index');

		// add extra's
		$this->insertExtra('utilities', 'widget', 'Fontsize', 'fontsize');
		$utilitiesID = $this->insertExtra('utilities', 'block', 'Utilities', null, null, 'N', 1000);

		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$navigationUtilitiesId = $this->setNavigation($navigationModulesId, 'Utilities', 'utilities/index');
	}
}
