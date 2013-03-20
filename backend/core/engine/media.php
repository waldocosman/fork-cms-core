<?php

/*
*
* This class will handle (media)uploads on the Backend
*
*/

class BackendMedia
{

	/**
	 * The image upload field
	 *
	 * @var field
	 */
	private $field;

	/**
	 * FieldTypeFile
	 *
	 * @var int
	 */
	private $fieldTypeFile = 2;

	/**
	 * FieldTypeImage
	 *
	 * @var int
	 */
	private $fieldTypeImage = 1;

	/**
	 * The form instance
	 *
	 * @var Backendform
	 */
	private $frm;

	/**
	 * The id of the module-item
	 *
	 * @var int
	 */
	private $id;

	/**
	 * The media-items
	 *
	 * @var array
	 */
	private $mediaItems;

	/**
	 * The linked module
	 *
	 * @var string
	 */
	private $module;

	/**
	 * The linked action
	 *
	 * @var string
	 */

	private $action;

	/**
	 * Contstructor
	 *
	 * @param BackendForm $form
	 * @param $module
	 * @param $id
	 */
	public function __construct(BackendForm $form, $module, $id, $action = "")
	{
		//--Set form instance
		$this->frm = $form;

		//--Set module
		$this->module = $module;

		//--Set module
		$this->action = $action;

		//--Set the id
		$this->id = (int)$id;

		//--Load the data
		$this->loadData();

		//--load the form
		$this->loadForm();
	}

	/**
	 * Validate function
	 */
	public function validate()
	{
		//--Validate form
		$this->validateForm();
	}

	/***
	 * Get the media-record
	 *
	 * @return mixed
	 */
	private function get($id)
	{
		return BackendModel::getDB()->getRecord("SELECT * FROM media WHERE id = ?", array($id));
	}

