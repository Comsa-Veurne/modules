{*
  variables that are available:
  - {$item}: contains data about the agenda item
  - {$beginDate}: begindate of the agenda item
  - {$endDate} : enddate of the agenda item
  - {$images}: agenda item images
  - {$videos}: agenda item videos
  - {$files}: agenda item files
  - {$location}: location of the agenda item
  - {$locationSettings}: google maps settings
*}

<h1 itemprop="name">{$item.title}</h1>

<article {option:item.color}class="{$item.color}"{/option:item.color}>
    <p class="meta">
        {* Category*}
        {$lblCategory|ucfirst}: <a href="{$item.category_full_url}"
                                   title="{$item.category_title}">{$item.category_title}</a>
    </p>

    {option:item.introduction}
        <div class="text">
            {$item.introduction}
        </div>
    {/option:item.introduction}

    <div class="row">
        <div class="col-sm-6">
            <b>{$lblDate|ucfirst}:</b>
            {$beginDate|date:{$dateFormatLong}:{$LANGUAGE}}
            {$beginDate|date:{$timeFormat}:{$LANGUAGE}}
            {option:!item.same_day}
                -
            {$endDate|date:{$dateFormatLong}:{$LANGUAGE}}
            {$endDate|date:{$timeFormat}:{$LANGUAGE}}
            {/option:!item.same_day}
            <br/>
            {* Show location info *}
            {option:locationSet}
            {option:location.name}
                <b>{$lblLocation|ucfirst}:</b>
            {$location.name}
            {/option:location.name}
            {option:location.street}
                <br/>
                <b>{$lblStreet|ucfirst}:</b>
            {$location.street}
            {/option:location.street}
            {option:location.number}
                <br/>
                <b>{$lblAddressNumber|ucfirst}:</b>
            {$location.number}
            {/option:location.number}
            {option:location.zip}
                <br/>
                <b>{$lblPostalCode|ucfirst}:</b>
            {$location.zip}
            {/option:location.zip}
            {option:location.city}
                <br/>
                <b>{$lblCity|ucfirst}:</b>
            {$location.city}
            {/option:location.city}
                <br/>
            {/option:locationSet}
            {option:item.price}
                <b>{$lblPrice|ucfirst}:</b>
                &euro;{$item.price}
            {/option:item.price}
            {option:item.allow_subscriptions}
                <br/>
                <a class="inputSubmit btn btn-primary" href="#agendaSubscriptionForm">{$msgSubscribe|ucfirst}</a>
            {/option:item.allow_subscriptions}
            <br/> <br/>
        </div>

        {* Agenda Single Image *}
        {*<div class="image">*}
        {*{option:item.image}<img src="{$item.image}" alt="{$item.title}" itemprop="image" />{/option:item.image}*}
        {*</div>*}

        {*Show location via Google Maps*}
        {option:googlemapsSet}
        {option:locationSet}
        {option:locationSettingsSet}
            {*@remark: do not remove the parseMap-class, it is used by JS*}
            <div id="map{$location.id}" class="parseMap col-sm-6"
                 style="height: {$locationSettings.height}px; {*width: {$locationSettings.width}px;*}"></div>
        {option:locationSettings.directions}
            <aside id="locationSearch{$location.id}" class="locationSearch">
                <form method="get" action="#">
                    <p>
                        <label for="locationSearchAddress{$location.id}">{$lblStart|ucfirst}<abbr
                                    title="{$lblRequiredField}">*</abbr></label>
                        <input type="text" id="locationSearchAddress{$location.id}" name="locationSearchAddress"
                               class="inputText"/>
                                <span id="locationSearchError{$location.id}" class="formError inlineError"
                                      style="display: none;">{$errFieldIsRequired|ucfirst}</span>
                    </p>
                    <p>
                        <input type="submit" id="locationSearchRequest{$location.id}" name="locationSearchRequest"
                               class="inputSubmit" value="{$lblShowDirections|ucfirst}"/>
                    </p>
                </form>
            </aside>
        {/option:locationSettings.directions}

        {option:locationSettings.full_url}
            <p><a href="{$locationSettings.maps_url}" title="{$lblViewLargeMap}">{$lblViewLargeMap|ucfirst}</a></p>
        {/option:locationSettings.full_url}
            <div id="markerText{$location.id}" style="display: none;">
                <address>
                    {$location.street} {$location.number}<br/>
                    {$location.zip} {$location.city}
                </address>
            </div>
        {/option:locationSettingsSet}
        {/option:locationSet}
        {/option:googlemapsSet}
    </div>

    {* Agenda Multiple Images *}
    {*{option:images}*}
    {*<div class="images">*}
    {*<h2><b>{$lblImages|ucfirst}:</b></h2>*}
    {*<ul>*}
    {*{iteration:images}*}
    {*<li>*}
    {*<img src="{$images.image_first}" alt="{$images.title}" title="{$images.title}"/>*}
    {*<img src="{$images.image_second}" alt="{$images.title}" title="{$images.title}" />*}
    {*<img src="{$images.image_third}" alt="{$images.title}" title="{$images.title}" />*}
    {*</li>*}
    {*{/iteration:images}*}
    {*</ul>*}
    {*</div>*}
    {*{/option:images}*}

    {* Agenda Multiple Videos *}
    {*{option:videos}*}
    {*<div class="videos">*}
    {*<h2><b>{$lblVideos|ucfirst}:</b></h2>*}
    {*<ul>*}
    {*{iteration:videos}*}
    {*<li><a class="fancybox fancybox.iframe" rel="gallery" href="{$videos.url}">*}
    {*<img src="{$videos.image}" alt="{$videos.title}" title="{$videos.title}">*}
    {*</a></li>*}
    {*{/iteration:videos}*}
    {*</ul>*}
    {*</div>*}
    {*{/option:videos}*}

    {* Agenda Multiple Files *}
    {*{option:files}*}
    {*<div class="files">*}
    {*<h2><b>{$lblFiles|ucfirst}:</b></h2>*}
    {*<ul>*}
    {*{iteration:files}*}
    {*<li><a href="{$files.url}">{$files.title}</a></li>*}
    {*{/iteration:files}*}
    {*</ul>*}
    {*</div>*}
    {*{/option:files}*}

    {option:item.text}
        <div class="text">
            <h2><b>{$lblInformation|ucfirst}:</b></h2>
            {$item.text}
        </div>
    {/option:item.text}

    {option:item.allow_subscriptions}
        <section id="agendaSubscriptionForm" class="mod">
            <div class="inner">
                <header class="hd">
                    <h2 id="{$actSubscribe}"><b>{$msgSubscribe|ucfirst}:</b></h2>
                </header>
                <div class="bd">
                    {option:subscriptionIsInModeration}
                        <div class="message warning"><p>{$msgAgendaSubscriptionInModeration}</p>
                        </div>{/option:subscriptionIsInModeration}
                    {option:subscriptionIsSpam}
                        <div class="message error"><p>{$msgAgendaSubscriptionIsSpam}</p>
                        </div>{/option:subscriptionIsSpam}
                    {option:subscriptionIsAdded}
                        <div class="message success"><p>{$msgAgendaSubscriptionIsAdded}</p>
                        </div>{/option:subscriptionIsAdded}
                    {form:subscriptionsForm}
                        <div class="alignBlocks">
                            <p {option:txtNameError}class="errorArea"{/option:txtNameError}>
                                <label for="name">{$lblName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
                                {$txtName} {$txtNameError}
                            </p>
                            <p {option:txtEmailError}class="errorArea"{/option:txtEmailError}>
                                <label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
                                {$txtEmail} {$txtEmailError}
                            </p>
                        </div>
                        <p>
                            <input class="inputSubmit btn btn-primary" type="submit" name="subscription"
                                   value="{$msgSubscribe|ucfirst}"/>
                        </p>
                    {/form:subscriptionsForm}
                </div>
            </div>
        </section>
    {/option:item.allow_subscriptions}

    {option:images}
        <h2><b>{$lblGallery|ucfirst}:</b></h2>
        <ul class="list-unstyled row media">
            {iteration:images}
                <li class="col-xs-6 col-sm-4 col-md-3">
                    <a class="colorbox" href="{$FRONTEND_FILES_URL}/Media/Images/800x/{$images.filename}"
                       title="{$item.title} - {$siteTitle}" rel="product-detail"
                       id="catalog-product-image-{$images.id}">
                        <img src="{$FRONTEND_FILES_URL}/Media/Images/400x300/{$images.filename}"
                             alt="{$item.title} - {$siteTitle}"
                             title="{$item.title} - {$siteTitle}"
                             class="img-responsive"/>
                    </a>
                </li>
            {/iteration:images}
        </ul>
    {/option:images}
</article>
<hr/>
<a class="btn btn-default" href="{$var|geturlforblock:'Agenda'}"><i class="fa fa-caret-left fa-lg"></i> Terug naar lijst</a>