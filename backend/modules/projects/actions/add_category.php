<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the add-action, it will display a form to create a new item
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class BackendProjectsAddCategory extends BackendBaseActionAdd
{
	/**
	 * Execute the actions
	 */
	public function execute()
	{
		parent::execute();

		$this->loadForm();
		$this->validateForm();

		$this->parse();
		$this->display();
	}

	/**
	 * Load the form
	 */
	protected function loadForm()
	{
		$rbtHiddenValues[] = array('label' => BL::lbl('Published'), 'value' => 'N');
		$rbtHiddenValues[] = array('label' => BL::lbl('Hidden'), 'value' => 'Y');

		$this->frm = new BackendForm('add');
		$this->frm->addText('title', null, null, 'inputText title', 'inputTextError title');
		$this->frm->addEditor('introduction');
		$this->frm->addRadiobutton('hidden', $rbtHiddenValues, 'N');
		$this->frm->addImage('image');

		$this->meta = new BackendMeta($this->frm, null, 'title', true);
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		parent::parse();

		// assign the url for the detail page
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

			// validate the image
			if($this->frm->getField('image')->isFilled())
			{
				// image extension and mime type
				$this->frm->getField('image')->isAllowedExtension(array('jpg', 'png', 'gif', 'jpeg'), BL::err('JPGGIFAndPNGOnly'));
				$this->frm->getField('image')->isAllowedMimeType(array('image/jpg', 'image/png', 'image/gif', 'image/jpeg'), BL::err('JPGGIFAndPNGOnly'));
			}

			$this->meta->validate();

			if($this->frm->isCorrect())
			{
				$item['meta_id'] = $this->meta->save();
				$item['title'] = $fields['title']->getValue();
				$item['introduction'] = $fields['introduction']->getValue();
				$item['language'] = BL::getWorkingLanguage();
				$item['hidden'] = $fields['hidden']->getValue();
				$item['user_id'] = BackendAuthentication::getUser()->getUserId();
				$item['sequence'] = BackendProjectsModel::getMaximumCategorySequence() + 1;

				// the image path
				$imagePath = FRONTEND_FILES_PATH . '/projects/images';

				// create folders if needed
				if(!SpoonDirectory::exists($imagePath . '/source')) SpoonDirectory::create($imagePath . '/source');
				if(!SpoonDirectory::exists($imagePath . '/128x128')) SpoonDirectory::create($imagePath . '/128x128');

				// image provided?
				if($this->frm->getField('image')->isFilled())
				{
					// build the image name
					$item['image'] = $this->meta->getURL() . '.' . $this->frm->getField('image')->getExtension();

					// upload the image & generate thumbnails
					$this->frm->getField('image')->generateThumbnails($imagePath, $item['image']);
				}

				$item['id'] = BackendProjectsModel::insert($item);

				$item['id'] = BackendProjectsModel::insertCategory($item);

				//				BackendSearchModel::saveIndex($this->getModule(), $item['id'], array('title' => $item['title'], 'text' => $item['title']));

				BackendModel::triggerEvent($this->getModule(), 'after_add_category', $item);
				$this->redirect(BackendModel::createURLForAction('categories') . '&report=added&highlight=row-' . $item['id']);
			}
		}
	}
}
