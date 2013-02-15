<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a frontend widget
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class FrontendContestWidgetScorelist extends FrontendBaseWidget
{
	/**
	 * @var array
	 */
	private $record;

	/**
	 * Exceute the action
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
	private function loadData()
	{
		//--Get scorelist
		$this->record = FrontendContestModel::getScoreList();
	}

	/**
	 * Parse the widget
	 */
	protected function parse()
	{
		$this->tpl->assign('widgetScorelist', $this->record);
	}
}
