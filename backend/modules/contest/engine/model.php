<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the contest module
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class BackendContestModel
{


	const QRY_DATAGRID_BROWSE_CONTESTS =
		'SELECT i.id, i.title,  count(cq.id) as count_questions
		 FROM contest AS i
		 LEFT JOIN contest_question AS cq ON cq.contest_id = i.id
		 WHERE i.language = ?
		 GROUP BY i.id
		 ORDER BY i.sequence ASC';

	const QRY_DATAGRID_BROWSE_QUESTIONS =
		'SELECT i.id, i.question as questions, i.sequence
		 FROM contest_question AS i
		 WHERE i.language = ? AND i.contest_id = ?
		 GROUP BY i.id
		 ORDER BY i.sequence ASC';
	/**
	 * Delete a certain item
	 *
	 * @param int $id
	 */
	public static function delete($id)
	{
	//--Delete contest answer
		BackendModel::getDB(true)->execute("	DELETE cu.*, cua.* FROM contest_user AS cu
													INNER JOIN contest_user_answer AS cua ON cua.contest_user_id = cu.id
												WHERE cu.contest_id = ?", (int) $id);

		//--Delete questions
		BackendModel::getDB(true)->delete('contest_question', 'contest_id = ?', (int) $id);

		BackendModel::getDB(true)->delete('contest', 'id = ?', (int) $id);


	}

	/**
	 * Checks if a certain item exists
	 *
	 * @param int $id
	 * @return bool
	 */
	public static function exists($id)
	{
		return (bool) BackendModel::getDB()->getVar(
			'SELECT 1
			 FROM contest AS i
			 WHERE i.id = ?
			 LIMIT 1',
			array((int) $id)
		);
	}

	/**
	 * Fetches a certain item
	 *
	 * @param int $id
	 * @return array
	 */
	public static function get($id)
	{
		return (array) BackendModel::getDB()->getRecord(
			'SELECT i.*, m.url
			 FROM contest AS i
 			 INNER JOIN meta AS m ON m.id = i.meta_id
			 WHERE i.id = ?',
			array((int) $id)
		);
	}

	/**
	 * Retrieve the unique url for an item
	 *
	 * @param string $url
	 * @param int[optional] $id
	 * @return string
	 */
	public static function getUrl($url, $id = null)
	{
		// redefine Url
		$url = SpoonFilter::urlise((string) $url);

		// get db
		$db = BackendModel::getDB();

		// new item
		if($id === null)
		{
			$numberOfItems = (int) $db->getVar(
				'SELECT 1
				 FROM contest AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ?
				 LIMIT 1',
				array(BL::getWorkingLanguage(), $url));

			// already exists
			if($numberOfItems != 0)
			{
				// add number
				$url = BackendModel::addNumber($url);

				// try again
				return self::getUrl($url);
			}
		}
		// current item should be excluded
		else
		{
			$numberOfItems = (int) $db->getVar(
				'SELECT 1
				 FROM contest AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ? AND i.id != ?
				 LIMIT 1',
				array(BL::getWorkingLanguage(), $url, $id));

			// already exists
			if($numberOfItems != 0)
			{
				// add number
				$url = BackendModel::addNumber($url);

				// try again
				return self::getUrl($url, $id);
			}
		}

		// return the unique Url!
		return $url;
	}

	/**
	 * Insert an item in the database
	 *
	 * @param array $data
	 * @return int
	 */
	public static function insert(array $data)
	{
		$data['created_on'] = BackendModel::getUTCDate();

		 $insertId = (int) BackendModel::getDB(true)->insert('contest', $data);


		// build array
		$extra['module'] = 'contest';
		$extra['type'] = 'widget';
		$extra['label'] = 'Contest';
		$extra['action'] = 'contest';
		$extra['data'] = serialize(array('language' => $data['language'], 'extra_label' => BackendLanguage::getLabel("Contest") . " " . $data['title'], 'id' => $insertId, 'edit_url' => BackendModel::createURLForAction('edit') . '&id=' . $insertId));
		$extra['hidden'] = 'N';
		$extra['sequence'] = '500' . $insertId;

		// insert extra
		BackendModel::getDB(true)->insert('modules_extras', $extra);

		return $insertId;

	}

	/**
	 * Updates an item
	 *
	 * @param int $id
	 * @param array $data
	 */
	public static function update($id, array $data)
	{
		$data['edited_on'] = BackendModel::getUTCDate();

		BackendModel::getDB(true)->update(
			'contest', $data, 'id = ?', (int) $id
		);
	}

	/**
	 * Insert an item in the database
	 *
	 * @param array $data
	 * @return int
	 */
	public static function insertQuestion(array $data)
	{
		$data['created_on'] = BackendModel::getUTCDate();

		return (int) BackendModel::getDB(true)->insert('contest_question', $data);
	}

	/**
	 * Updates an item
	 *
	 * @param int $id
	 * @param array $data
	 */
	public static function updateQuestion($id, array $data)
	{
		$data['edited_on'] = BackendModel::getUTCDate();

		BackendModel::getDB(true)->update(
			'contest_question', $data, 'id = ?', (int) $id
		);
	}

	/**
	 * Checks if a certain item exists
	 *
	 * @param int $id
	 * @return bool
	 */
	public static function existsQuestion($id)
	{
		return (bool) BackendModel::getDB()->getVar(
			'SELECT 1
			 FROM contest_question AS i
			 WHERE i.id = ?
			 LIMIT 1',
			array((int) $id)
		);
	}

	/**
	 * Fetches a certain item
	 *
	 * @param int $id
	 * @return array
	 */
	public static function getQuestion($id)
	{
		return (array) BackendModel::getDB()->getRecord(
			'SELECT i.*, m.url
			 FROM contest_question AS i
 			 INNER JOIN meta AS m ON m.id = i.meta_id
			 WHERE i.id = ?',
			array((int) $id)
		);
	}

	/**
	 * Delete a certain item
	 *
	 * @param int $id
	 */
	public static function deleteQuestion($id)
	{
		BackendModel::getDB(true)->delete('contest_question', 'id = ?', (int) $id);
	}

	/**
	 * Get the max sequence id for a contest
	 *
	 * @param int $id		The category id.
	 * @return int
	 */
	public static function getMaximumSequence()
	{
		return (int) BackendModel::getDB()->getVar(
			'SELECT MAX(i.sequence)
			 FROM contest AS i',
			array());
	}

	/**
	 * Get the max sequence id for a question
	 *
	 * @param int $id		The category id.
	 * @return int
	 */
	public static function getMaximumSequenceQuestion($contest_id)
	{
		return (int) BackendModel::getDB()->getVar(
			'SELECT MAX(i.sequence)
			 FROM contest_question AS i
			 WHERE i.contest_id = ?',
			array((int) $contest_id));
	}
}
