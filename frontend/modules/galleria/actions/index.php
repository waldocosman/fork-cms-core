<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the Index-action, it will display the overview of galleria posts
 *
 * @author John Poelman <john.poelman@bloobz.be>
 */
class FrontendGalleriaIndex extends FrontendBaseBlock
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
		$this->record = false;
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		$this->tpl->assign('items', $this->record);
	}
}
