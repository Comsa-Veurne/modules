{option:items}
    <ul class="unstyled">
        {iteration:items}
            <li>
                <a href="{$var|geturlforblock:'Mailengine':'MailengineDetail'}?id={$items.id}">{$items.subject}
                    -
                    <time>{$items.start_time|date:{$dateFormatLong}:{$LANGUAGE}}</time>
                </a></li>
        {/iteration:items}

    </ul>
{/option:items}