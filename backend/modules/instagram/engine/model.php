<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the instagram module
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class BackendInstagramModel
{

	private static $access_token = '';
	private static $client_id;
	private static $client_secret;
	public static $instagram_url = 'https://api.instagram.com/v1/';
	public static $instagram_access_token_url = 'https://api.instagram.com/';
	private static $username;
	private static $website_url;

	/**
	 * Get settings from the db and fill in the properties
	 */
	private static function getSettings()
	{
		self::$client_id = BackendModel::getModuleSetting('instagram', 'client_id');
		self::$client_secret = BackendModel::getModuleSetting('instagram', 'client_secret');
		self::$website_url = BackendModel::getModuleSetting('instagram', 'website_url');
		self::$username = BackendModel::getModuleSetting('instagram', 'username');
		self::$access_token = BackendModel::getModuleSetting('instagram', 'access_token');
	}

	/**
	 * Get the instagram userid (from the api)
	 *
	 *     * @return bool
	 */
	public static function getUserId()
	{
		//--Get settings
		BackendInstagramModel::getSettings();

		//--Get the response from the Curl-call
		$response = BackendInstagramModel::executeCurl(self::$instagram_url . 'users/search/?q=' . self::$username . '&access_token=' . self::$access_token . '&client-id=' . self::$client_id);

		//--Check if there isn't an error
		if($response != false)
		{
			//--Get userid from the response
			$userid = (int)$response->data[0]->id;

			//--Check if the userid is larger then 0
			if($userid > 0)
			{
				//--Fill in the settings
				BackendModel::setModuleSetting("instagram", 'userid', $userid);

				//--Return true
				return true;
			}
		}

		//--If there is an error: return false
		return false;
	}

	/**
	 *
	 * Make a CURL call
	 *
	 * @param $url
	 * @param string $postfields
	 * @return bool|mixed
	 */
	public static function executeCurl($url, $postfields = "")
	{
		//--set options
		$options = array();
		$options[CURLOPT_RETURNTRANSFER] = true; //--return web page
		$options[CURLOPT_HEADER] = false; //--don't return headers
		$options[CURLOPT_FOLLOWLOCATION] = true; //-- follow redirects
		$options[CURLOPT_ENCODING] = ''; //-- handle compressed
		$options[CURLOPT_USERAGENT] = '-'; //-- who am i
		$options[CURLOPT_AUTOREFERER] = true; //-- set referer on redirect
		$options[CURLOPT_CONNECTTIMEOUT] = 120; //-- timeout on connect
		$options[CURLOPT_TIMEOUT] = 120; //-- timeout on response
		$options[CURLOPT_MAXREDIRS] = 10; //-- stop after 10 redirects
		$options[CURLOPT_URL] = $url;
		$options[CURLOPT_RETURNTRANSFER] = true;

		//--Check if the param postfields is empty
		if($postfields == "")
		{
			//--Postfields is empty -> get request
			$options[CURLOPT_CUSTOMREQUEST] = 'GET';
		}
		else
		{
			//--Postfields are filled in, make a post-request
			$options[CURLOPT_POST] = 1;
			$options[CURLOPT_POSTFIELDS] = $postfields;
		}

		//--Init cURL
		$curl = curl_init();

		//--Set options to cURL
		curl_setopt_array($curl, $options);

		//--Execute cURL
		$content = curl_exec($curl);

		//--Get cURL info
		$info = curl_getinfo($curl);

		//--Get cURL errors
		$error = curl_error($curl);

		//--Validate headers
		if(!in_array($info['http_code'], array(200))) return false;

		// validate and stop if needed
		if(!isset($info['content_type'])) return false;

		//--Close cURL connection
		curl_close($curl);

		//--Return json-decoded string
		return json_decode($content);
	}
}
