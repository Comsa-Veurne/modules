{option:widgetCatalogProducts}
    <h2>{$lblProducts}</h2>
    <ul class="products list-unstyled row">
        {iteration:widgetCatalogProducts}
            <li class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
                <div>
                    <a href="{$widgetCatalogProducts.full_url}" title="{$widgetCatalogProducts.title}">
                        <img class="img-responsive" src="{$widgetCatalogProducts.image_dim1}" alt="{$widgetCatalogProducts.title|ucfirst}" title="{$widgetCatalogProducts.title|ucfirst}"/>
                    </a>

                    <h3>
                        <a href="{$widgetCatalogProducts.full_url}" title="{$widgetCatalogProducts.title}">{$widgetCatalogProducts.title|ucfirst}</a>
                    </h3>
                </div>
            </li>
        {/iteration:widgetCatalogProducts}
    </ul>
{/option:widgetCatalogProducts}