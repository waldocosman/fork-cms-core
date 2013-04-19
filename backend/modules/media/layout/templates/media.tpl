<div class="dataGridHolder">
    <div class="tableHeading">
        <div class="oneLiner">
            <h3 class="floater">{$lblImages|ucfirst}</h3>
            <div class="clearfix">&nbsp;</div>
            {$fileImages}
        </div>

        {option:mediaItems}
            <ul class="media">
                {iteration:mediaItems}
                    <li id="li-{$mediaItems.id}" style="float:left;margin:0 15px 15px 0;width:150px;">
                        <img src="{$mediaItems.image_128x128}" alt="{$mediaItems.filename}" title="{$mediaItems.filename}">
                        <label class="delete-image" for="image-{$mediaItems.id}">{$lblDeleteImage}</label> <input type="checkbox" name="image-{$mediaItems.id}" id="image-{$mediaItems.id}" />
                    </li>
                {/iteration:mediaItems}
            </ul>
        {/option:mediaItems}
    </div>
</div>