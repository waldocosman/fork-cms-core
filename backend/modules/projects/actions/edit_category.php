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
class BackendProjectsEditCategory extends BackendBaseActionEdit
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
		$this->id = $this->getParameter('id', 'int', null);
		if($this->id == null || !BackendProjectsModel::existsCategory($this->id))
		{
			$this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
		}

		$this->record = BackendProjectsModel::getCategory($this->id);
	}

	/**
	 * Load the form
	 */
	protected function loadForm()
	{
		// set hidden values
		$rbtHiddenValues[] = array('label' => BL::lbl('Published'), 'value' => 'N');
		$rbtHiddenValues[] = array('label' => BL::lbl('Hidden'), 'value' => 'Y');

		// create form
		$this->frm = new BackendForm('edit');
		$this->frm->addText('title', $this->record['title'], null, 'inputText title', 'inputTextError title');
		$this->frm->addEditor('introduction', $this->record['introduction']);
		$this->frm->addRadiobutton('hidden', $rbtHiddenValues, $this->record['hidden']);
		$this->frm->addImage('image');
		$this->frm->addCheckbox('delete_image');

		// meta
		$this->meta = new BackendMeta($this->frm, $this->record['meta_id'], 'title', true);
		$this->meta->setUrlCallback('BackendProjectsModel', 'getUrl', array($this->record['id']));
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		parent::parse();
		$this->tpl->assign('item', $this->record);

		// get url
		$url = BackendModel::getURLForBlock($this->URL->getModule(), 'category');
		$url404 = BackendModel::getURL(404);
		if($url404 != $url) $this->tpl->assign('detailURL', SITE_URL . $url);
	}

	/**
	 * Validate the form
	 */
	protected function validateForm()
	{
		if($this->frm->isSubmitted())
		{
			$this->frm->cleanupFields();

			// validation
			$fields = $this->frm->getFields();
			$fields['title']->isFilled(BL::err('FieldIsRequired'));

			$this->meta->validate();

			if($this->frm->isCorrect())
			{
				$item['meta_id'] = $this->meta->save(true);
				$item['title'] = $fields['title']->getValue();
				$item['introduction'] = $fields['introduction']->getValue();
				$item['language'] = BL::getWorkingLanguage();
				$item['hidden'] = $fields['hidden']->getValue();
				$item['image'] = $this->record['image'];

				// the image path
				$imagePath = FRONTEND_FILES_PATH . '/projects/images';

				// create folders if needed
				if(!SpoonDirectory::exists($imagePath . '/source')) SpoonDirectory::create($imagePath . '/source');
				if(!SpoonDirectory::exists($imagePath . '/128x128')) SpoonDirectory::create($imagePath . '/128x128');

				// if the image should be deleted
				if($this->frm->getField('delete_image')->isChecked())
				{
					// delete the image
					SpoonFile::delete($imagePath . '/source/' . $item['image']);

					// reset the name
					$item['image'] = null;
				}

				// new image given?
				if($this->frm->getField('image')->isFilled())
				{
					// delete the old image
					SpoonFile::delete($imagePath . '/source/' . $this->record['image']);

					// build the image name
					$item['image'] = $this->meta->getURL() . '.' . $this->frm->getField('image')->getExtension();

					// upload the image & generate thumbnails
					$this->frm->getField('image')->generateThumbnails($imagePath, $item['image']);
				}

				// rename the old image
				elseif($item['image'] != null)
				{
					// get the old file extension
					$imageExtension = SpoonFile::getExtension($imagePath . '/source/' . $item['image']);

					// get the new image name
					$newName = $this->meta->getURL() . '.' . $imageExtension;

					// only change the name if there is a difference
					if($newName != $item['image'])
					{
						// loop folders
						foreach(BackendModel::getThumbnailFolders($imagePath, true) as $folder)
						{
							// move the old file to the new name
							SpoonFile::move($folder['path'] . '/' . $item['image'], $folder['path'] . '/' . $newName);
						}

						// assign the new name to the database
						$item['image'] = $newName;
					}
				}

				BackendProjectsModel::updateCategory($this->id, $item);
				$item['id'] = $this->id;

				//				BackendSearchModel::saveIndex($this->getModule(), $item['id'], array('title' => $item['title'], 'text' => $item['title']));

				BackendModel::triggerEvent($this->getModule(), 'after_edit_category', $item);
				$this->redirect(BackendModel::createURLForAction('categories') . '&report=edited&highlight=row-' . $item['id']);
			}
		}
	}
}
