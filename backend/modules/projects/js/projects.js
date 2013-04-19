/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Interaction for the projects module
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
jsBackend.projects =
{
	// constructor
	init: function()
	{
		// do meta
		if($('#title').length > 0) $('#title').doMeta();

        $filter = $('#filter');
        $filterCategory = $('#filter #category');

        $filterCategory.on('change', function(e)
        {
            $filter.submit();
        });
	}
}

$(jsBackend.projects.init);
