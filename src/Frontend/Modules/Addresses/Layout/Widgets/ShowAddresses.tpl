{option:!items}
    <p class="text-warning">{$msgNoItems}</p>
{/option:!items}

<div class="row">
    <div class="col-xs-12">
        <div id="googleMaps" style="height:500px;width:500px;"></div>
        <div class="clearfix">&nbsp;</div>
    </div>
    <!-- div.col-xs-12 -->
</div>
<!-- div.row -->

{option:items}
    <div class="row">
        <ul class="list-unstyled addresses-list">
            {iteration:items}
                <li class="col-xs-12 col-md-4">
                    <header>
                        <h4><a href="{$items.full_url}" title="{$items.company}">{$items.company}</a></h4>
                    </header>
                    <dl class="dl-horizontal" id="address-{$items.id}">
                        <dt class="hidden-xs">{$lblAddress|ucfirst}</dt>
                        <dd>
                            {$items.address},
                            {$items.zipcode} {$items.city|ucfirst},
                            {$items.countryname}<br/>
                        </dd>

                        {option:items.phone}
                            <dt class="hidden-xs">{$lblPhone|ucfirst}</dt>
                            <dd>{$items.phone}</dd>
                        {/option:items.phone}


                        {option:items.email}
                            <dt class="hidden-xs">{$lblEmail|ucfirst}</dt>
                            <dd><a href="mailto:{$items.email}">{$items.email}</a></dd>
                        {/option:items.email}

                        {option:items.website}
                            <dt class="hidden-xs">{$lblWebsite|ucfirst}</dt>
                            <dd><a href="http://{$items.website}" rel="external">{$items.website}</a></dd>
                        {/option:items.website}
                        <dt class="empty">
                        </dt>
                        <dd>
                            <a href="{$items.full_url}" title="{$items.company}"
                               class="btn btn-default btn-sm">{$lblMoreInfo}</a>
                        </dd>
                    </dl>
                </li>
            {/iteration:items}
        </ul>
    </div>
{/option:items}

{option:items}
{option:!search}
    <ul class="list-unstyled list-addresses {option:!search}sr-only{/option:!search}">
        {iteration:items}
            <li>

                <h4><a href="{$items.full_url}" title="{$items.company}">{$items.company}</a></h4>
                <dl id="address-{$items.id}">

                    <dt class="hidden-xs">{$lblAddress|ucfirst}</dt>
                    <dd>
                        {$items.address},
                        {$items.zipcode} {$items.city|ucfirst},
                        {$items.countryname}<br/>

                    </dd>

                    {option:items.phone}
                        <dt class="hidden-xs">{$lblPhone|ucfirst}</dt>
                        <dd>{$items.phone}</dd>
                    {/option:items.phone}


                    {option:items.email}
                        <dt class="hidden-xs">{$lblEmail|ucfirst}</dt>
                        <dd><a href="mailto:{$items.email}">{$items.email}</a></dd>
                    {/option:items.email}

                    {option:items.website}
                        <dt class="hidden-xs">{$lblWebsite|ucfirst}</dt>
                        <dd><a href="http://{$items.website}" rel="external">{$items.website}</a></dd>
                    {/option:items.website}
                    <dt class="empty"></dt>
                    <dd>
                        <a href="{$items.full_url}" title="{$items.company}">{$lblMoreInfo}</a>
                    </dd>
                </dl>

            </li>
        {/iteration:items}
    </ul>
{/option:!search}
{/option:items}


