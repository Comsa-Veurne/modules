{option:item}
    <div id="galleryIndex">
        <article class="mod">
            <h3>{$item.title}</h3>
            {$item.description}
        </article>
    </div>
{option:item.images}
    <ul class="list-unstyled row gallery-gallery">
        {iteration:item.images}
            <li class="col-xs-6 col-sm-4 col-md-3">
                <a href="{$item.images.image_800x}" title="{$item.images.description}" class="colorbox"><img src="{$item.images.image_400x300}" alt="{$item.images.description} {$item.images.filename}" title="{$item.images.description} {$item.images.filename}" class="img-responsive"></a>
                {*<span>{$item.images.description}</span>*}
            </li>
        {/iteration:item.images}
    </ul>
    <a class="btn btn-primary pull-right" href="{$var|geturlforblock:'Gallery'}">{$msgToCatalogOverview}</a>
    <div class="clear">&nbsp;</div>
{/option:item.images}
{/option:item}

