/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Interaction for the utilities module
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
jsFrontend.utilities =
{
	// constructor
	init: function()
	{
        jsFrontend.utilities.changeFontSize();
	},
    changeFontSize: function()
    {

        $("#fontSizeSmall").click(function()
            {
                $(jsFrontend.data.get('utilities.settings.classname_container')).css("fontSize", jsFrontend.data.get('utilities.settings.fontsize_small') + "px");
                jsFrontend.utilities.saveFontSize(jsFrontend.data.get('utilities.settings.fontsize_small'));
                return false;
            }
        );

        $("#fontSizeLarge").click(function()
            {
                $(jsFrontend.data.get('utilities.settings.classname_container')).css("fontSize", jsFrontend.data.get('utilities.settings.fontsize_large') + "px");
                jsFrontend.utilities.saveFontSize(jsFrontend.data.get('utilities.settings.fontsize_large'));

                return false;
            }
        );
    },

    //--Save fontSize with Ajax
    saveFontSize: function(intFontSize)
    {
        //--Execute Ajax-call
        $.ajax({
            url: "/frontend/ajax.php",
            type: "POST",
            data:
            {
                fork: {module: 'utilities', action: 'fontsize'},
                fontsize: intFontSize
            }
        });

    }
}

$(jsFrontend.utilities.init);
