{option:success}
<div class="text-success">
    {$msgFileAddedProfile}
</div>
{/option:success}

{* Error *}
{option:formError}
<div class="text-error">
    {option:!loginError}
        <p>{$errFormError}</p>
    {/option:!loginError}
</div>
{/option:formError}

{form:file}
    {$fileImage} {$fileImageError}
     <input class="inputSubmit" type="submit" value="{$lblUpload|ucfirst}" />
{/form:file}