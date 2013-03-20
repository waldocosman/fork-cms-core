/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Interaction for the instagram module
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
jsFrontend.instagram =
{

    //--Container
    container: null,

    //--Flag to see if there is an ajax-call busy
    ajax_busy: false,

    //--Max id, used in the api-call
    user_media_recent_max_id: '',

    //--The number of images to get
    user_media_recent_count: 10,

    //--Flag to see if there are no more images left
    is_ended: false,

    // constructor
    init: function ()
    {

        jsFrontend.instagram.user_media_recent_count = jsFrontend.data.get('instagram.settings.user_media_recent_count');

        //--Get recent media
        $(jsFrontend.instagram.user_media_recent);

        $(window).on('scroll', jsFrontend.instagram.user_media_recent_check_last_visible);

    },

    user_media_recent: function ()
    {
        $("div.instagram_user_media_recent").each(function (i, e)
        {
            //--Get container element
            jsFrontend.instagram.container = $(this);

            //--Call ajax-function
            $(jsFrontend.instagram.user_media_recent_ajax());
        });

        $("div.instagram_user_media_recent a.more-images").click(function ()
            {
                //--Call ajax-function
                $(jsFrontend.instagram.user_media_recent_ajax());

                return false;
            }
        );
    },

    user_media_recent_ajax: function ()
    {
        //--Set busy-flag to true
        jsFrontend.instagram.ajax_busy = true;

        //--Show loading
        $(jsFrontend.instagram.container).find("span.loading").show();

        //--ajax call!
        $.ajax(
            {
                data: {
                    fork: { module: 'instagram', action: 'user_media_recent' },
                    max_id: jsFrontend.instagram.user_media_recent_max_id
                },
                success: function (data, textStatus)
                {

                    //--Set ajax-flag to false
                    jsFrontend.instagram.ajax_busy = false;


                    // alert the user
                    if (data.code != 200 && jsFrontend.debug)
                    {
                        alert(data.message);
                    }

                    //--Hide the loading span
                    $(jsFrontend.instagram.container).find("span.loading").hide();

                    var counter = 0;

                    //--Loop the data
                    $(data.data).each(function (i, e)
                    {
                        //--Add counter
                        counter++;

                        //--Create li-element
                        var li = $("<li></li>");

                        //--Create a-element
                        var a = $('<a href="' + e.link + '" style="display:none;" title="' + e.caption.text + '" target="_blank"></a>');

                        //--Create img-element
                        var img = $('<img src="' + e.images.thumbnail.url + '" alt="' + e.caption.text + '" title="' + e.caption.text + '"/>');

                        //--Append image
                        $(img).appendTo(a);

                        //--Append li to a
                        $(a).appendTo(li);

                        //--Append li to ul
                        $(li).appendTo($(jsFrontend.instagram.container).children("ul"));

                        //--Fade in a-element
                        $(a).fadeIn();

                        //--Add count
                        jsFrontend.instagram.user_media_recent_max_id = e.id;
                    })

                    //--Check if there are more images left
                    if (counter < jsFrontend.instagram.user_media_recent_count)
                    {
                        //--Set is_ended flag
                        jsFrontend.instagram.is_ended = true;

                        //--Hide more images
                        $("div.instagram_user_media_recent a.more-images").hide();
                    }

                    //--Check if the last image is still visible (large screens)
                    jsFrontend.instagram.user_media_recent_check_last_visible();
                }
            });
    },

    user_media_recent_check_last_visible: function ()
    {
        //--Get last image in container
        var $lastLi = $("div.instagram_user_media_recent ul li:last");

        var $liTop = $lastLi.position().top;
        var $liHeight = $lastLi.height();
        var $documentHeight = $(document).height();
        var $documentTop = $(document).scrollTop();
        var $windowHeight = $(window).height();


        //--Check the position of the scroll
        if ($windowHeight >= $documentHeight || ($liTop + $liHeight) < ($documentTop + $windowHeight))
        {
            //--Check if there is an ajax-call busy or if the last image is already visible.
            if (jsFrontend.instagram.ajax_busy == false && jsFrontend.instagram.is_ended == false)
            {
                //--Call ajax-function
                $(jsFrontend.instagram.user_media_recent_ajax());

            }
        }
    }
}

$(jsFrontend.instagram.init);

