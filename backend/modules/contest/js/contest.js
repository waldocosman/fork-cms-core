/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Interaction for the contest module
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
jsBackend.contest =
{
	// constructor
	init: function()
	{

		// do meta
		if($('#title').length > 0) $('#title').doMeta();
	},
}

$(jsBackend.contest.init);
