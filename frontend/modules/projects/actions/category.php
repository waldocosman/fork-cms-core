<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the Category-action, it will display the overview of projects posts
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class FrontendProjectsCategory extends FrontendBaseBlock
{
	/**
	 * The category data
	 *
	 * @var array
	 */
	private $category;

	/**
	 * The items data
	 *
	 * @var array
	 */
	private $items;

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
		//--Get all the categories
		$categories = FrontendProjectsModel::getAllCategories();

		//--Fill array with possible categories
		$possibleCategories = array();
		foreach($categories as $category)
		{
			$possibleCategories[$category['url']] = $category['id'];
		}

		// requested category
		$requestedCategory = SpoonFilter::getValue($this->URL->getParameter(1, 'string'), array_keys($possibleCategories), 'false');

		// validate category
		if($requestedCategory == 'false') $this->redirect(FrontendNavigation::getURL(404));

		// set category
		$this->category = $categories[$possibleCategories[$requestedCategory]];

		// get articles
		$this->items = FrontendProjectsModel::getAllForCategory($requestedCategory, $this->pagination['limit'], $this->pagination['offset']);
		
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		// add into breadcrumb
		$this->breadcrumb->addElement(SpoonFilter::ucfirst(FL::lbl('Category')));
		$this->breadcrumb->addElement($this->category['label']);

		// set pageTitle
		$this->header->setPageTitle(SpoonFilter::ucfirst(FL::lbl('Category')));
		$this->header->setPageTitle($this->category['label']);

		// advanced SEO-attributes
		if(isset($this->category['meta_data']['seo_index'])) $this->header->addMetaData(array('name' => 'robots', 'content' => $this->category['meta_data']['seo_index']));
		if(isset($this->category['meta_data']['seo_follow'])) $this->header->addMetaData(array('name' => 'robots', 'content' => $this->category['meta_data']['seo_follow']));

		$this->tpl->assign('items', $this->items);
		$this->tpl->assign('category', $this->category);
		//--Hide title
		$this->tpl->assign('hideContentTitle', true);
	}
}
