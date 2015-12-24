<h3>{$lblSubscribeNewsletter}</h3>
{option:subscribeIsSuccess}
    <div class="alert alert-success"><p>{$msgSubscribeSuccess}</p></div>
{/option:subscribeIsSuccess}

<div class="row">
    <div class="col-md-4">

        {option:!subscribeHideForm}
        {form:subscribe}
            <div class="form-group{option:txtEmailError} errorArea{/option:txtEmailError}">
                <label for="email" class="control-label">{$lblEmail|ucfirst}
                    <abbr title="{$lblRequiredField}">*</abbr></label>
                {$txtEmail} {$txtEmailError}
                <input id="send" class="btn btn-primary" type="submit" name="send" value="{$lblSubscribe|ucfirst}"/>
            </div>
        {/form:subscribe}
        {/option:!subscribeHideForm}
    </div>
</div>
