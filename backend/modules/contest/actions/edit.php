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
class BackendContestEdit extends BackendBaseActionEdit
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
		if($this->id == null || !BackendContestModel::exists($this->id))
		{
			$this->redirect(
				BackendModel::createURLForAction('index') . '&error=non-existing'
			);
		}

		$this->dataGrid = new BackendDataGridDB(BackendContestModel::QRY_DATAGRID_BROWSE_QUESTIONS, array(BL::getWorkingLanguage(), $this->id));

		$this->dataGrid->enableSequenceByDragAndDrop();


		// check if this action is allowed
		if(BackendAuthentication::isAllowedAction('edit_question'))
		{

			$this->dataGrid->setColumnURL('questions', BackendModel::createURLForAction('edit_question') . '&amp;id=[id]');


			$this->dataGrid->addColumn('edit', null, BL::lbl('Edit'),BackendModel::createURLForAction('edit_question') . '&amp;id=[id]',BL::lbl('Edit')
			);
		}

		$this->record = BackendContestModel::get($this->id);

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

		// create form
		$this->frm = new BackendForm('edit');
		$this->frm->addText('title', $this->record['title'], null,'inputText title', 'inputTextError title');
		$this->frm->addEditor('text', $this->record['text']);
		$this->frm->addEditor('success_message', $this->record['success_message']);
		$this->frm->addRadiobutton('hidden', $rbtHiddenValues, $this->record['hidden']);
		$this->frm->addRadiobutton('profiles_only', $rbtProfilesOnly, $this->record['profiles_only']);
		$this->frm->addRadiobutton('multiple_participations', $rbtMultipleParticipations, $this->record['multiple_participations']);

		// meta
		$this->meta = new BackendMeta($this->frm, $this->record['meta_id'], 'title', true);
		$this->meta->setUrlCallback('BackendContestModel', 'getUrl',array($this->record['id']));
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		parent::parse();

		$this->tpl->assign('item', $this->record);

		$this->tpl->assign(
			'dataGrid',
			($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false
		);

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
			$fields['title']->isFilled(BL::err('FieldIsRequired'));
			$this->meta->validate();

			if($this->frm->isCorrect())
			{
				$item['meta_id'] = $this->meta->save(true);
				$item['title'] = $fields['title']->getValue();
				$item['text'] = $fields['text']->getValue();
				$item['success_message'] = $fields['success_message']->getValue();
				$item['language'] = BL::getWorkingLanguage();
				$item['hidden'] = $fields['hidden']->getValue();
				$item['user_id'] = BackendAuthentication::getUser()->getUserId();
				$item['profiles_only'] = $fields['profiles_only']->getValue();
				$item['multiple_participations'] = $fields['multiple_participations']->getValue();

				BackendContestModel::update($this->id, $item);
				$item['id'] = $this->id;

				BackendSearchModel::saveIndex($this->getModule(),$item['id'],array('title' => $item['title'], 'text' => $item['title']));

				BackendModel::triggerEvent($this->getModule(), 'after_edit', $item);
				$this->redirect(BackendModel::createURLForAction('index') . '&report=edited&highlight=row-' . $item['id']);
			}
		}
	}
}
