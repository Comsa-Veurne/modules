{option:!item}
    <p class="text-warning">{$msgNoitem}</p>
{/option:!item}

{option:item}
    <div class="addresses-detail">


        <div class="row">
            <div class="col-md-5">

                <article>
                    <header>
                        <h1>{$item.company}</h1>
                    </header>
                    <div>
                        <dl class="dl-horizontal" id="address-{$item.id}">
                            <dt>{$lblAddress|ucfirst}</dt>
                            <dd>
                                {$item.address}, {$item.zipcode} {$item.city},{$item.country}
                            </dd>

                            {option:item.phone}
                                <dt>{$lblPhone|ucfirst}</dt>
                                <dd>{$item.phone}</dd>
                            {/option:item.phone}

                            {option:item.fax}
                                <dt>{$lblFax|ucfirst}</dt>
                                <dd>{$item.fax}</dd>
                            {/option:item.fax}

                            {option:item.email}
                                <dt>{$lblEmail|ucfirst}</dt>
                                <dd><a href="mailto:{$item.email}">{$item.email}</a></dd>
                            {/option:item.email}

                            {option:item.website}
                                <dt>{$lblWebsite|ucfirst}</dt>
                                <dd><a href="http://{$item.website}" rel="external">{$item.website}</a></dd>
                            {/option:item.website}

                            {option:item.vat}
                                <dt>{$lblVat|ucfirst}</dt>
                                <dd>{$item.vat}</dd>
                            {/option:item.vat}


                        </dl>
                        {option:item.text}
                            <h3>{$lblInfo}</h3>
                        {$item.text}
                        {/option:item.text}

                        {option:item.opening_hours}
                            <h3>{$lblOpeningHours}</h3>
                        {$item.opening_hours}
                        {/option:item.opening_hours}
                    </div>
                </article>
            </div>
            <!-- div.col-md-5 -->
            <div class="col-md-6">
                <div class="google-maps-detail" id="googleMaps" style="height:200px;width:100%;"></div>
                <br/>
                {option:item.image}
                    <img src="{$item.image_800x}" alt="{$item.company}" title="{$item.company}" class="img-responsive"/>
                {/option:item.image}

            </div>
            <!-- div.col-md-6 -->
        </div>
        <!-- div.row -->
        <div class="row">

            <div class="col-md-5">
                <a href="{$goback}" class="btn btn-default" title="{$lblBackToOverview}">{$lblBackToOverview}</a>
            </div>
        </div>
    </div>
    <!-- /.dealer -->
{/option:item}