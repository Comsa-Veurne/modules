{option:blocks}
    <div class="blocks">
        <h3>
            {$category.title|ucfirst}
        </h3>
        <ul class="list-unstyled">
            {iteration:blocks}
                <li>
                    <!-- /.link -->
                    {option:blocks.image}
                        <div class="block" style="background-image: url('{$FRONTEND_FILES_URL}/Blocks/image/600x300/{$blocks.image}');">
                            <div class="block-text">

                                <div class="text">
                                    <h2>
                                        {$blocks.title}
                                    </h2>
                                    {$blocks.text}
                                </div>
                                <!-- /.text -->

                                <a class="link" href="{$blocks.link}">
                                    <h4>
                                        &nbsp;<i class="fa fa-caret-right fa-lg"></i>
                                        {option:blocks.linktext}
                                        {$blocks.linktext}
                                        {/option:blocks.linktext}
                                        {option:!blocks.linktext}
                                        {$blocks.title}
                                        {/option:!blocks.linktext}
                                        <!-- /.fa fa-long-arrow-right -->
                                    </h4>
                                </a>
                            </div>
                        </div>
                    {/option:blocks.image}
                </li>
                <!-- /.block -->
            {/iteration:blocks}
        </ul>
    </div>
    <!-- /.blocks -->
{/option:blocks}
