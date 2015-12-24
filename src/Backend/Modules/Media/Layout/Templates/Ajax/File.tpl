{iteration:mediaItems.files}
    <li id="li-{$mediaItems.files.id}">
        <a class="url" target="_blank" href="{$mediaItems.files.url}">
            <img src="/src/Backend/Modules/Media/Layout/Images/ext/{$mediaItems.files.extension}.png"">
        </a>
        <div>
            <span class="filename">{$mediaItems.files.name}</span>
        </div>
        {$mediaItems.files.txtText}
        <a href="" data-message-id="confirmDelete-{$mediaItems.files.id}" class="delete button icon iconDelete" id="{$mediaItems.files.id}">{$lblDelete|ucfirst}</a>
        <div id="confirmDelete-{$mediaItems.files.id}" title="{$lblDelete|ucfirst}?" style="display: none;">
            <p>
                {$msgConfirmDelete|sprintf:{$mediaItems.files.name}}
            </p>
        </div>
        <input type="checkbox" class="check"/>
    </li>
{/iteration:mediaItems.files}