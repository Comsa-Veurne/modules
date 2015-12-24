{option:widgetMailings}
    <ul class="unstyled">
        {iteration:widgetMailings}
            <li>
                <a href="{$var|geturlforblock:'mailengine':'MailengineDetail'}?id={$widgetMailings.id}">{$widgetMailings.subject}
                    -
                    <time>{$widgetMailings.start_time|date:{$dateFormatLong}:{$LANGUAGE}}</time>
                </a></li>
        {/iteration:widgetMailings}

    </ul>
{/option:widgetMailings}