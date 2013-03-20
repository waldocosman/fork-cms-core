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
class FrontendInstagramModel
{
	private static $access_token = '';
	private static $client_id;
	private static $client_secret;
	private static $instagram_url = 'https://api.instagram.com/v1/';
	private static $redirect_uri;
	private static $username;
	private static $userid;
	private static $website_url;


	private static function getSettings()
	{
		self::$client_id = FrontendModel::getModuleSetting('instagram', 'client_id');
		self::$client_secret = FrontendModel::getModuleSetting('instagram', 'client_secret');
		self::$website_url = FrontendModel::getModuleSetting('instagram', 'website_url');
		self::$username = FrontendModel::getModuleSetting('instagram', 'username');
		self::$userid = FrontendModel::getModuleSetting('instagram', 'userid');
		self::$access_token = FrontendModel::getModuleSetting('instagram', 'access_token');
	}

	/**
	 *
	 * Get the userid
	 *
	 * @return bool
	 */
	public static function getUserId()
	{
		//--Get settings
		FrontendInstagramModel::getSettings();

		//--Execute Curl and get the response
		$response = FrontendInstagramModel::executeCurl(self::$instagram_url . 'users/search/?q=' . self::$username . '&access_token=' . self::$access_token . '&client-id=' . self::$client_id);

		//--Check if there isn't an error
		if($response != false)
		{
			//--Get userid from the response
			$userid = (int)$response->data[0]->id;

			//--Check if the userid is larger then 0
			if($userid > 0)
			{
				//--Fill in the settings
				FrontendModel::setModuleSetting("instagram", 'userid', $userid);

				//--Return true
				return true;
			}
		}

		//--If there is an error: return false
		return false;
	}

	public static function getUserMediaRecent($count = 20, $maxId = '')
	{

		//--Get the settings
		FrontendInstagramModel::getSettings();

		$url = self::$instagram_url . 'users/' . self::$userid . '/media/recent/?access_token=' . self::$access_token . '&client-id=' . self::$client_id . '&count=' . $count;

		//--Check if maxId is filled in
		$url .= $maxId != '' ? '&max_id=' . $maxId : '';

		//--Execute the Curl and get the response
		$response = FrontendInstagramModel::executeCurl($url);

		//--Check if the response is correct
		if($response !== false)
		{
			//--Initialize images-array
			$images = array();

			//--Loop the images from the response
			foreach($response->data as $key => $rowImage)
			{
				$image = array("images" => array("thumbnail" => array("url" => $rowImage->images->thumbnail->url, "width" => $rowImage->images->thumbnail->width, "height" => $rowImage->images->thumbnail->height), "low_resolution" => array("url" => $rowImage->images->low_resolution->url, "width" => $rowImage->images->low_resolution->width, "height" => $rowImage->images->low_resolution->height), "standard_resolution" => array("url" => $rowImage->images->standard_resolution->url, "width" => $rowImage->images->standard_resolution->width, "height" => $rowImage->images->standard_resolution->height)), "id" => $rowImage->id, "link" => $rowImage->link, "caption" => array("text" => isset($rowImage->caption->text) ? $rowImage->caption->text : "", "id" => isset($rowImage->caption->id) ? $rowImage->caption->id : "",));

				$images[$key] = $image;
			}

			//--Return the images
			return $images;
		}

		//--Error occured so false is returned
		return false;
	}

	/**
	 *
	 * Make a cURL call
	 *
	 * @param $url
	 * @param string $postfields
	 * @return bool|mixed
	 */
	public static function executeCurl($url, $postfields = "")
	{
		//--Set options
		$options = array();
		$options[CURLOPT_RETURNTRANSFER] = true; // return web page
		$options[CURLOPT_HEADER] = false; // don't return headers
		$options[CURLOPT_FOLLOWLOCATION] = true; // follow redirects
		$options[CURLOPT_ENCODING] = ''; // handle compressed
		$options[CURLOPT_USERAGENT] = '-'; // who am i
		$options[CURLOPT_AUTOREFERER] = true; // set referer on redirect
		$options[CURLOPT_CONNECTTIMEOUT] = 120; // timeout on connect
		$options[CURLOPT_TIMEOUT] = 120; // timeout on response
		$options[CURLOPT_MAXREDIRS] = 10; // stop after 10 redirects
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

	/**
	 * Get the settings to use in the JS
	 *
	 * @return array
	 */
	public static function getSettingsJs()
	{
		//--Get settings from module
		$settings = array("user_media_recent_count" => FrontendModel::getModuleSetting("instagram", "user_media_recent_count"));

		return $settings;
	}
}
