{option:point}
    <a href="{$point.full_url}" class="btn btn-default" title="{$point.title}">{$lblReadMore|ucfirst}</a>
    {option:point.allow_subscriptions}
    <a href="{$point.full_url}#agendaSubscriptionForm" class="btn btn-primary" title="{$point.title}">{$lblSubscribe|ucfirst}</a>
    {/option:point.allow_subscriptions}
{/option:point}