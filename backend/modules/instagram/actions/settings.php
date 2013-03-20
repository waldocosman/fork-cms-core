<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit-action, it will display a form with the item data to edit
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class BackendInstagramSettings extends BackendBaseActionEdit
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		$this->loadData();
		$this->loadForm();
		$this->validateForm();

		$this->parse();
		$this->display();
	}

	/**
	 * Load the item data
	 */
	protected function loadData()
	{

	}

	/**
	 * Load the form
	 */
	protected function loadForm()
	{
		// create form
		$this->frm = new BackendForm('settings');

		//--The number of images received by the API in 1 ajax-call (max 20).
		$this->frm->addText('user_media_recent_count', BackendModel::getModuleSetting($this->URL->getModule(), 'user_media_recent_count'));


		//--Contains the client_id (from API Instagram)
		$this->frm->addText('client_id', BackendModel::getModuleSetting($this->URL->getModule(), 'client_id'));

		//--Contains the client secret
		$this->frm->addText('client_secret', BackendModel::getModuleSetting($this->URL->getModule(), 'client_secret'));

		//--Contains the website url
		$this->frm->addText('website_url', BackendModel::getModuleSetting($this->URL->getModule(), 'website_url'));

		//--Redirect uri
		$this->frm->addText('redirect_uri', BackendModel::getModuleSetting($this->URL->getModule(), 'redirect_uri'));

		//--Fill in the username to connect with
		$this->frm->addText('username', BackendModel::getModuleSetting($this->URL->getModule(), 'username'));

		//--Access token is generated
		$this->frm->addText('access_token', BackendModel::getModuleSetting($this->URL->getModule(), 'access_token'));
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{

		BackendInstagramModel::getUserId();

		parent::parse();

		//--Get client id
		$client_id = BackendModel::getModuleSetting($this->URL->getModule(), 'client_id');

		if ($client_id != "")
		{
			//--Create redirect-uri
			$redirect_uri = 'http://' . $_SERVER['SERVER_NAME'] . str_replace('settings', 'get_access_token', $_SERVER['REQUEST_URI']);

			$this->tpl->assign("access_token_url", BackendInstagramModel::$instagram_access_token_url . 'oauth/authorize/?client_id=' . $client_id . '&redirect_uri=' . $redirect_uri . '&response_type=code');
		}
		else
		{
			$this->tpl->assign("access_token_url", false);
		}

	}

	/**
	 * Validate the form
	 */
	protected function validateForm()
	{
		if ($this->frm->isSubmitted())
		{
			//--Cleanup fields
			$this->frm->cleanupFields();

			//--Check if post is correct
			if ($this->frm->isCorrect())
			{

				//--Save the settings
				BackendModel::setModuleSetting($this->URL->getModule(), 'client_id', (string)$this->frm->getField('client_id')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'client_secret', (string)$this->frm->getField('client_secret')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'website_url', (string)$this->frm->getField('website_url')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'redirect_uri', (string)$this->frm->getField('redirect_uri')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'username', (string)$this->frm->getField('username')->getValue());


				$userMediaRecentCount = $this->frm->getField('user_media_recent_count')->getValue() > 0 && $this->frm->getField('user_media_recent_count')->getValue() <= 20 ? $this->frm->getField('user_media_recent_count')->getValue() : 10;
				BackendModel::setModuleSetting($this->URL->getModule(), 'user_media_recent_count', (int)$userMediaRecentCount);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_saved_settings');

				//--Redirect & save report
				$this->redirect(BackendModel::createURLForAction('settings') . '&report=saved');
			}
		}
	}
}
