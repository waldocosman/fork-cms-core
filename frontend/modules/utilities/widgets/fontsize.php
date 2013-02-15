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
class FrontendUtilitiesWidgetFontsize extends FrontendBaseWidget
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

		//--Get fontsize
		$intFontSize = SpoonSession::get("fontsize") != null ? SpoonSession::get("fontsize") :  FrontendModel::getModuleSetting($this->module, "fontsize_small")/* ::getModuleSetting("utilities", 'fontsize_small')*/ ;

		$this->record = array(	"fontsize" => $intFontSize,
								"fontsize_small" => FrontendModel::getModuleSetting($this->module, 'fontsize_small'),
								"fontsize_large" => FrontendModel::getModuleSetting($this->module, 'fontsize_large'),
								"classname_container" => FrontendModel::getModuleSetting($this->module, 'classname_container')
		);

		//--Add data to Javascript
		$this->addJSData("settings", $this->record);

	}

	/**
	 * Parse the widget
	 */
	protected function parse()
	{
		$this->tpl->assign('widgetFontsize', $this->record);
	}
}
