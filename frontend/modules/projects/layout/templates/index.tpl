{*{$items|dump}*}
{option:!items}
    <div id="projectsIndex">
        <section class="mod">
            <div class="inner">
                <div class="bd content">
                    <p>{$msgNoItems}</p>
                </div>
            </div>
        </section>
    </div>
{/option:!items}

{option:items}
    <ul class="unstyled">
        {iteration:items}
            <li>
                <h3><a href="{$items.category_full_url}" title="{$items.label}">{$items.label}</a></h3>
                {option:items.image}
                <a href="{$items.category_full_url}" title="{$items.label}">
                    <img src="{$items.image_128x128}" alt="{$items.label}"/>
                </a>
                {/option:items.image}
            </li>

        {/iteration:items}
    </ul>
{/option:items}