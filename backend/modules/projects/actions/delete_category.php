<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the delete-action, it deletes an item
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class BackendProjectsDeleteCategory extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id !== null && BackendProjectsModel::existsCategory($this->id))
		{
			parent::execute();
			$this->record = (array)BackendProjectsModel::getCategory($this->id);

			BackendProjectsModel::deleteCategory($this->id);

			BackendModel::triggerEvent($this->getModule(), 'after_delete_category', array('id' => $this->id));

			$this->redirect(BackendModel::createURLForAction('categories') . '&report=deleted&var=' . urlencode($this->record['title']));
		}
		else
		{
			$this->redirect(BackendModel::createURLForAction('categories') . '&error=non-existing');
		}
	}
}
