{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
    <h2>{$lblModuleSettings|ucfirst}: {$lblLocation}</h2>
</div>

{form:settings}
<div class="box horizontal">
    <div class="heading">
        <h3>{$lblUtilities|ucfirst}</h3>
    </div>
    <div class="options">
        <p>
            <label for="classnameContainer">{$lblClassnameContainer|ucfirst}</label>
            {$txtClassnameContainer} {$txtClassnameContainerError}
        </p>
    </div>
    <div class="options">
        <p>
            <label for="fontsizeSmall">{$lblFontsizeSmall|ucfirst}</label>
            {$txtFontsizeSmall} {$txtFontsizeSmallError}
        </p>
    </div>
    <div class="options">
        <p>
            <label for="fontsizeLarge">{$lblFontsizeLarge|ucfirst}</label>
            {$txtFontsizeLarge} {$txtFontsizeLargeError}
        </p>
    </div>

</div>

<div class="fullwidthOptions">
    <div class="buttonHolderRight">
        <input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
    </div>
</div>
{/form:settings}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}