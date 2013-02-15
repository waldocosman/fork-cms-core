{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblQuestion|ucfirst}: {$lblEdit}</h2>
</div>

{form:edit}
	<div class="tabs">
		<ul>
			<li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
			<li><a href="#tabSEO">{$lblSEO|ucfirst}</a></li>
		</ul>

		<div id="tabContent">
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td id="leftColumn">

						{* Main content *}
						<div class="box">
                            <div class="heading">
                                &nbsp;
                            </div>
                            <div class="options">
                                <p>
                                    <label for="question">{$lblQuestion|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
                                    {$txtQuestion} {$txtQuestionError}
                                </p>
                            </div>
                            <div class="options">
                                <p>
                                    <label for="answer">{$lblAnswer|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
                                    {$txtAnswer} {$txtAnswerError}
                                </p>
                            </div>
                            <div class="options">
                                <p>
                                    <label for="points">{$lblPoints|ucfirst}</label>
                                    {$txtPoints} {$txtPointsError}
                                </p>
                            </div>
						</div>

					</td>

					<td id="sidebar">
						<div id="publishOptions" class="box">
							<div class="heading">
								<h3>{$lblStatus|ucfirst}</h3>
							</div>

							<div class="options">
								<ul class="inputList">
                                    {iteration:hidden}
                                        <li>
                                            {$hidden.rbtHidden}
                                            <label for="{$hidden.id}">{$hidden.label}</label>
                                        </li>
                                    {/iteration:hidden}
								</ul>
							</div>
						</div>

                        <div id="mustBeCorrectOptions" class="box">
                            <div class="heading">
                                <h3>{$lblMustBeCorrect|ucfirst}</h3>
                            </div>

                            <div class="options">
                                <ul class="inputList">
                                    {iteration:must_be_correct}
                                        <li>
                                            {$must_be_correct.rbtMustBeCorrect}
                                            <label for="{$must_be_correct.id}">{$must_be_correct.label}</label>
                                        </li>
                                    {/iteration:must_be_correct}
                                </ul>
                            </div>
                        </div>

                        <div id="requiredOptions" class="box">
                            <div class="heading">
                                <h3>{$lblRequired|ucfirst}</h3>
                            </div>

                            <div class="options">
                                <ul class="inputList">
                                    {iteration:required}
                                        <li>
                                            {$required.rbtRequired}
                                            <label for="{$required.id}">{$required.label}</label>
                                        </li>
                                    {/iteration:required}
                                </ul>
                            </div>
                        </div>
					</td>
				</tr>
			</table>
		</div>

		<div id="tabSEO">
			{include:{$BACKEND_CORE_PATH}/layout/templates/seo.tpl}
		</div>
	</div>

	<div class="fullwidthOptions">
		<a href="{$var|geturl:'delete_question'}&amp;id={$item.id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
			<span>{$lblQuestion|ucfirst} {$lblDelete|lowercase}</span>
		</a>
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblSave|ucfirst}" />
		</div>
	</div>

	<div id="confirmDelete" title="{$lblQuestion|ucfirst} {$lblDelete|lowercase}?" style="display: none;">
		<p>
			{$msgConfirmDelete|sprintf:{$item.question}}
		</p>
	</div>
{/form:edit}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}