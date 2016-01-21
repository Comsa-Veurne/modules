{option:products}
{*option:category}
    <h3>
        <a href="{$category.full_url}" title="{$category.title}">
            {$category.title}
        </a>
    </h3>
{/option:category*}
    <h2 class="sr-only">{$lblProducts} {$category.title}</h2>
    <ul class="products list-unstyled row">
        {iteration:products}
            <li class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
                <div>
                        {option:products.image_dim1}
                    <a href="{$products.image_dim2}" class="colorbox" title="{$products.title}">
                        <img class="img-responsive" src="{$products.image_dim1}" alt="{$products.title|ucfirst}" title="{$products.title|ucfirst}"/>
                    </a>
                        {/option:products.image_dim1}

                    {option:!products.image_dim1}
                        <a  class="colorbox" title="{$products.title}">
                        </a>
                    {/option:!products.image_dim1}
                    <h3>
                         {$products.title}
                    </h3>
                    <div class="summary">
                        {$products.summary}
                    </div>
                </div>
            </li>
        {/iteration:products}
    </ul>
{/option:products}