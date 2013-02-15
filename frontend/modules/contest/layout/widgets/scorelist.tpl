{*
	variables that are available:
	- {$widgetScorelist}: contains all the data for this widget
*}

{option:widgetScorelist}
<table>
    <tr>
        <th>{$lblDisplayName|ucfirst}</th>
        <th>{$lblContestParticipated|ucfirst}</th>
        <th>{$lblPoints|ucfirst}</th>
    </tr>


    {iteration:widgetScorelist}

    <tr>
        <td>{$widgetScorelist.display_name}</td>
        <td>{$widgetScorelist.total_contests}</td>
        <td>{$widgetScorelist.total_points}</td>
    </tr>

    {/iteration:widgetScorelist}

</table>
{/option:widgetScorelist}

{option:!widgetScorelist}
<p class="text-info">{$msgNoScores}</p>
{/option:!widgetScorelist}
