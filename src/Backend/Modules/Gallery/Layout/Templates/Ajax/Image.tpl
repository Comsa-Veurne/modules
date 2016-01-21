{iteration:images}
    <li id="li-{$images.id}">
        <img src="{$images.image_128x128}" alt="{$images.filename}" title="{$images.filename}">

        <div>
            <span class="filename">{$images.name}</span>
        </div>
        {$images.field_description}
        <a href="" data-message-id="confirmDelete-{$images.id}" class="delete button icon iconDelete" id="{$images.id}">{$lblDelete|ucfirst}</a>
        <div id="confirmDelete-{$images.id}" title="{$lblDelete|ucfirst}?" style="display: none;">
            <p>
                {$msgConfirmDelete|sprintf:{$images.name}}
            </p>
        </div>
        <input type="checkbox" class="check"/>
    </li>
{/iteration:images}