<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the elete-action, it deletes an item
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class BackendContestDeleteQuestion extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id !== null && BackendContestModel::existsQuestion($this->id))
		{
			parent::execute();
			$this->record = (array) BackendContestModel::getQuestion($this->id);

			BackendContestModel::deleteQuestion($this->id);
			BackendSearchModel::removeIndex(
				$this->getModule(), $this->id
			);

			BackendModel::triggerEvent(
				$this->getModule(), 'after_delete_question',
				array('id' => $this->id)
			);

			$this->redirect(

				BackendModel::createURLForAction('edit') . '&id=' . $this->record["contest_id"] .'&report=deleted&var=' . urlencode(substr(strip_tags($this->record['question']), 0, 25)) . '#tabQuestions'
			);
		}
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}
}
