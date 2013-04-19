<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the index-action (default), it will display the overview of projects posts
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class BackendProjectsIndex extends BackendBaseActionIndex
{
	/**
	 * The category where is filtered on
	 *
	 * @var    array
	 */
	private $category;

	/**
	 * The id of the category where is filtered on
	 *
	 * @var    int
	 */
	private $categoryId;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// set category id
		$this->categoryId = SpoonFilter::getGetValue('category', null, null, 'int');
		if($this->categoryId == 0) $this->categoryId = null;
		else
		{
			// get category
			$this->category = BackendProjectsModel::getCategory($this->categoryId);

			// reset
			if(empty($this->category))
			{
				// reset GET to trick Spoon
				$_GET['category'] = null;

				// reset
				$this->categoryId = null;
			}
		}

		$this->loadDataGrid();

		$this->parse();
		$this->display();
	}

	/**
	 * Load the dataGrid
	 */
	protected function loadDataGrid()
	{

		// filter on category?
		if($this->categoryId != null)
		{
			// create datagrid
			$this->dataGrid = new BackendDataGridDB(BackendProjectsModel::QRY_DATAGRID_BROWSE_PROJECTS_FOR_CATEGORY, array($this->categoryId, BL::getWorkingLanguage()));

			// set the URL
			$this->dataGrid->setURL('&amp;category=' . $this->categoryId, true);
		}

		else
		{
			$this->dataGrid = new BackendDataGridDB(BackendProjectsModel::QRY_DATAGRID_BROWSE_PROJECTS, array(BL::getWorkingLanguage()));
		}

		$this->dataGrid->enableSequenceByDragAndDrop();

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('edit'))
		{
			$this->dataGrid->setColumnURL('title', BackendModel::createURLForAction('edit') . '&amp;id=[id]');
			$this->dataGrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::lbl('Edit'));
		}
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		// parse the dataGrid if there are results
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);

		// get categories
		$categories = BackendProjectsModel::getCategories(true);

		// multiple categories?
		if(count($categories) > 1)
		{
			// create form
			$frm = new BackendForm('filter', null, 'get', false);

			// create element
			$frm->addDropdown('category', $categories, $this->categoryId);
			$frm->getField('category')->setDefaultElement('');

			// parse the form
			$frm->parse($this->tpl);
		}

		// parse category
		if(!empty($this->category)) $this->tpl->assign('filterCategory', $this->category);
	}
}
