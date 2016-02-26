{iteration:mediaItems.videos}
    <li id="li-{$mediaItems.videos.id}">
        {$mediaItems.videos.video_html}
        <div>
            <span class="filename-norename">{$mediaItems.videos.filename}</span>
        </div>
        {$mediaItems.videos.txtText}
        <a href="" data-message-id="confirmDelete-{$mediaItems.videos.id}" class="delete button icon iconDelete"
           id="{$mediaItems.videos.id}">{$lblDelete|ucfirst}</a>
        <div id="confirmDelete-{$mediaItems.videos.id}" title="{$lblDelete|ucfirst}?" style="display: none;">
            <p>
                {$msgConfirmDelete|sprintf:{$mediaItems.videos.filename}}
            </p>
        </div>
        <input type="checkbox" class="check"/>
    </li>
{/iteration:mediaItems.videos}