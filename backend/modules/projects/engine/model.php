<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the projects module
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class BackendProjectsModel
{

	const QRY_DATAGRID_BROWSE_PROJECTS_CATEGORIES = 'SELECT i.id, i.title, i.sequence
		 FROM projects_categories AS i
		 WHERE i.language = ?
		 ORDER BY i.sequence ASC';

	const QRY_DATAGRID_BROWSE_PROJECTS = 'SELECT i.id, i.title, i.sequence
		 FROM projects AS i
		 WHERE i.language = ?
		 ORDER BY i.sequence ASC';

	const QRY_DATAGRID_BROWSE_PROJECTS_FOR_CATEGORY = 'SELECT i.id, i.title, i.sequence
		 FROM projects AS i
		 WHERE i.category_id = ? AND i.language = ?
		 ORDER BY i.sequence ASC';

	/**
	 * Delete a certain item
	 *de
	 *
	 * @param int $id
	 */
	public static function delete($id)
	{
		BackendModel::getDB(true)->delete('projects', 'id = ?', (int)$id);
	}

	/**
	 * Delete a certain category
	 *de
	 *
	 * @param int $id
	 */
	public static function deleteCategory($id)
	{
		BackendModel::getDB(true)->delete('projects_categories', 'id = ?', (int)$id);
	}

	/**
	 * Checks if a certain item exists
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	public static function exists($id)
	{
		return (bool)BackendModel::getDB()->getVar('SELECT 1
			 FROM projects AS i
			 WHERE i.id = ?
			 LIMIT 1', array((int)$id));
	}

	/**
	 * Checks if a certain item exists
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	public static function existsCategory($id)
	{
		return (bool)BackendModel::getDB()->getVar('SELECT 1
			 FROM projects_categories AS i
			 WHERE i.id = ?
			 LIMIT 1', array((int)$id));
	}

	/**
	 * Fetches a certain item
	 *
	 * @param int $id
	 *
	 * @return array
	 */
	public static function get($id)
	{
		return (array)BackendModel::getDB()->getRecord('SELECT i.*, m.url
			 FROM projects AS i
			 INNER JOIN meta AS m ON m.id = i.meta_id
			 WHERE i.id = ?', array((int)$id));
	}

	/**
	 * Fetches a certain item
	 *
	 * @param int $id
	 *
	 * @return array
	 */
	public static function getCategory($id)
	{
		return (array)BackendModel::getDB()->getRecord('SELECT i.*, m.url
			 FROM projects_categories AS i
			 INNER JOIN meta AS m ON m.id = i.meta_id
			 WHERE i.id = ?', array((int)$id));
	}

	/**
	 * Get all categories
	 *
	 * @param bool[optional] $includeCount Include the count?
	 *
	 * @return array
	 */
	public static function getCategories($includeCount = false)
	{
		$db = BackendModel::getDB();

		if($includeCount)
		{
			return (array) $db->getPairs(
				'SELECT i.id, CONCAT(i.title, " (", COUNT(p.category_id) ,")") AS title
				 FROM projects_categories AS i
				 LEFT OUTER JOIN projects AS p ON i.id = p.category_id AND i.language = p.language
				 WHERE i.language = ?
				 GROUP BY i.id',
				array(BL::getWorkingLanguage())
			);
		}

		return (array)$db->getPairs('SELECT i.id, i.title
			 FROM projects_categories AS i
			 WHERE i.language = ?', array(BL::getWorkingLanguage()));
	}

	/**
	 * Retrieve the unique url for an item
	 *
	 * @param string $url
	 * @param int[optional] $id
	 *
	 * @return string
	 */
	public static function getUrl($url, $id = null)
	{
		// redefine Url
		$url = SpoonFilter::urlise((string)$url);

		// get db
		$db = BackendModel::getDB();

		// new item
		if($id === null)
		{
			$numberOfItems = (int)$db->getVar('SELECT 1
				 FROM projects AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ?
				 LIMIT 1', array(BL::getWorkingLanguage(), $url));

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
			$numberOfItems = (int)$db->getVar('SELECT 1
				 FROM projects AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ? AND i.id != ?
				 LIMIT 1', array(BL::getWorkingLanguage(), $url, $id));

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
	 * Retrieve the unique url for an item
	 *
	 * @param string $url
	 * @param int[optional] $id
	 *
	 * @return string
	 */
	public static function getUrlForCategory($url, $id = null)
	{
		// redefine Url
		$url = SpoonFilter::urlise((string)$url);

		// get db
		$db = BackendModel::getDB();

		// new item
		if($id === null)
		{
			$numberOfItems = (int)$db->getVar('SELECT 1
				 FROM projects_categories AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ?
				 LIMIT 1', array(BL::getWorkingLanguage(), $url));

			// already exists
			if($numberOfItems != 0)
			{
				// add number
				$url = BackendModel::addNumber($url);

				// try again
				return self::getUrlForCategory($url);
			}
		}
		// current item should be excluded
		else
		{
			$numberOfItems = (int)$db->getVar('SELECT 1
				 FROM projects_categories AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ? AND i.id != ?
				 LIMIT 1', array(BL::getWorkingLanguage(), $url, $id));

			// already exists
			if($numberOfItems != 0)
			{
				// add number
				$url = BackendModel::addNumber($url);

				// try again
				return self::getUrlForCategory($url, $id);
			}
		}

		// return the unique Url!
		return $url;
	}

	/**
	 * Insert an item in the database
	 *
	 * @param array $data
	 *
	 * @return int
	 */
	public static function insert(array $data)
	{
		$data['created_on'] = BackendModel::getUTCDate();

		return (int)BackendModel::getDB(true)->insert('projects', $data);
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

		BackendModel::getDB(true)->update('projects', $data, 'id = ?', (int)$id);
	}

	/**
	 * Insert an item in the database
	 *
	 * @param array $data
	 *
	 * @return int
	 */
	public static function insertCategory(array $data)
	{
		$data['created_on'] = BackendModel::getUTCDate();

		return (int)BackendModel::getDB(true)->insert('projects_categories', $data);
	}

	/**
	 * Updates an category
	 *
	 * @param int $id
	 * @param array $data
	 */
	public static function updateCategory($id, array $data)
	{
		$data['edited_on'] = BackendModel::getUTCDate();

		BackendModel::getDB(true)->update('projects_categories', $data, 'id = ?', (int)$id);
	}

	/**
	 * Get the maximum sequence for a category
	 *
	 * @return int
	 */
	public static function getMaximumCategorySequence()
	{
		return (int)BackendModel::getDB()->getVar('SELECT MAX(i.sequence)
			 FROM projects_categories AS i
			 WHERE i.language = ? ', array(BL::getWorkingLanguage()));
	}

	/**
	 * Get the maximum sequence for an item
	 *
	 * @param int category_id
	 *
	 * @return int
	 */
	public static function getMaximumSequence($id)
	{
		return (int)BackendModel::getDB()->getVar('SELECT MAX(i.sequence)
			 FROM projects AS i
			 WHERE i.language = ? AND category_id = ?', array(BL::getWorkingLanguage(), $id));
	}
}
