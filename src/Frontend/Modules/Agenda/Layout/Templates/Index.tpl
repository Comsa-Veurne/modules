{*
  variables that are available:
  - {$items}: contains all agenda
  - {$wholeday}: whole day agenda bool (yes/no)
  - {$timestamp}: current day timestamp
*}

{option:topitems}
    <h2>{$lblAgenda|ucfirst}</h2>
    <div class="agenda-top-list row">
        {iteration:topitems}
            <div class="col-xs-6 agenda-top-point">
                <div class="row">
                    <div class="col-lg-2 col-md-3 col-sm-4 center-block text-center calandar-icon">
                   <span class="day">
                       {$topitems.begin_date|date:'j':{$LANGUAGE}}
                   </span>
                    <span class="month">
                        {$topitems.begin_date|date:'M':{$LANGUAGE}}
                    </span>
                    </div>
                    <div class="col-lg-10 col-md-9 col-sm-8">
                        <h3>{$topitems.title}</h3>
                        <p>{$topitems.text|substring:0:250}</p>
                        <a class="btn btn-default" href="{$topitems.full_url}"
                           title="{$topitems.title}">{$lblReadMore|ucfirst} <i class="fa fa-caret-right fa-lg"></i></a>
                    </div>
                </div>
            </div>
        {/iteration:topitems}
    </div>
{/option:topitems}
{option:items}
    <h3>{$lblAgendaNearFuture}</h3>
{iteration:items}
    <div>
        <div class="agenda-points">
            <h4>{$items.begin_date|date:'j F Y':{$LANGUAGE}}: {$items.title}</h4>
            <p>{$items.text|substring:0:250}</p>
            <a class="btn btn-default" href="{$items.full_url}"
               title="{$items.title}">{$lblReadMore|ucfirst} <i class="fa fa-caret-right fa-lg"></i></a>
        </div>
    </div>
{/iteration:items}
{/option:items}
{option:!topitems}
    <p class="date">{$lblNoAgenda|ucfirst}.</p>
{/option:!topitems}