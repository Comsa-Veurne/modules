{option:categories}
    <ul class="list-unstyled row catalog-categories-homepage">

        {iteration:categories}
            <li class="col-sm-6">
                <a href="{$categories.full_url}" title="{$categories.title}">

                    {option:categories.image}
                        <img src="{$FRONTEND_FILES_URL}/Media/Images/480x300/{$categories.image.filename}" alt="{$categories.title} - {$siteTitle}" title="{$categories.title} - {$siteTitle}" class="img-responsive"/>
                    {/option:categories.image}
                    {option:!categories.balltext}
                    {option:categories.hours_heat}
                        <span class="button-heat" style="background-color:#{$categories.hours_heat_color};">
                                {$lblTill}
                            <strong>{$categories.hours_heat} h</strong>
                            {$lblHeat}
                            </span>
                        <!-- /.heat -->
                    {/option:categories.hours_heat}
                    {/option:!categories.balltext}
                    {option:categories.balltext}
                        <span class="button-heat button-heat-text" style="background-color:#{$categories.ballcolor};">
                            <strong>{$categories.balltext}</strong>
                        </span>
                        <!-- /.heat -->
                    {/option:categories.balltext}
                    <h2 {option:categories.hours_heat_color}style="color:#{$categories.hours_heat_color};"{/option:categories.hours_heat_color}>
                        {$categories.title}
                    </h2>
                </a>
            </li>
            <!-- /.col-sm-6 -->
        {/iteration:categories}
    </ul>
    <!-- /.catalog-categories-homepage -->
{/option:categories}