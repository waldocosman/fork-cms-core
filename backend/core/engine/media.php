<?php

/*
*
* This class will handle (media)uploads on the Backend
*
*/

class BackendMedia
{

	/***
	 * Get the media-record
	 *
	 * @param int $id
	 * @return mixed
	 */
	public static function get($id)
	{

		$db = BackendModel::getDB();

		return $db->getRecord("SELECT * FROM media WHERE id = ?", array($id));

	}

}