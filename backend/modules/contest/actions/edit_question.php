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
class BackendContestEditQuestion extends BackendBaseActionEdit
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
		if($this->id == null || !BackendContestModel::existsQuestion($this->id))
		{
			$this->redirect(
				BackendModel::createURLForAction('index') . '&error=non-existing'
			);
		}

		$this->record = BackendContestModel::getQuestion($this->id);
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

		// create form
		$this->frm = new BackendForm('edit');
		$this->frm->addText('question',$this->record['question'], null, 'inputText title', 'inputTextError title');
		$this->frm->addText('answer', $this->record['answer'], null, 'inputText', 'inputTextError');
		$this->frm->addText('points', $this->record['points'], null, 'inputText', 'inputTextError');
		$this->frm->addRadiobutton('hidden', $rbtHiddenValues, $this->record['hidden']);
		$this->frm->addRadiobutton('must_be_correct', $rbtMustBeCorrect, $this->record['must_be_correct']);
		$this->frm->addRadiobutton('required', $rbtRequired, $this->record['required']);

		// meta
		$this->meta = new BackendMeta($this->frm, $this->record['meta_id'], 'question', true);
		$this->meta->setUrlCallback('BackendContestModel', 'getUrl',array($this->record['id']));
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		parent::parse();
		$this->tpl->assign('item', $this->record);

		// get url
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
				$item['meta_id'] = $this->meta->save(true);
				$item['question'] = $fields['question']->getValue();
				$item['answer'] = $fields['answer']->getValue();
				$item['points'] = $fields['points']->getValue();
				$item['language'] = BL::getWorkingLanguage();
				$item['hidden'] = $fields['hidden']->getValue();
				$item['must_be_correct'] = $fields['must_be_correct']->getValue();
				$item['required'] = $fields['required']->getValue();

				$item['contest_id'] = $this->record["contest_id"];

				BackendContestModel::updateQuestion($this->id, $item);
				$item['id'] = $this->id;

				BackendSearchModel::saveIndex(
					$this->getModule(),
					$item['id'],
					array('title' => $item['question'], 'text' => $item['question'])
				);

				BackendModel::triggerEvent(
					$this->getModule(), 'after_edit_question', $item
				);
				$this->redirect(
					BackendModel::createURLForAction('edit') . '&amp;id=' . $item["contest_id"] .'&report=edited&highlight=row-' . $item['id'] . '#tabQuestions'
				);
			}
		}
	}
}