	/**
	 * Get all the mediaitems linked to the module
	 *
	 *
	 */
	private function getFromModule()
	{
		$records = BackendModel::getDB()->getRecords("SELECT m.id, filename FROM media AS m
															INNER JOIN media_modules AS mm ON mm.media_id = m.id
														WHERE mm.module = ? AND mm.other_id = ?
														ORDER BY m.id", array($this->module, $this->id));

		//--Loop records
		if(!empty($records))
		{
			//--Get the thumbnail-folders
			$folders = BackendModel::getThumbnailFolders(FRONTEND_FILES_PATH . '/media/images', true);

			//--Create the image-links to the thumbnail folders
			foreach($records as &$row)
			{
				foreach($folders as $folder) $row['image_' . $folder['dirname']] = $folder['url'] . '/' . $folder['dirname'] . '/' . $row['filename'];
			}
		}
		
		$this->mediaItems = $records;
	}

	/**
	 * Load the data
	 */
	private function loadData()
	{
		$this->getFromModule();
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		//--Add image field
		$this->field = $this->frm->addImage('images');

		//--Check if mediaItems is empty
		if(!empty($this->mediaItems))
		{
			//--Loop the images and create checkbox
			foreach($this->mediaItems as $row)
			{
				$this->frm->addCheckbox("image-" . $row["id"]);
			}
		}
	}

	/**
	 * Validate the form
	 */
	private function validateForm()
	{
		//--is the form submitted?
		if($this->frm->isSubmitted())
		{
			//--no errors?
			if($this->frm->isCorrect())
			{
				//--Get the field
				$filImage = $this->frm->getField('images');

				//--Check if the field is filled in
				if($filImage->isFilled())
				{
					// image extension and mime type
					$filImage->isAllowedExtension(array('jpg', 'png', 'gif', 'jpeg'), BL::err('JPGGIFAndPNGOnly'));
					$filImage->isAllowedMimeType(array('image/jpg', 'image/png', 'image/gif', 'image/jpeg'), BL::err('JPGGIFAndPNGOnly'));

					//--Add media to database
					if(is_int($this->addFile()))
					{
						//--If media is added, redirect to the tabMedia
						SpoonHTTP::redirect(BackendModel::createURLForAction($this->action, $this->module) . "&id=" . $this->id . "&report=media-added#tabMedia");
					}
				}

				//--Check if the image-array is not empty.
				if(!empty($this->mediaItems))
				{
					//--Get folders
					$folders = BackendModel::getThumbnailFolders(FRONTEND_FILES_PATH . '/media/images', true);

					//--Loop the images
					foreach($this->mediaItems as $row)
					{
						//--Check if the delete parameter is filled in.
						if(SpoonFilter::getPostValue("image-" . $row["id"], null, "") == "on")
						{
							$image = $this->get((int)$row["id"]);

							if(!empty($image))
							{
								foreach($folders as $folder)
								{
									//--delete the image
									SpoonFile::delete($folder['path'] .  '/' . $row['filename']);
								}

								//--Delete images from the database
								$db = BackendModel::getDB();
								$db->delete("media_modules", "media_id=?", array($row["id"]));
								$db->delete("media", "id=?", array($row["id"]));
							}
						}
					}
				}
			}
		}
	}

	//--Getter for mediaItems
	public function getMediaItems()
	{
		if(empty($this->mediaItems))
		{
			return array();
		}

		return $this->mediaItems;
	}

	/**
	 * Add file
	 *
	 * @return int
	 */
	private function addFile()
	{
		//--Upload file
		$id = $this->uploadFile();

		//--Link the
		$this->linkMediaToModule($id);

		return $id;
	}


	/***
	 * Upload a file
	 *
	 */
	private function uploadFile()
	{
		//--Check if the file is an image or file
		if($this->isImage())
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
		$filename = $this->checkFilename();

		$item = array();
		$item["filename"] = $filename;
		$item["extension"] = $this->field->getExtension();
		$item["created_on"] = BackendModel::getUTCDate('Y-m-d H:i:s');
		$item["filesize"] = $this->field->getFileSize("b");

		$data = array();

		//--Check if file is an image to specify data
		if($this->isImage())
		{
			// create folders if needed
			if(!SpoonDirectory::exists($path . '/128x128')) SpoonDirectory::create($path . '/128x128');

			$item["filetype"] = $this->fieldTypeImage;
			$data["width"] = $this->field->getWidth();
			$data["height"] = $this->field->getHeight();

			// upload the image & generate thumbnails
			$this->field->generateThumbnails($path, $filename);
		}
		else
		{
			$item["filetype"] = $this->fieldTypeFile;

			// move the source file
			$this->field->moveFile($path . "/" . $filename);
		}

		//--Serialize data
		$item["data"] = serialize($data);

		//--Insert into media
		return BackendModel::getDB(true)->insert("media", $item);
	}

	/***
	 * Check if the field is an image
	 *
	 * @return boolean
	 */
	private function isImage()
	{
		//--Array with image-extensions
		$arrImages = array("jpg", "jpeg", "gif", "png");

		//--Check if the file is an image or file
		if(in_array($this->field->getExtension(), $arrImages))
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
	 * @return string
	 */
	private function checkFilename($filename = "", $extension = "", $try = 0)
	{
		//--Check if filename is empty
		if($filename == "")
		{
			$filename = substr($this->field->getFilename(), 0, 0 - (strlen($this->field->getExtension()) + 1));
		}

		//--Check if extension is empty
		if($extension == "")
		{
			$extension = $this->field->getExtension();
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

		$record = BackendModel::getDB()->getRecord("SELECT filename FROM media WHERE filename = ?", array($filename_full));
		if(is_null($record))
		{
			return $filename_full;
		}
		else
		{
			//--Get new filename
			return $this->checkFilename($filename, $extension, $try + 1);
		}
	}

	/***
	 * Link media to module
	 *
	 * @param $id
	 *
	 * @return int or boolean
	 */

	private function linkMediaToModule($media_id)
	{
		if($this->module != "" && $this->id > 0)
		{
			//--Calculate sequence
			$sequence = (int)BackendModel::getDB()->getVar('SELECT MAX(i.sequence)
			 FROM media_modules AS i
			 WHERE i.module = ? AND other_id = ?', array((int)$this->module, $this->id));
			$sequence += 1;

			$insert = array();
			$insert["media_id"] = $media_id;
			$insert["module"] = $this->module;
			$insert["other_id"] = $this->id;
			$insert["identifier"] = 0;
			$insert["sequence"] = $sequence;
			$insert["language"] = BackendLanguage::getWorkingLanguage();
			$insert["title"] = "";
			$insert["linktype"] = 0;

			//--Add record to db
			return BackendModel::getDB(true)->insert("media_modules", $insert);
		}

		return false;
	}
}