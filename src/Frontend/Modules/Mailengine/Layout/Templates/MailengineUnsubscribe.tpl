<h3>{$lblUnsubscribeNewsletter}</h3>

{option:unsubscribeIsSuccess}
    <div class="alert alert-success"><p>{$msgUnsubscribeSuccess}</p></div>
{/option:unsubscribeIsSuccess}

{option:!unsubscribeHideForm}
{form:unsubscribe}
    <p{option:txtEmailError} class="errorArea"{/option:txtEmailError}>
        <label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
        {$txtEmail} {$txtEmailError}
    </p>
    <p>
        <input id="send" class="btn btn-primary" type="submit" name="send" value="{$lblUnsubscribe|ucfirst}"/>
    </p>
{/form:unsubscribe}
{/option:!unsubscribeHideForm}