<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the GetAccessToken action
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class BackendInstagramGetAccessToken extends BackendBaseAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		//--Get settings
		$client_id = BackendModel::getModuleSetting($this->URL->getModule(), 'client_id');
		$client_secret = BackendModel::getModuleSetting($this->URL->getModule(), 'client_secret');

		//--create redirect uri
		$redirect_uri = 'http://' . $_SERVER['SERVER_NAME'] . '/private/' . strtolower(BL::getWorkingLanguage()) . '/instagram/get_access_token';

		//--Create url
		$url = BackendInstagramModel::$instagram_access_token_url . 'oauth/access_token';

		//--Create post fields
		$postfields = 'client_id=' . $client_id;
		$postfields .= '&client_secret=' . $client_secret;
		$postfields .= '&grant_type=' . 'authorization_code';
		$postfields .= '&redirect_uri=' . $redirect_uri;
		$postfields .= '&code=' . $_GET['code'];


		//--Execute cURL
		$response = BackendInstagramModel::executeCurl($url, $postfields);

		//--Check if the response is ok.
		if ($response !== false && isset($response->access_token) && is_string($response->access_token))
		{
			//--Save the setting
			BackendModel::setModuleSetting($this->URL->getModule(), 'access_token', (string)$response->access_token);

			//--Get the userid and save it into the settings
			if (BackendInstagramModel::getUserId() === false)
			{
				//--Error so redirect
				$this->redirect(BackendModel::createURLForAction('settings') . '&error=error-userid');
			}


			//--Redirect
			$this->redirect(BackendModel::createURLForAction('settings') . '&report=saved');
		}
		else
		{
			//--Error so redirect
			$this->redirect(BackendModel::createURLForAction('settings') . '&error=error-occured');
		}

		parent::execute();

		$this->parse();
		$this->display();
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{

	}
}
