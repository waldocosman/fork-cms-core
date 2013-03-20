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
class FrontendInstagramWidgetUserMediaRecent extends FrontendBaseWidget
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

		//--Add css to the template
		$this->header->addCSS('/frontend/modules/' . $this->getModule() . '/layout/css/instagram.css');

		//--Get the settings for the Javascript
		$settings = FrontendInstagramModel::getSettingsJs();

		//--Add data to Javascript
		$this->addJSData("settings", $settings);

		$this->loadTemplate();
		$this->loadData();

		$this->parse();
	}

	/**
	 * Load the data
	 */
	private function loadData()
	{
	}

	/**
	 * Parse the widget
	 */
	protected function parse()
	{
	}
}
