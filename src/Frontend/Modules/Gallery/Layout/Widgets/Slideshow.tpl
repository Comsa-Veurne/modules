{option:widgetSlideshow}
    {*<img class="slider-border hidden-xs" src="{$THEME_URL}/Core/Layout/Images/slider-kader.png" alt="{$siteTitle}" title="{$siteTitle}" class="img-responsive"/>*}
    <!-- /.slider-border -->
    <div class="slider">
        <div class="pager"></div>
        <ul class="gallery-slideshow list-unstyled" data-cycle-fx="fade" data-cycle-slides="li" data-cycle-timeout="5000" data-cycle-auto-height="calc" data-cycle-loader="wait" data-cycle-pager=".pager" data-cycle-center-horz="true" data-cycle-center-vert="true">
            {iteration:widgetSlideshow}
                <li>
                    <img src="{$FRONTEND_FILES_URL}/Gallery/Images/800x/{$widgetSlideshow.filename}" alt="{$siteTitle}{*$widgetSlideshow.text*}" title="{$siteTitle}{*$widgetSlideshow.text*}" class="img-responsive">
                    &nbsp;
                </li>
            {/iteration:widgetSlideshow}
        </ul>
    </div>
    <!-- /.slider -->
    <div class="clearfix"></div>
    <!-- /.clearfix -->

{/option:widgetSlideshow}



