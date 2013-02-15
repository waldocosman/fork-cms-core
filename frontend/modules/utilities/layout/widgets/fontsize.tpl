{*
	variables that are available:
	- {$widgetFontsize}: contains all the data for this widget
*}

<ul class="unstyled pull-right nav-fontsize">
    <li><a href="#" id="fontSizeSmall">a</a>-</li>
    <li><a href="#" id="fontSizeLarge">A</a></li>
</ul>

<script type="text/javascript">
    window.onload = function()
    {
        //--Fontsize aanpassen
        $(jsFrontend.data.get('utilities.settings.classname_container')).css("fontSize", {$widgetFontsize.fontsize} + "px");
    }

</script>