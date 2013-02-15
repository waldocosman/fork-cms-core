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
class FrontendContestModel
{

	/**
	 *
	 * Get all the contests
	 *
	 * @param null $parent
	 * @return mixed
	 */

	public static function getAllContests()
	{

		$return = FrontendModel::getDB()->getRecords("
				SELECT i.*, m.url from contest AS i
					INNER JOIN meta AS m ON m.id = i.meta_id
				WHERE i.language=? AND i.parent = ?", array(FRONTEND_LANGUAGE), 'id');


		// loop items and unserialize
		foreach($return as &$row)
		{
			if(isset($row['meta_data'])) $row['meta_data'] = @unserialize($row['meta_data']);
		}

		return $return;

	}

	/**
	 *
	 * Get all the questions for a contest
	 *
	 * @param null $parent
	 * @return mixed
	 */
	public static function getAllForContest($contestId, $limit = null)
	{
		$contestId = (int) $contestId;
		$limit = (int) $limit;

		// get items
		if($limit != null) $items = (array) FrontendModel::getDB()->getRecords(
			'SELECT i.*, m.url
			 FROM contest_question AS i
			 INNER JOIN meta AS m ON i.meta_id = m.id
			 WHERE i.contest_id = ? AND i.language = ? AND i.hidden = ?
			 ORDER BY i.sequence
			 LIMIT ?',
			array((int) $contestId, FRONTEND_LANGUAGE, 'N', (int) $limit)
		);

		else $items = (array) FrontendModel::getDB()->getRecords(
			'SELECT i.*, m.url
			 FROM contest_question AS i
			 INNER JOIN meta AS m ON i.meta_id = m.id
			 WHERE i.contest_id = ? AND i.language = ? AND i.hidden = ?
			 ORDER BY i.sequence',
			array((int) $contestId, FRONTEND_LANGUAGE, 'N')
		);

		// init var
		$link = FrontendNavigation::getURLForBlock('contest', 'detail');

		// build the item urls
		foreach($items as &$item) $item['full_url'] = $link . '/' . $item['url'];

		return $items;

	}

	/**
	 *
	 * Get all the contests
	 *
	 * @param null $parent
	 * @return mixed
	 */

	public static function get($contestId)
	{

		$return = FrontendModel::getDB()->getRecords("
				SELECT i.*, m.url from contest AS i
					INNER JOIN meta AS m ON m.id = i.meta_id
				WHERE i.language=? AND i.id = ?", array(FRONTEND_LANGUAGE, $contestId), 'id');

		// loop items and unserialize
		foreach($return as &$row)
		{
			if(isset($row['meta_data'])) $row['meta_data'] = @unserialize($row['meta_data']);

			//--Get questions for the contest
			$row["questions"] = FrontendContestModel::getAllForContest($contestId);
			$row["multiple_questions"] = $row["questions"] > 1 ? true : false;
		}

		return $return;

	}

	/**
	 * Add the user to the database
	 * @param $contestUser
	 */
	public static function insertContestUser(array $contestUser)
	{

		// get db
		$db = FrontendModel::getDB(true);

		//--Add the date
		$contestUser["create_date"] = FrontendModel::getUTCDate();

		// insert comment
		$contestUser['id'] = (int) $db->insert('contest_user', $contestUser);

		return $contestUser['id'];

	}

	/**
	 * Add the answers of the user to the database
	 * @param $contestUserAnswer
	 */
	public static function insertContestUserAnswer(array $contestUserAnswer)
	{

		// get db
		$db = FrontendModel::getDB(true);

		// insert comment
		$contestUserAnswer['id'] = (int) $db->insert('contest_user_answer', $contestUserAnswer);

		return $contestUserAnswer['id'];

	}

	public static function checkContestParticipation($contestUser)
	{
		// get db
		$db = FrontendModel::getDB(true);

		if(isset($contestUser["email"]))
		{
			return $db->getRecord("SELECT id FROM contest_user WHERE contest_id = ? AND email = '?' AND status='ok'", array($contestUser["contest_id"], $contestUser['email']));
		}
		elseif(isset($contestUser["user_id"]))
		{
			return $db->getRecord("SELECT id FROM contest_user WHERE contest_id = ? AND user_id = ? AND status='ok'", array($contestUser["contest_id"], $contestUser['user_id']));
		}
	}

	/**
	 * Get scorelist from all the games
	 */
	public static function getScorelist()
	{
		// get db
		$db = FrontendModel::getDB(true);

		return $db->getRecords("SELECT * FROM
											(
												SELECT p.display_name, COUNT(contest_id) as total_contests, SUM(points) AS total_points FROM contest_user AS cu
													LEFT JOIN contest_user_answer AS cua ON cua.contest_user_id = cu.id
													INNER JOIN profiles AS p ON p.id = cu.user_id
												WHERE cu.status = 'ok' AND cua.correct = 'Y' AND cu.user_id > 0
												GROUP BY cu.user_id
											UNION
												SELECT display_name, COUNT(contest_id) as total_contests, SUM(points) AS total_points FROM contest_user AS cu
													LEFT JOIN contest_user_answer AS cua ON cua.contest_user_id = cu.id
												WHERE cu.status = 'ok' AND cua.correct = 'Y' AND cu.user_id = 0
												GROUP BY display_name
											) a
									ORDER BY total_points DESC, total_contests ASC");
	}

}
