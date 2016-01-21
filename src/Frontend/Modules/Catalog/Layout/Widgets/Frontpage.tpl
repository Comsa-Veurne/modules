{*
	variables that are available:
	- {$widgetCatalogRecentProducts}: contains an array with all products, each element contains data about the product
*}
{option:products}
    <ul id="cycle-big" class="list-unstyled">
        {iteration:products}
            <li id="catalog-showimage-{$products.image.id}">
                <a href="{$products.full_url}">
                    {option:products.image}
                        <img src="{$FRONTEND_FILES_URL}/Media/Images/1140x500/{$products.image.filename}"
                             alt="{$products.title}" title="{$products.title}" class="img-responsive"/>
                    {/option:products.image}
                    <h5>
                        {option:products.balltext}
                            <strong>&nbsp;{$products.balltext}: </strong>
                        {/option:products.balltext}
                        {option:!products.balltext}
                            <strong>&nbsp;</strong>
                        {/option:!products.balltext}
                        {$products.title}
                    </h5>
                </a>
            </li>
        {/iteration:products}
    </ul>
    <div class="row carousel">
        <div class="col-xs-1 text-center"><i id="prev" class="fa fa-caret-left"></i></div>
        <div class="col-xs-10">
            <ul id="cycle-carousel" class="list-unstyled cycle-carousel" data-cycle-pause-on-hover="true"
                data-cycle-slides="li" data-cycle-fx="carousel" data-cycle-next="#next" data-cycle-prev="#prev"
                data-cycle-timeout=5000>
                {iteration:productsCarousel}
                    <li data-catalog-id="{$productsCarousel.image.id}">
                        <a href="{$productsCarousel.full_url}">
                            {option:productsCarousel.image}
                                <img data-catalog-id="{$productsCarousel.image.id}"
                                     src="{$FRONTEND_FILES_URL}/Media/Images/800x/{$productsCarousel.image.filename}"
                                     alt="{$productsCarousel.title}" title="{$productsCarousel.title}"
                                     class="img-responsive"/>
                            {/option:productsCarousel.image}
                            <h5>
                                {option:productsCarousel.balltext}
                                    <strong>{$productsCarousel.balltext}</strong>
                                {/option:productsCarousel.balltext}
                                {option:!productsCarousel.balltext}
                                    <strong>&nbsp;</strong>
                                {/option:!productsCarousel.balltext}
                                {$productsCarousel.title}
                            </h5>
                        </a>
                    </li>
                {/iteration:productsCarousel}
            </ul>
        </div>
        <div class="col-xs-1 text-center"><i id="next" class="fa fa-caret-right"></i></div>
    </div>
{/option:products}