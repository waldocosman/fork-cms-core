{$items|dump}

{option:!items}
	<div id="instagramIndex">
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
	<div id="instagramIndex">
		{iteration:items}
			<article class="mod">
				<div class="inner">
					<header class="hd">
						<h3><a href="{$items.full_url}" title="{$items.title}">{$items.title}</a></h3>
					</header>
					<div class="bd content">
						{$items.text}
					</div>
				</div>
			</article>
		{/iteration:items}
	</div>
	{include:core/layout/templates/pagination.tpl}
{/option:items}