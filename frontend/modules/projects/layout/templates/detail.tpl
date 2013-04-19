{option:item}
<article>
    <header>
        <h1>{$item.title}</h1>
    </header>

    {$item.text}

    {option:item.image}
        <a href="{$item.image_source}" title="{$lblProject} {$item.title}">
            <img src="{$item.image_128x128}" alt="{$lblProject} {$item.title}"/>
        </a>
    {/option:item.image}

    <footer>
        <ul class="unstyled">
            {option:navigation.previous}
                <li>
                    <a href="{$navigation.previous.url}" rel="prev">{$lblPreviousProject|ucfirst}: {$navigation.previous.title}</a>
                </li>
            {/option:navigation.previous}
            {option:navigation.next}
                <li>
                    <a href="{$navigation.next.url}" rel="next">{$lblNextProject|ucfirst}: {$navigation.next.title}</a>
                </li>
            {/option:navigation.next}
        </ul>
        <a href="{$item.category_full_url}" title="{$lblMoreProjectsAbout} {$item.category_title|lowercase}">{$lblMoreProjectsAbout} {$item.category_title|lowercase}</a>
    </footer>
</article>
{/option:item}