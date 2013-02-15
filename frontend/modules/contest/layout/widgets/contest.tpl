{*
	variables that are available:
	- {$widgetContest}: contains all the data for this widget
*}
{*{$widgetContest|dump}*}
{option:widgetContest}

        <h3>{$widgetContest.title}</h3>
        {$widgetContest.text}

        {option:widgetContest.questions}

            <form method="post" id="{$formName}">
                <input type="hidden" name="form" value="{$formName}" />
            {iteration:widgetContest.questions}
                <label for="answer_{$widgetContest.id}_{$widgetContest.questions.id}"> {$widgetContest.questions.question}</label>

                {option:widgetContest.questions.fielderror}
                <span class="text-error">{$widgetContest.questions.fielderror}</span>
                {/option:widgetContest.questions.fielderror}

                {$widgetContest.questions.field}


            {/iteration:widgetContest.questions}

                {option:widgetContest.multiple_questions}
                <input type="submit" name="submit_{$widgetContest.id}" id="submit_{$widgetContest.id}" value="{$lblSaveAnswers}"/>
                {/option:widgetContest.multiple_questions}

                {option:!widgetContest.multiple_questions}
                    <input type="submit" name="submit_{$widgetContest.id}" id="submit_{$widgetContest.id}" value="{$lblSaveAnswer}"/>
                {/option:!widgetContest.multiple_questions}
            </form>

        {/option:widgetContest.questions}

    {option:!widgetContest.questions}
    <p class="text-info">{$msgNoQuestions}</p>
    {/option:!widgetContest.questions}


{/option:widgetContest}

{option:successMessage}
<p class="text-success">{$successMessage}</p>
{/option:successMessage}

{option:profilesOnly}
<p class="text-warning">{$msgContestProfilesOnly}</p>
{/option:profilesOnly}

{option:multipleParticipations}
<p class="text-error">{$msgContestMultipleParticipations}</p>
{/option:multipleParticipations}