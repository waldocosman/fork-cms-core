<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a frontend widget
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class FrontendContestWidgetContest extends FrontendBaseWidget
{
	/**
	 * @var array
	 */
	private $record;

	private $frm;
	/**
	 * Exceute the action
	 */
	public function execute()
	{
		parent::execute();

		$this->loadTemplate();
		$this->loadData();

		//--Check if the user is logged in
		$loggedin = FrontendProfilesAuthentication::isLoggedIn();

		//--Check if the game is for profiles only
		if($this->record["profiles_only"] == "Y" && $loggedin == false)
		{
			$this->parseProfilesOnly();

			return $this->tpl->getContent(FRONTEND_MODULES_PATH . '/' . $this->getModule() . '/layout/widgets/' . $this->getAction() . '.tpl');
		}

		//--Check if the person already has played the game
		if($this->checkMultipleParticipations() && !isset($_GET["contest"]))
		{
			$this->parseMultipleParticipations();

			return $this->tpl->getContent(FRONTEND_MODULES_PATH . '/' . $this->getModule() . '/layout/widgets/' . $this->getAction() . '.tpl');
		}


		if(isset($_GET['contest']) && $_GET['contest'] == "done")
		{
			$this->parseSuccessMessage();

			return $this->tpl->getContent(FRONTEND_MODULES_PATH . '/' . $this->getModule() . '/layout/widgets/' . $this->getAction() . '.tpl');
		}
		else
		{
			$this->loadForm();
			$this->validateForm();
			$this->parse();
		}
	}

	/**
	 * Load the data
	 */
	private function loadData()
	{
		$this->record = FrontendContestModel::get($this->data['id']);

		//--Replace record with id-key value
		if(!empty($this->record))
		{
			$this->record = $this->record[$this->data['id']];
		}


	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		//--Create a form
		$this->frm = new FrontendForm('formContest' . $this->record['id']);

		//--Check if there are questions
		if(!empty($this->record['questions']))
		{

			$this->frm->setMethod("post");

			//--Loop the questions
			foreach($this->record['questions'] as &$row)
			{
				$row['fieldname'] = "answer_" . $this->record['id'] . "_" . $row['id'];
				$input= $this->frm->addText($row['fieldname']);//->setAttribute("required");

				$row['field'] = $input->parse();
				$row['fielderror'] = "";
			}
		}
	}

	/**
	 * Check if the form is submitted and validate the fields
	 */
	private function validateForm()
	{
		if($this->frm->isSubmitted())
		{

			foreach($this->record['questions'] as &$row)
			{

				if($row["required"] == "Y")
				{
					//--Check the error
					$this->frm->getField($row['fieldname'])->isFilled(FL::err('FieldIsRequired'));
				}


				if($row["must_be_correct"] == "Y")
				{

					//--Check the error
					$this->frm->getField($row['fieldname'])->isFilled(FL::err('FieldIsRequired'));

					//--Check if the answer is correct
					if($this->frm->getField($row['fieldname'])->getValue() != $row["answer"])
					{
						$this->frm->getField($row['fieldname'])->setError(FL::err('AnswerNotCorrect'));
					}
				}

				//--Get the error
				$row['fielderror'] = $this->frm->getField($row['fieldname'])->getErrors();
			}

			//--Check if the form is correct
			if($this->frm->isCorrect())
			{
				$status = "ok";
			}
			else
			{
				$status = "error";
			}


			//--Check if the user is logged in
			FrontendProfilesAuthentication::isLoggedIn();

			//--Get the user id
			$profileId = FrontendProfilesAuthentication::getProfile()->getId();

			//--Get the profile
			$profile = FrontendProfilesModel::get($profileId);

			$arrContestUser = array(
				"contest_id" => $this->data['id'],
				"user_id" => $profileId,
				"display_name" => $profile->getDisplayName(),
				"first_name" => $profile->getSetting("first_name") != "" ? $profile->getSetting("first_name") : $profile->getDisplayName(),
				"last_name" => $profile->getSetting("last_name"),
				"email" => $profile->getEmail(),
				"status" => $status,
			);


			//--Add contest-user to the database
			$contestUserId = FrontendContestModel::insertContestUser($arrContestUser);

			$sequence = 1;
			foreach($this->record['questions'] as &$row)
			{
				//--Check if the answer is correct
				$answerCorrect = $this->frm->getField($row['fieldname'])->getValue() == $row["answer"] ? true : false ;

				$arrContestUserAnswer = array(
					"contest_user_id" => $contestUserId,
					"answer_id" => $row['id'],
					"answer" => $this->frm->getField($row['fieldname'])->getValue(),
					"correct" => $answerCorrect === true ? "Y" : "N",
					"points" => $answerCorrect === true ? $row["points"] : 0,
					"sequence" => $sequence
				);

				//--Add the answer to the database
				FrontendContestModel::insertContestUserAnswer($arrContestUserAnswer);

				//--Count sequence
				$sequence ++;

			}

			//--If form is correct, redirect is possible.
			if($this->frm->isCorrect())
			{
				//--Redirect
				$redirect = SITE_URL . '/' . $this->URL->getQueryString();
				$redirect .= (stripos($redirect, '?') === false) ? '?' : '&';
				$redirect .= 'contest=done';

				// redirect with identifier
				SpoonHTTP::redirect($redirect);
			}
		}
	}

	/**
	 * Parse the widget
	 */
	protected function parse()
	{
		$this->tpl->assign('formName', $this->frm->getName());
		$this->tpl->assign('widgetContest', $this->record);
	}

	/**
	 * Parse the widget
	 */
	protected function parseSuccessMessage()
	{
		$this->tpl->assign('successMessage', $this->record['success_message'] != "" ? $this->record['success_message'] : FrontendLanguage::getMessage("ContestSuccess"));
	}

	/**
	 * Parse the profiles only page
 	 */
	protected function parseProfilesOnly()
	{
		$this->tpl->assign('profilesOnly', true);
	}

	/**
	 * Parse multiple participations page
	 */
	protected function parseMultipleParticipations()
	{
		$this->tpl->assign('multipleParticipations', true);
	}

	protected function checkMultipleParticipations()
	{

		if($this->record["multiple_participations"] == "N")
		{

			//--Check if someone is logged in.
			$loggedin = FrontendProfilesAuthentication::isLoggedIn();

			if($loggedin == true)
			{
				//--Get the user id
				$profileId = FrontendProfilesAuthentication::getProfile()->getId();

				$contestUser = array(
					"contest_id" => $this->data['id'],
					"user_id" => $profileId
				);

				if(FrontendContestModel::checkContestParticipation($contestUser) !== null)
				{
					return true;
				}
			}
		}

		return false;

	}
}
