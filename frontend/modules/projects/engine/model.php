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
class FrontendProjectsModel
{
	/**
	 * Get all the categories
	 *
	 * @return array
	 */
	public static function getAllCategories()
	{
		$return = (array)FrontendModel::getDB()->getRecords('SELECT c.id, c.title AS label,c.image, m.url, COUNT(c.id) AS total, m.data AS meta_data
			 FROM projects_categories AS c
			 INNER JOIN projects AS i ON c.id = i.category_id AND c.language = i.language
			 INNER JOIN meta AS m ON c.meta_id = m.id
			 WHERE c.language = ? AND i.hidden = ?
			 GROUP BY c.id
			 ORDER BY c.sequence ASC', array(FRONTEND_LANGUAGE, 'N'), 'id');

		//--Get link for the categories
		$categoryLink = FrontendNavigation::getURLForBlock('projects', 'category');

		//--Get folders
		$folders = FrontendModel::getThumbnailFolders(FRONTEND_FILES_PATH . '/projects/images', true);

		// loop items and unserialize
		foreach($return as &$row)
		{
			$row['category_full_url'] = $categoryLink . '/' . $row['url'];

			if(isset($row['meta_data'])) $row['meta_data'] = @unserialize($row['meta_data']);

			// image?
			if(isset($row['image']))
			{
				foreach($folders as $folder)
				{
					$row['image_' . $folder['dirname']] = $folder['url'] . '/' . $row['image'];
				}
			}
		}

		return $return;
	}

	/**
	 * Get all items in a category (at least a chunk)
	 *
	 * @param string $categoryURL The URL of the category to retrieve the posts for.
	 * @param int[optional] $limit The number of items to get.
	 * @param int[optional] $offset The offset.
	 *
	 * @return array
	 */
	public static function getAllForCategory($categoryURL)
	{
		$items = (array)FrontendModel::getDB()->getRecords('SELECT i.id, i.language, i.title, i.introduction, i.text, c.title AS category_title, m2.url AS category_url, i.image,m.url
			 FROM projects AS i
			 INNER JOIN projects_categories AS c ON i.category_id = c.id
			 INNER JOIN meta AS m ON i.meta_id = m.id
			 INNER JOIN meta AS m2 ON c.meta_id = m2.id
			 WHERE  i.language = ? AND i.hidden = ? AND m2.url = ?
			 ORDER BY i.sequence ASC
			 ', array(FRONTEND_LANGUAGE, 'N', (string)$categoryURL), 'id');

		// no results?
		if(empty($items)) return array();

		// init var
		$link = FrontendNavigation::getURLForBlock('projects', 'detail');
		$categoryLink = FrontendNavigation::getURLForBlock('projects', 'category');
		$folders = FrontendModel::getThumbnailFolders(FRONTEND_FILES_PATH . '/projects/images', true);

		// loop
		foreach($items as $key => $row)
		{
			// URLs
			$items[$key]['full_url'] = $link . '/' . $row['url'];
			$items[$key]['category_full_url'] = $categoryLink . '/' . $row['category_url'];

			// image?
			if(isset($row['image']))
			{
				foreach($folders as $folder)
				{
					$items[$key]['image_' . $folder['dirname']] = $folder['url'] . '/' . $row['image'];
				}
			}
		}

		// return
		return $items;
	}

	/**
	 * Get an item
	 *
	 * @param string $URL The URL for the item.
	 *
	 * @return array
	 */
	public static function get($URL)
	{
		$return = (array)FrontendModel::getDB()->getRecord('SELECT i.id, i.language, i.title, i.introduction, i.text,
			 c.title AS category_title, m2.url AS category_url, i.image,
			 m.keywords AS meta_keywords, m.keywords_overwrite AS meta_keywords_overwrite,
			 m.description AS meta_description, m.description_overwrite AS meta_description_overwrite,
			 m.title AS meta_title, m.title_overwrite AS meta_title_overwrite,
			 m.url,
			 m.data AS meta_data
			 FROM projects AS i
			 INNER JOIN projects_categories AS c ON i.category_id = c.id
			 INNER JOIN meta AS m ON i.meta_id = m.id
			 INNER JOIN meta AS m2 ON c.meta_id = m2.id
			 WHERE i.language = ? AND i.hidden = ? AND m.url = ?
			 LIMIT 1', array( FRONTEND_LANGUAGE, 'N', (string)$URL));

		// unserialize
		if(isset($return['meta_data'])) $return['meta_data'] = @unserialize($return['meta_data']);

		// image?
		if(isset($return['image']))
		{
			$folders = FrontendModel::getThumbnailFolders(FRONTEND_FILES_PATH . '/projects/images', true);

			foreach($folders as $folder) {
				$return['image_' . $folder['dirname']] = $folder['url'] . '/' .  $return['image'];
			}
		}

		// return
		return $return;
	}

	/**
	 * Get an array with the previous and the next post
	 *
	 * @param int $id The id of the current item.
	 * @return array
	 */
	public static function getNavigation($id)
	{
		// redefine
		$id = (int) $id;

		// get db
		$db = FrontendModel::getDB();

		// get date for current item
		$sequence = (string) $db->getVar(
			'SELECT i.sequence
			 FROM projects AS i
			 WHERE i.id = ? AND i.hidden = ?',
			array($id, 'N')
		);

		$category_id= (string) $db->getVar(
			'SELECT i.category_id
			 FROM projects AS i
			 WHERE i.id = ? AND i.hidden = ?',
			array($id, 'N')
		);

		// validate
		if($sequence == '') return array();

		// init var
		$navigation = array();

		// get previous post
		$navigation['previous'] = $db->getRecord(
			'SELECT i.id, i.title, m.url
			 FROM projects AS i
			 INNER JOIN meta AS m ON i.meta_id = m.id
			 WHERE i.id != ? AND i.hidden = ? AND i.language = ? AND i.sequence <= ? AND i.category_id = ?
			 ORDER BY i.sequence DESC
			 LIMIT 1',
			array($id, 'N', FRONTEND_LANGUAGE, $sequence, $category_id)
		);

		// get next post
		$navigation['next'] = $db->getRecord(
			'SELECT i.id, i.title, m.url
			 FROM projects AS i
			 INNER JOIN meta AS m ON i.meta_id = m.id
			 WHERE i.id != ? AND i.hidden = ? AND i.language = ? AND i.sequence > ? AND i.category_id = ?
			 ORDER BY i.sequence ASC
			 LIMIT 1',
			array($id, 'N', FRONTEND_LANGUAGE, $sequence, $category_id)
		);

		return $navigation;
	}

	/**
	 * Parse the search results for this module
	 *
	 * Note: a module's search function should always:
	 * 		- accept an array of entry id's
	 * 		- return only the entries that are allowed to be displayed, with their array's index being the entry's id
	 *
	 *
	 * @param array $ids The ids of the found results.
	 * @return array
	 */
	public static function search(array $ids)
	{
		$items = (array) FrontendModel::getDB()->getRecords(
			'SELECT i.id, i.title, i.introduction, i.text, m.url
			 FROM projects AS i
			 INNER JOIN meta AS m ON i.meta_id = m.id
			 WHERE i.hidden = ? AND i.language = ? AND i.id IN (' . implode(',', $ids) . ')',
			array('N', FRONTEND_LANGUAGE), 'id'
		);

		// prepare items for search
		foreach($items as &$item)
		{
			$item['full_url'] = FrontendNavigation::getURLForBlock('projects', 'detail') . '/' . $item['url'];
		}

		// return
		return $items;
	}
}
