{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblContest|ucfirst}: {$lblAdd}</h2>
</div>

{form:add}
	<label for="title">{$lblTitle|ucfirst}</label>
	{$txtTitle} {$txtTitleError}

	<div id="pageUrl">
		<div class="oneLiner">
			{option:detailURL}<p><span><a href="{$detailURL}">{$detailURL}/<span id="generatedUrl"></span></a></span></p>{/option:detailURL}
			{option:!detailURL}<p class="infoMessage">{$errNoModuleLinked}</p>{/option:!detailURL}
		</div>
	</div>

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
								<h3><abbr title="{$lblRequiredField}">*</abbr></h3>
							</div>
                            <div class="optionsRTE">
                                {$txtText} {$txtTextError}
                            </div>
						</div>
                        <div class="box">
                            <div class="heading">
                                <h3>{$lblSuccessMessage}</h3>
                            </div>
                            <div class="optionsRTE">
                                {$txtSuccessMessage} {$txtSuccessMessageError}
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

                        <div id="publishOptions" class="box">
                            <div class="heading">
                                <h3>{$lblProfilesOnly|ucfirst}</h3>
                            </div>

                            <div class="options">
                                <ul class="inputList">
                                    {iteration:profiles_only}
                                        <li>
                                            {$profiles_only.rbtProfilesOnly}
                                            <label for="{$profiles_only.id}">{$profiles_only.label}</label>
                                        </li>
                                    {/iteration:profiles_only}
                                </ul>
                            </div>
                        </div>

                        <div id="publishOptions" class="box">
                            <div class="heading">
                                <h3>{$lblMultipleParticipationsPossible|ucfirst}</h3>
                            </div>

                            <div class="options">
                                <ul class="inputList">
                                    {iteration:multiple_participations}
                                        <li>
                                            {$multiple_participations.rbtMultipleParticipations}
                                            <label for="{$multiple_participations.id}">{$multiple_participations.label}</label>
                                        </li>
                                    {/iteration:multiple_participations}
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
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:add}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}