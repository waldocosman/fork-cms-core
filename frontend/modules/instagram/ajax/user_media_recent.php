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
class FrontendInstagramAjaxUserMediaRecent extends FrontendBaseAJAXAction
{

	/**
	 * Name of the cachefile
	 *
	 * @var    string
	 */
	private $cacheFile;

	private $count = 10;

	private $images;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		//--Get max_id parameter (used to second, third,... ajax-request)
		$maxId = SpoonFilter::getPostValue('max_id', null, '', 'string');

		//--Get the number of images that need to be requested
		$this->count = FrontendModel::getModuleSetting($this->module, 'user_media_recent_count') > 0 ? FrontendModel::getModuleSetting($this->module, 'user_media_recent_count') : 10;

		$this->loadData($maxId);
		$this->display();
	}

	/**
	 * Load the data
	 *
	 * @param $maxId
	 */
	private function loadData($maxId)
	{
		//--Get the images from the API
		$this->images = FrontendInstagramModel::getUserMediaRecent($this->count, $maxId);
	}

	/**
	 * Display
	 */
	private function display()
	{
		//--Output
		$this->output(self::OK, $this->images);
	}
}
