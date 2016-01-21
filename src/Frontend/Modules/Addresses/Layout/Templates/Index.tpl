{*{option:!items}
    <p class="text-warning">{$msgNoItems}</p>
{/option:!items}*}

<div class="row">
    {form:search}
    {$hidLat}
    {$hidLng}
        <div class="col-xs-12">

            <div class="form-group">
                <div class="row">
                    {*<div class="col-md-3 text-center">
                        <ul class="list-unstyled checkboxes">
                        {iteration:topgroups}
                            <li>
                                {$topgroups.chkTopgroups}
                                <label for="{$topgroups.id}">{$topgroups.label}</label>
                            </li>
                        {/iteration:topgroups}
                        </ul>
                    </div>*}
                    <div class="col-md-3">
                        {$ddmGroups}
                    </div>
                    <div class="col-md-6">
                        <label for="search" class="sr-only">{$lblSearch|ucfirst}</label>
                        <input type="text" class="form-control search" name="search" id="search"
                               placeholder="{$lblAddress}, {$lblZipcode}, ..."
                               {option:search}value="{$search}"{/option:search}/> {$txtSearchError}
                    </div>
                    <!-- div.col-xs-9 -->
                    <div class=" col-md-3">
                        <input type="submit" class="btn btn-primary form-control" Value="{$lblFind|ucfirst}"/>
                    </div>
                    <!-- div.col-xs-3 -->
                </div>
                <!-- div.row -->
            </div>
            <!-- div.form-group -->
        </div>
        <!-- div.col-xs-12 -->
    {/form:search}
</div>
{option:search}
    <div class="row">
        <div class="col-xs-12">
            {option:countItems}
                <div class="alert alert-success">
                    {option:search}{$msgSearchFor}:<strong>{$search}</strong>,{/option:search}
                    {$msgNumberResults} <strong>{$countItems}</strong>.
                </div>
            {/option:countItems}

            {option:!countItems}
                <div class="alert alert-danger">
                    {$msgNoResults}
                </div>
            {/option:!countItems}

        </div>
    </div>
{/option:search}

<div class="row">
    <div class="col-xs-12">
        <div id="googleMaps" style="height:500px;width:500px;"></div>
        <div class="clearfix">&nbsp;</div>
    </div>
    <!-- div.col-xs-12 -->
</div>
<!-- div.row -->

{option:items}
    {* List
    <div class="table-responsive">
        <table class="table table-striped" style="width: 100%">
            <tr>
                <th>{$lblName|ucfirst}</th>
                <th>{$lblAddress|ucfirst}</th>
                <th>{$lblContact|ucfirst}</th>
                <th>{$lblTraining|ucfirst}</th>
            </tr>
            {iteration:items}
                <tr>
                    <td>
                        {$items.firstname} {$items.name}
                    </td>
                    <td id="address-{$items.id}">
                        {$items.address}<br/>
                        {$items.zipcode} {$items.city|ucfirst}<br/>
                        {$items.countryname}
                    </td>
                    <td id="contact-{$items.id}">
                        {option:items.phone}
                            <dd>{$items.phone}</dd>
                        {/option:items.phone}


                        {option:items.email}
                            <dd><a href="mailto:{$items.email}" target="_blank">{$items.email}</a></dd>
                        {/option:items.email}

                        {option:items.website}
                            <dd><a href="http://{$items.website}" target="_blank" rel="external">{$items.website}</a>
                            </dd>
                        {/option:items.website}
                    </td>
                    <td>
                        {option:items.groups}
                            <ul class="list-unstyled">
                                {iteration:items.groups}
                                    <li>{$items.groups.title}</li>
                                {/iteration:items.groups}
                                {option:items.remark}
                                    <li>{$lblRemark}: {$items.remark}</li>
                                {/option:items.remark}
                            </ul>
                        {/option:items.groups}
                    </td>
                </tr>
            {/iteration:items}
        </table>
    </div>
    *}
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