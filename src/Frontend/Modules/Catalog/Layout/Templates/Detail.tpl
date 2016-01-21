<div class="row">
    <div class="col-sm-3">
        {include:Modules/Catalog/Layout/Templates/CategoriesTree.tpl}
    </div>
    <!-- /.col-sm-3 -->
    <div class="col-sm-9">

        {option:item}
            {* Product information: see dump for additional info *}
            <meta itemprop="name" content="{$item.title}"/>
            <meta itemprop="category" content="{$item.category_title}"/>
        {option:brand}
            <meta itemprop="brand" content="{$brand.title}"/>
        {/option:brand}
            <h1>{$item.title}
                <small>{option:brand}{$brand.title}{/option:brand}</small>
                {*<small class="category">{$item.category_title}</small>*}
                {option:parentCategories}
                    <span class="sr-only">{$parentCategories}</span>
                {/option:parentCategories}
            </h1>
            <div class="row">


                <div class="col-sm-6">
                    {*
                                        <h4>{$lblInfo}</h4>
                    *}
                    {$item.text}
                    {option:specifications}
                        <hr/>
                        <h4>{$lblSpecifications|ucfirst}</h4>
                        <dl class="dl-list specifications">

                            {iteration:specifications}
                                <dt>{$specifications.title}</dt>
                                <dd> {$specifications.value}</dd>
                                <div class="clearfix"></div>
                            {/iteration:specifications}
                        </dl>
                    {/option:specifications}
                    {option:showContactSend}
                        <div class="alert alert-success">
                            <h4>{$lblContact|ucfirst}</h4>
                            <h5>{$lblMessageSend|ucfirst}</h5>
                        </div>
                    {/option:showContactSend}
                    {option:!showContactSend}
                    {option:contactAllowed}
                        <hr>
                        <h4>{$lblMoreInfo|ucfirst}:</h4>
                    {form:contact}
                        <div class="control-group {option:txtNameError}error{/option:txtNameError}">
                            <label for="name" class="control-label">{$lblName|ucfirst}*</label>
                            {$txtName}
                            <p class="text-error">{$txtNameError}</p>
                        </div>
                        <div class="control-group {option:txtPhoneError}error{/option:txtPhoneError}">
                            <label for="name" class="control-label">{$lblPhone|ucfirst}</label>
                            {$txtPhone}
                            <p class="text-error">{$txtPhoneError}</p>
                        </div>
                        <div class="control-group {option:txtEmailContactError}error{/option:txtEmailContactError}">
                            <label for="email" class="control-label">{$lblEmail|ucfirst}*</label>
                            {$txtEmailContact}
                            <p class="text-error">{$txtEmailContactError}</p>
                        </div>
                        <div class="control-group ">
                            <label for="message" class="control-label">{$lblMessage|ucfirst}</label>
                            {$txtMessageContact}
                            <p class="text-error">{$txtMessageContactError}</p>
                        </div>
                        <p>
                            <input type="submit" value='{$lblSend|ucfirst}' class="btn btn-primary"/>
                        </p>
                    {/form:contact}
                    {/option:contactAllowed}
                    {/option:!showContactSend}
                    {option:files}
                        <hr/>
                        <h4>{$lblFiles|ucfirst}</h4>
                        <ul class="list-unstyled">
                            {iteration:files}
                                <li>

                                    <a href="{$files.url}" target="_blank">
                                        {$files.title}
                                    </a>
                                </li>
                            {/iteration:files}
                        </ul>
                    {/option:files}

                    {option:videos}
                        <hr/>
                        <h4>{$lblVideos|ucfirst}</h4>
                        <ul class="list-unstyled row">

                            {iteration:videos}
                                <li class="col-sm-6">


                                    <h5>{$videos.title}</h5>
                                    {option:videos.image}
                                        <a href="{$videos.url}" target="_blank">
                                            <img src="{$videos.image}" alt="{$videos.title}" class="img-responsive"/>
                                        </a>
                                    {/option:videos.image}

                                </li>
                            {/iteration:videos}
                        </ul>
                    {/option:videos}
                </div>
                <!-- /.col-sm-6 -->

                <div class="col-sm-6">

                    {option:images}
                        {*{option:item.balltext}
                            <span class="button-heat button-heat-text" style="background-color:#{$item.ballcolor};">
                                <strong>{$item.balltext}</strong>
                            </span>
                            <!-- /.heat -->
                        {/option:item.balltext}*}
                        <ul class="list-unstyled catalog-product-images">
                            {iteration:images}
                                <li id="catalog-product-large-image-{$images.id}">
                                    <a href="{$FRONTEND_FILES_URL}/Media/Images/800x/{$images.filename}"
                                       class="colorbox" title="{$item.title} {$parentCategories} - {$siteTitle}"
                                       rel="product-detail">
                                        <img src="{$FRONTEND_FILES_URL}/Media/Images/400x450/{$images.filename}"
                                             alt="{$item.title} {$parentCategories} - {$siteTitle}"
                                             title="{$item.title} {$parentCategories} - {$siteTitle}"
                                             class="img-responsive"/>
                                    </a>
                                </li>
                            {/iteration:images}
                        </ul>
                    {/option:images}

                    {option:images}
                        <ul class="list-unstyled row media">
                            {iteration:images}
                                <li class="col-xs-6 col-sm-4 col-md-3">
                                    <a href="{$FRONTEND_FILES_URL}/Media/Images/800x/{$images.filename}"
                                       title="{$item.title} {$parentCategories} - {$siteTitle}" rel="product-detail"
                                       id="catalog-product-image-{$images.id}">
                                        <img src="{$FRONTEND_FILES_URL}/Media/Images/400x450/{$images.filename}"
                                             alt="{$item.title} {$parentCategories} - {$siteTitle}"
                                             title="{$item.title} {$parentCategories} - {$siteTitle}"
                                             class="img-responsive"/>
                                    </a>
                                </li>
                            {/iteration:images}
                        </ul>
                    {/option:images}
                </div>
                <!-- /.col-sm-6 -->

            </div>
            <!-- /.row -->
            <footer>
                <ul class="pagination">
                    {option:previousProduct}
                        <li>
                            <a href="{$previousProduct.url}" rel="next">{$lblPreviousProduct|ucfirst}
                                : {$previousProduct.title}</a>
                        </li>
                    {/option:previousProduct}
                    {option:nextProduct}
                        <li>
                            <a href="{$nextProduct.url}" rel="prev">{$lblNextProduct|ucfirst}
                                : {$nextProduct.title}</a>
                        </li>
                    {/option:nextProduct}
                </ul>
            </footer>
            {* Product images *}

            {*<a href="/{$category.url}">{$lblMoreProductsIn} {$item.category_title}</a>*}
            {*<br/>*}
            {*Product specifications *}







            {* Back to overview *}
            {*<p>*}
            {*<a href="{$var|geturlforblock:'catalog'}" title="{$msgToCatalogOverview|ucfirst}">{$msgToCatalogOverview|ucfirst}</a>*}
            {*</p>*}
        {/option:item}
    </div>
</div>
