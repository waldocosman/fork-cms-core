<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer for the contest module
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class ContestInstaller extends ModuleInstaller
{
	public function install()
	{
		// import the sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// install the module in the database
		$this->addModule('contest');

		// install the locale, this is set here beceause we need the module for this
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		$this->setModuleRights(1, 'contest');

		$this->setActionRights(1, 'contest', 'add');
		$this->setActionRights(1, 'contest', 'edit');
		$this->setActionRights(1, 'contest', 'delete');
		$this->setActionRights(1, 'contest', 'add_question');
		$this->setActionRights(1, 'contest', '');
		$this->setActionRights(1, 'contest', 'edit_question');
		$this->setActionRights(1, 'contest', 'delete_question');
		$this->setActionRights(1, 'contest', 'index');

		// add extra's
		$this->insertExtra('contest', 'widget', 'Scorelist', 'scorelist');
		$this->insertExtra('contest', 'widget', 'Contest', 'contest');
		$contestID = $this->insertExtra('contest', 'block', 'Contest', null, null, 'N', 1000);

		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$navigationContestId = $this->setNavigation($navigationModulesId, 'Contest', 'contest/index');
	}
}
