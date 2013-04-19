<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the Detail-action, it will display the overview of projects posts
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class FrontendProjectsDetail extends FrontendBaseBlock
{
	/**
	 * The record data
	 *
	 * @var array
	 */
	private $record;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->loadTemplate();
		$this->loadData();
		$this->parse();
	}

	/**
	 * Load the data
	 */
	protected function loadData()
	{
		//--Check the params
		if($this->URL->getParameter(1) === null) $this->redirect(FrontendNavigation::getURL(404));

		//--Get record
		$this->record = FrontendProjectsModel::get($this->URL->getParameter(1));

		//-- If record empty, 404
		if(empty($this->record)) $this->redirect(FrontendNavigation::getURL(404));

		// overwrite URLs
		$this->record['category_full_url'] = FrontendNavigation::getURLForBlock('projects', 'category') . '/' . $this->record['category_url'];
		$this->record['full_url'] = FrontendNavigation::getURLForBlock('projects', 'detail') . '/' . $this->record['url'];
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{

		// build Facebook Open Graph-data
		if(FrontendModel::getModuleSetting('core', 'facebook_admin_ids', null) !== null || FrontendModel::getModuleSetting('core', 'facebook_app_id', null) !== null)
		{
			// add specified image
			if(isset($this->record['image']) && $this->record['image'] != '') $this->header->addOpenGraphImage(FRONTEND_FILES_URL . '/projects/images/source/' . $this->record['image']);

			// add images from content
			$this->header->extractOpenGraphImages($this->record['text']);

			// add additional OpenGraph data
			$this->header->addOpenGraphData('title', $this->record['title'], true);
			$this->header->addOpenGraphData('type', 'article', true);
			$this->header->addOpenGraphData('url', SITE_URL . $this->record['full_url'], true);
			$this->header->addOpenGraphData('site_name', FrontendModel::getModuleSetting('core', 'site_title_' . FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE), true);
			$this->header->addOpenGraphData('description', $this->record['title'], true);
		}

		// when there are 2 or more categories with at least one item in it, the category will be added in the breadcrumb
		if(count(FrontendProjectsModel::getAllCategories()) > 1) $this->breadcrumb->addElement($this->record['category_title'], FrontendNavigation::getURLForBlock('projects', 'category') . '/' . $this->record['category_url']);

		// add into breadcrumb
		$this->breadcrumb->addElement($this->record['title']);

		// set meta
		$this->header->setPageTitle($this->record['meta_title'], ($this->record['meta_title_overwrite'] == 'Y'));
		$this->header->addMetaDescription($this->record['meta_description'], ($this->record['meta_description_overwrite'] == 'Y'));
		$this->header->addMetaKeywords($this->record['meta_keywords'], ($this->record['meta_keywords_overwrite'] == 'Y'));

		// advanced SEO-attributes
		if(isset($this->record['meta_data']['seo_index'])) $this->header->addMetaData(array('name' => 'robots', 'content' => $this->record['meta_data']['seo_index']));
		if(isset($this->record['meta_data']['seo_follow'])) $this->header->addMetaData(array('name' => 'robots', 'content' => $this->record['meta_data']['seo_follow']));

		$this->header->setCanonicalUrl($this->record['full_url']);

		$this->tpl->assign('item', $this->record);

		// assign navigation
		$this->tpl->assign('navigation', FrontendProjectsModel::getNavigation($this->record['id']));

		//--Hide title
		$this->tpl->assign('hideContentTitle', true);
	}
}
