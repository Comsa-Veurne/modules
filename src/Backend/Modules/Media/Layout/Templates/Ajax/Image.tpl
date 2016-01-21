{iteration:mediaItems.images}
    <li id="li-{$mediaItems.images.id}">
        <img src="{$mediaItems.images.image_128x128}" alt="{$mediaItems.images.filename}"
             title="{$mediaItems.images.filename}">
        <div>
            <span class="filename">{$mediaItems.images.name}</span>
        </div>
        {$mediaItems.images.txtText}
        <a href="" data-message-id="confirmDelete-{$mediaItems.images.id}" class="delete button icon iconDelete"
           id="{$mediaItems.images.id}">{$lblDelete|ucfirst}</a>
        <div id="confirmDelete-{$mediaItems.images.id}" title="{$lblDelete|ucfirst}?" style="display: none;">
            <p>
                {$msgConfirmDelete|sprintf:{$mediaItems.images.name}}
            </p>
        </div>
        <input type="checkbox" class="check"/>
    </li>
{/iteration:mediaItems.images}