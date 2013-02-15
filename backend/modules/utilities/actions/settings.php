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
class BackendUtilitiesSettings extends BackendBaseActionEdit
{
	/**
	 * Execute the action
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


		// create form
		$this->frm = new BackendForm('settings');
		$this->frm->addText('classname_container', BackendModel::getModuleSetting($this->URL->getModule(), 'classname_container'));
		$this->frm->addText('fontsize_small', BackendModel::getModuleSetting($this->URL->getModule(), 'fontsize_small'));
		$this->frm->addText('fontsize_large', BackendModel::getModuleSetting($this->URL->getModule(), 'fontsize_large'));
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		parent::parse();

		$this->tpl->assign('godUser', BackendAuthentication::getUser()->isGod());

	}

	/**
	 * Validate the form
	 */
	protected function validateForm()
	{
		if($this->frm->isSubmitted())
		{
			$this->frm->cleanupFields();

			if($this->frm->isCorrect())
			{
				$fontsize_small = (int) $this->frm->getField("fontsize_small")->getValue();
				$fontsize_large = (int) $this->frm->getField("fontsize_large")->getValue();

				if($fontsize_small < 1)
				{
					$fontsize_small = 1;
				}

				if($fontsize_large < 1)
				{
					$fontsize_large = 1;
				}

				BackendModel::setModuleSetting($this->URL->getModule(), 'classname_container', (string) $this->frm->getField('classname_container')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'fontsize_small', (int) $fontsize_small);
				BackendModel::setModuleSetting($this->URL->getModule(), 'fontsize_large', (int) $fontsize_large);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_saved_settings');

				// redirect to the settings page
				$this->redirect(BackendModel::createURLForAction('settings') . '&report=saved');
			}
		}
	}
}
