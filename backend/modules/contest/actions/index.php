<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the index-action (default), it will display the overview of contest posts
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class BackendContestIndex extends BackendBaseActionIndex
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->loadDataGrid();

		$this->parse();
		$this->display();
	}

	/**
	 * Load the dataGrid
	 */
	protected function loadDataGrid()
	{
		$this->dataGrid = new BackendDataGridDB(
			BackendContestModel::QRY_DATAGRID_BROWSE_CONTESTS, array(BL::getWorkingLanguage())
		);


		$this->dataGrid->setColumnURL('count_questions', BackendModel::createURLForAction('edit') . '&amp;id=[id]#tabQuestions');

		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('edit'))
		{
			$this->dataGrid->setColumnURL('title', BackendModel::createURLForAction('edit') . '&amp;id=[id]');
			$this->dataGrid->addColumn('edit', null, BL::lbl('Edit'),BackendModel::createURLForAction('edit') . '&amp;id=[id]',BL::lbl('Edit'));
		}
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		// parse the dataGrid if there are results
		$this->tpl->assign(
			'dataGrid',
			($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false
		);
	}
}
