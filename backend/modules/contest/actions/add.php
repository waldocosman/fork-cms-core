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
class BackendContestAdd extends BackendBaseActionAdd
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
		$rbtHiddenValues[] = array('label' => BL::lbl('Hidden'),'value' => 'Y');
		$rbtHiddenValues[] = array('label' => BL::lbl('Published'),'value' => 'N');

		$rbtProfilesOnly[] = array('label' => BL::lbl('Yes'),'value' => 'Y');
		$rbtProfilesOnly[] = array('label' => BL::lbl('No'),'value' => 'N');

		$rbtMultipleParticipations[] = array('label' => BL::lbl('Yes'),'value' => 'Y');
		$rbtMultipleParticipations[] = array('label' => BL::lbl('No'),'value' => 'N');

		$this->frm = new BackendForm('add');
		$this->frm->addText('title', null, null, 'inputText title', 'inputTextError title');
		$this->frm->addEditor('text');
		$this->frm->addEditor('success_message');

		$this->frm->addRadiobutton('hidden', $rbtHiddenValues, 'N');
		$this->frm->addRadiobutton('profiles_only', $rbtProfilesOnly, 'Y');
		$this->frm->addRadiobutton('multiple_participations', $rbtMultipleParticipations, 'N');

		$this->meta = new BackendMeta($this->frm, null, 'title', true);
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
			$fields['title']->isFilled(BL::err('FieldIsRequired'));
			$fields['text']->isFilled(BL::err('FieldIsRequired'));

			$this->meta->validate();

			if($this->frm->isCorrect())
			{
				$item['meta_id'] = $this->meta->save();
				$item['title'] = $fields['title']->getValue();
				$item['text'] = $fields['text']->getValue();
				$item['success_message'] = $fields['success_message']->getValue();
				$item['language'] = BL::getWorkingLanguage();
				$item['hidden'] = $fields['hidden']->getValue();
				$item['profiles_only'] = $fields['profiles_only']->getValue();
				$item['multiple_participations'] = $fields['multiple_participations']->getValue();
				$item['user_id'] = BackendAuthentication::getUser()->getUserId();
				$item['sequence'] = BackendContestModel::getMaximumSequence() + 1;

				$item['id'] = BackendContestModel::insert($item);

				BackendSearchModel::saveIndex(
					$this->getModule(),
					$item['id'],
					array('title' => $item['title'], 'text' => $item['title'])
				);

				BackendModel::triggerEvent(
					$this->getModule(), 'after_add', $item
				);
				$this->redirect(
					BackendModel::createURLForAction('index') . '&report=added&highlight=row-' . $item['id']
				);
			}
		}
	}
}
