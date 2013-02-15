<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is an ajax handler
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class FrontendUtilitiesAjaxFontsize extends FrontendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// get parameters
		$intFontSize = SpoonFilter::getPostValue('fontsize', null, 10, 'int');

		//--Opslaan van de grootte in de sessie
		SpoonSession::set("fontsize", $intFontSize);

		// output
		$this->output(self::OK, null, FL::msg('Success'));
	}
}
