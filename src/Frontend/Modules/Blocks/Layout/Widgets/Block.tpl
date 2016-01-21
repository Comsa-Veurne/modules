{option:block.image}
    <div class="block" style="background-image: url('{$FRONTEND_FILES_URL}/Blocks/image/600x300/{$block.image}');">
        <div class="block-text">

            <div class="text">
                <h2>
                    {$block.title}
                </h2>
                {$block.text}
            </div>
            <!-- /.text -->

            <a class="link" href="{$block.link}">
                <h4>
                    &nbsp;<i class="fa fa-caret-right fa-lg"></i>
                    {option:block.linktext}
                    {$block.linktext}
                    {/option:block.linktext}
                    {option:!block.linktext}
                    {$block.title}
                    {/option:!block.linktext}
                    <!-- /.fa fa-long-arrow-right -->
                </h4>
            </a>
        </div>
    </div>
{/option:block.image}