{*
	variables that are available:
	- {$widgetGallery}: contains all the data for this widget
*}

<h3>{$item.title}</h3>
{option:widgetGallery}
        <ul class="list-unstyled row gallery-gallery gallery-widget">
            {iteration:widgetGallery}
    <li class="col-md-3 col-sm-4 col-xs-6">
        <a href="{$widgetGallery.image_800x}" title="{$widgetGallery.description}" class="colorbox"><img src="{$widgetGallery.image_400x300}" alt="{$widgetGallery.description} - {$widgetGallery.filename}" title="{$widgetGallery.description} - {$widgetGallery.filename}" class="img-responsive"></a>
        <span>{$widgetGallery.description}</span>
    </li>
{/iteration:widgetGallery}
        </ul>
{option:showMoreButton}
    <button class="btn btn-default pull-right more-pictures" type="submit">{$lblMorePictures}</button>
    <button class="btn btn-default pull-right less-pictures" type="submit">{$lblLessPictures}</button>
{/option:showMoreButton}
    </div>
    <!-- /.container-galleria -->
    <div class="clearfix"></div>
{/option:widgetGallery}
