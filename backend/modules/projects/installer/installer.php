<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer for the projects module
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class ProjectsInstaller extends ModuleInstaller
{
	public function install()
	{
		// import the sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// install the module in the database
		$this->addModule('projects');

		// install the locale, this is set here beceause we need the module for this
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		$this->setModuleRights(1, 'projects');

		$this->setActionRights(1, 'projects', 'add_category');
		$this->setActionRights(1, 'projects', '');
		$this->setActionRights(1, 'projects', 'edit_category');
		$this->setActionRights(1, 'projects', 'delete_category');
		$this->setActionRights(1, 'projects', 'categories');
		$this->setActionRights(1, 'projects', 'add');
		$this->setActionRights(1, 'projects', 'edit');
		$this->setActionRights(1, 'projects', 'delete');
		$this->setActionRights(1, 'projects', 'index');

		// add extra's
		$projectsID = $this->insertExtra('projects', 'block', 'Projects', null, null, 'N', 1000);

		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$navigationProjectsId = $this->setNavigation($navigationModulesId, 'Projects');
		$this->setNavigation($navigationProjectsId, 'Projects', 'projects/index', array('projects/add',	'projects/edit'));
		$this->setNavigation($navigationProjectsId, 'Category', 'projects/categories', array('projects/add_category',	'projects/edit_category'));

	}
}
