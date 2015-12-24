{option:items}
    <ul class="list-unstyled row gallery-overview">
        {iteration:items}
            <li class="col-md-6">
                <div class="row">
                    <div class="col-sm-6">
                        {option:items.image}
                            <a href="{$items.full_url}" title="{$items.title}">
                                <img src="{$items.image.image_400x300}" alt="{$items.title}" title="{$items.title}" class="img-responsive"/>
                            </a>
                        {/option:items.image}

                    </div>
                    <!-- /.col-sm-6 -->
                    <div class="col-sm-6">
                        <h3><a href="{$items.full_url}" title="{$items.title}">{$items.title}</a></h3>
                        {$items.description|truncate:100:'...'}

                    </div>
                    <!-- /.col-sm-6 -->
                </div>
                <!-- /.row -->

            </li>
            <!-- /.col-sm-2 -->
            {*<div class="col-sm-9">*}
            {*{$items.description}*}
            {*<a href="{$items.full_url}" title="{$items.title}">*}
            {*{$lblViewMorePhotos}*}
            {*</a>*}
            {*</div>*}
            <!-- /.col-sm-9 -->
        {/iteration:items}
    </ul>
{/option:items}