<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the FileUpload-action, it will display the overview of profiles posts
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class FrontendProfilesFileUpload extends FrontendBaseBlock
{


	private $frm;
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// profile logged in
		if(FrontendProfilesAuthentication::isLoggedIn())
		{
			$this->loadTemplate();

			if(isset($_GET['file']) && $_GET['file'] == "done")
			{
				$this->parseSuccess();
			}
			else
			{
				$this->loadData();
				$this->validateForm();
				$this->parse();
			}
		}
		else
		{
			$this->redirect(FrontendNavigation::getURLForBlock('profiles', 'login') . '?queryString=' . FrontendNavigation::getURLForBlock('profiles', 'file_upload'),307);
		}
	}

	/**
	 * Load the data
	 */
	protected function loadData()
	{
		$this->frm = new FrontendForm('file');
		$this->frm->addImage("image");

	}

	/**
	 * Check the form
	 */
	protected function validateForm()
	{

		//--Check if form is submitted
		if($this->frm->isSubmitted())
		{
			//--Get file
			$fileProfile = $this->frm->getField("image");

			//--check if the field is empty
			$fileProfile->isFilled(FL::getError("FileRequired"));
			if($this->frm->isCorrect(true))
			{
				if(is_int(FrontendMedia::addFile($fileProfile, "profile", 1)))
				{

					//--Redirect
					$redirect = SITE_URL . '/' . $this->URL->getQueryString();
					$redirect .= (stripos($redirect, '?') === false) ? '?' : '&';
					$redirect .= 'file=done';

					// redirect with identifier
					SpoonHTTP::redirect($redirect);

				}
			}
		}
		return false;
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		$this->frm->parse($this->tpl);
	}

	/**
	 * Parse the page
	 */
	protected function parseSuccess()
	{
		// assign items
		$this->tpl->assign('success', true);
	}

}
