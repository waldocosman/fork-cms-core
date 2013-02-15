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
class BackendContestAddQuestion extends BackendBaseActionAdd
{

	private $contest_id;
	/**
	 * Execute the actions
	 */
	public function execute()
	{
		parent::execute();

		$this->contest_id = $this->getParameter('contest_id', 'int', null);

		//--Get contest from id
		$contest = BackendContestModel::get($this->contest_id);

		//--Check if contest exists
		if(empty($contest))
		{
			//--Redirect if content doesn't exist.
			$this->redirect(
				BackendModel::createURLForAction('index') . '&error=non-existing'
			);
		}

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
		$rbtHiddenValues[] = array('label' => BL::lbl('Hidden'),'value' => 'Y');
		$rbtHiddenValues[] = array('label' => BL::lbl('Published'),'value' => 'N');

		$rbtMustBeCorrect[] = array('label' => BL::lbl('Yes'),'value' => 'Y');
		$rbtMustBeCorrect[] = array('label' => BL::lbl('No'),'value' => 'N');

		$rbtRequired[] = array('label' => BL::lbl('Yes'),'value' => 'Y');
		$rbtRequired[] = array('label' => BL::lbl('No'),'value' => 'N');

		$this->frm = new BackendForm('add');
		$this->frm->addText('question', null, null, 'inputText title', 'inputTextError title');
		$this->frm->addText('answer', null, null, 'inputText', 'inputTextError');
		$this->frm->addText('points', null, null, 'inputText', 'inputTextError');
		$this->frm->addRadiobutton('hidden', $rbtHiddenValues, 'N');
		$this->frm->addRadiobutton('must_be_correct', $rbtMustBeCorrect, 'N');
		$this->frm->addRadiobutton('required', $rbtRequired, 'Y');

		$this->meta = new BackendMeta($this->frm, null, 'question', true);
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		parent::parse();

		// assign the url for the detail page
		$url = BackendModel::getURLForBlock($this->URL->getModule(), 'detail');
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
			$fields['question']->isFilled(BL::err('FieldIsRequired'));
			$fields['answer']->isFilled(BL::err('FieldIsRequired'));
			$this->meta->validate();

			if($this->frm->isCorrect())
			{
				$item['meta_id'] = $this->meta->save();
				$item['question'] = $fields['question']->getValue();
				$item['answer'] = $fields['answer']->getValue();
				$item['points'] = $fields['points']->getValue();
				$item['language'] = BL::getWorkingLanguage();
				$item['hidden'] = $fields['hidden']->getValue();
				$item['must_be_correct'] = $fields['must_be_correct']->getValue();
				$item['required'] = $fields['required']->getValue();
				$item['contest_id'] = $this->contest_id;
				$item['sequence'] = BackendContestModel::getMaximumSequenceQuestion($this->contest_id) + 1;

				$item['id'] = BackendContestModel::insertQuestion($item);

				BackendSearchModel::saveIndex(
					$this->getModule(),
					$item['id'],
					array('title' => $item['question'], 'text' => $item['question'])
				);

				BackendModel::triggerEvent(
					$this->getModule(), 'after_add_question', $item
				);
				$this->redirect(
					BackendModel::createURLForAction('edit') . '&amp;id=' . $this->contest_id .'&report=added&highlight=row-' . $item['id'] . '#tabQuestions'
				);
			}
		}
	}
}
