<?php

/*
*
* This class will handle (media)uploads on the Frontend
*
*/

class FrontendMedia
{

	protected static $field;

	private static $fieldTypeImage = 1;

	private static $fieldTypeFile = 2;

	public static function addFile($field, $module = "", $other_id = null, $identifier = "")
	{

		self::$field = $field;


		//--Upload file
		$id = self::uploadFile($field);


		//--Check if there is a module and id available
		if($module != "" && !is_null($other_id))
		{
			self::linkMediaToModule($id, $module, $other_id, $identifier);
		}

		return $id;
	}


	/***
	 * Upload a file
	 *
	 * @param $field
	 */
	public static function uploadFile()
	{
		//--Check if the file is an image or file
		if(self::isImage())
		{
			// the image path
			$path = FRONTEND_FILES_PATH . '/media/images';
		}
		else
		{
			// the file path
			$path = FRONTEND_FILES_PATH . '/media/files';
		}

		// create folders if needed
		if(!SpoonDirectory::exists($path . '/source')) SpoonDirectory::create($path . '/source');


		// build the filename
		$filename = self::checkFilename();

		$item = array();
		$item["filename"] = $filename;
		$item["extension"] = self::$field->getExtension();
		$item["created_on"] = FrontendModel::getUTCDate('Y-m-d H:i:s');
		$item["filesize"] = self::$field->getFileSize("b");

		$data = array();

		//--Check if file is an image to specify data
		if(self::isImage())
		{

			// create folders if needed
			if(!SpoonDirectory::exists($path . '/128x128')) SpoonDirectory::create($path . '/128x128');

			$item["filetype"] = self::$fieldTypeImage;
			$data["width"] = self::$field->getWidth();
			$data["height"] = self::$field->getHeight();

			// upload the image & generate thumbnails
			self::$field->generateThumbnails($path, $filename);

		}
		else
		{
			$item["filetype"] = self::$fieldTypeFile;

			// move the source file
			self::$field->moveFile($path . "/". $filename);
		}



		//--Serialize data
		$item["data"] = serialize($data);

		// get db
		$db = FrontendModel::getDB(true);

		//--Insert into media
		return $db->insert("media", $item);

	}

	/***
	 * Check if the field is an image
	 *
	 * @param $field
	 */
	protected static function isImage()
	{
		//--Array with image-extensions
		$arrImages = array("jpg", "jpeg", "gif", "png");

		//--Check if the file is an image or file
		if(in_array(self::$field->getExtension(), $arrImages))
		{
			return true;
		}

		return false;
	}

	/**
	 * Build the filename
	 *
	 * @param $filename
	 * @param $extension
	 * @param $try
	 *
	 */
	protected static function checkFilename($filename = "", $extension = "", $try = 0)
	{
		//--Check if filename is empty
		if($filename == "")
		{
			$filename = substr(self::$field->getFilename(), 0, 0 - (strlen(self::$field->getExtension())+1));
		}

		//--Check if extension is empty
		if($extension == "")
		{
			$extension = self::$field->getExtension();
		}

		if($try > 0)
		{
			$filename_full = $filename . $try . "." . $extension;
		}
		else
		{
			//--Get filename
			$filename_full = $filename . "." . $extension;
		}

		$db = FrontendModel::getDB();
		$record = $db->getRecord("SELECT filename FROM media WHERE filename = ?", array($filename_full));
		if(is_null($record))
		{
			return $filename_full;
		}
		else
		{
			//--Get new filename
			return self::checkFilename($filename, $extension, $try +1);
		}
	}
	/***
	 * Link media to module
	 * @param $id
	 * @param $module
	 * @param $other_id
	 * @param string $identifier
	 */

	protected static function linkMediaToModule($media_id, $module, $other_id, $identifier = "")
	{

		$db = FrontendModel::getDB(true);

		//--Calculate sequence
		$sequence = (int) $db->getVar(
									'SELECT MAX(i.sequence)
									 FROM media_modules AS i
									 WHERE i.module = ? AND other_id = ? and identifier = ?',
									array((int) $module, $other_id, $identifier));
		$sequence +=1;

		$insert = array();
		$insert["media_id"] = $media_id;
		$insert["module"] = $module;
		$insert["other_id"] = $other_id;
		$insert["identifier"] = $identifier;
		$insert["sequence"] = $sequence;
		$insert["language"] = FRONTEND_LANGUAGE;
		$insert["title"] = "";
		$insert["linktype"] = 0;

		//--Add record to db
		return $db->insert("media_modules", $insert);
	}

}