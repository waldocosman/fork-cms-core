{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
    <h2>{$lblModuleSettings|ucfirst}: {$lblLocation}</h2>
</div>

{form:settings}
<div class="box horizontal">
    <div class="heading">
        <h3>{$lblInstagramUserImagesSettings|ucfirst}</h3>
    </div>
    <div class="options">
        <p>
            <label for="access_token">{$lblUserMediaRecentCount|ucfirst}</label>
            {$txtUserMediaRecentCount} {$txtUserMediaRecentCountError}
        </p>
    </div>
</div>
<div class="box horizontal">
    <div class="heading">
        <h3>{$lblInstagramSettingsAPI|ucfirst}</h3>
    </div>
    <div class="options">
        <p>
            <label for="clientId">{$lblClientId|ucfirst}</label>
            {$txtClientId} {$txtClientIdError}
        </p>
    </div>
    <div class="options">
        <p>
            <label for="clientSecret">{$lblClientSecret|ucfirst}</label>
            {$txtClientSecret} {$txtClientSecretError}
        </p>
    </div>
    <div class="options">
        <p>
            <label for="websiteUrl">{$lblWebsiteUrl|ucfirst}</label>
            {$txtWebsiteUrl} {$txtWebsiteUrlError}
        </p>
    </div>
    <div class="options">
        <p>
            <label for="redirectUri">{$lblRedirectUri|ucfirst}</label>
            {$txtRedirectUri} {$txtRedirectUriError}
        </p>
    </div>
    <div class="options">
        <p>
            <label for="username">{$lblUsername|ucfirst}</label>
            {$txtUsername} {$txtUsernameError}
        </p>
    </div>
    <div class="options">
        <p>
            <label for="access_token">{$lblAccessToken|ucfirst}</label>
            {$txtAccessToken} {$txtAccessTokenError}
        </p>
    </div>

    {option:access_token_url}
    <div class="options">
        <p>
          <a href="{$access_token_url}" class="button">{$lblGetAccessToken}</a>
        </p>
    </div>
    {/option:access_token_url}


</div>

<div class="fullwidthOptions">
    <div class="buttonHolderRight">
        <input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
    </div>
</div>
{/form:settings}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}