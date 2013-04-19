<h1>{$lblProject} {$category.label}</h1>
{option:items}
    <ul class="unstyled">
        {iteration:items}
            <li>
                <a href="{$items.full_url}">{$items.title}</a>
                {option:items.image}
                    <a href="{$items.full_url}" title="{$lblProject} {$items.title}">
                        <img src="{$items.image_128x128}" alt="{$lblProject} {$items.title}"/>
                    </a>
                {/option:items.image}
            </li>
        {/iteration:items}
    </ul>
{/option:items}