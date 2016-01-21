{option:categoriesTree}
    <h3 class="hidden-xs">{$lblProducts}</h3>
    <a class="catalog-categories-responsive visible-xs" href="">
        {$lblCategories|ucfirst} {$lblProducts} <span class="glyphicon glyphicon-chevron-down"></span>
    </a>
    <!-- /.categories-responsive -->
    <ul class="list-unstyled  catalog-categories-left">

        {iteration:categoriesTree}
            <li {option:categoriesTree.selected}class="selected"{/option:categoriesTree.selected}>
                <a href="{$categoriesTree.full_url}" title="{$categoriesTree.title}">
                    {$categoriesTree.title}
                </a>
                {option:categoriesTree.children}
                    <ul class="list-unstyled">
                        {iteration:categoriesTree.children}
                            <li {option:categoriesTree.children.selected}class="selected"{/option:categoriesTree.children.selected}>
                                <a href="{$categoriesTree.children.full_url}" title="{$categoriesTree.children.title}">
                                    {$categoriesTree.children.title}
                                </a>
                            </li>
                        {/iteration:categoriesTree.children}

                    </ul>
                {/option:categoriesTree.children}
            </li>
        {/iteration:categoriesTree}
    </ul>
    <!-- /.list-unstyled categories -->
{/option:categoriesTree}