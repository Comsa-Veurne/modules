{*
	variables that are available:
	- {$widgetUpcomingAgendaLimited}:
*}

{option:widgetUpcomingAgendaLimited}
    <section id="UpcomingAgendaItems">
        <header>
            <h3>{$lblUpcomingAgendaItems|ucfirst}</h3>
        </header>
        <ul class="list-unstyled">
            {iteration:widgetUpcomingAgendaLimited}
                <li>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6">
                            <time itemprop="date"
                                  datetime="{$widgetUpcomingAgendaLimited.begin_date|date:'Y-m-d\TH:i:s'}">{$widgetUpcomingAgendaLimited.begin_date|date:"D. d F 'y":{$LANGUAGE}|ucfirst}</time>
                            {option:!widgetUpcomingAgendaLimited.same_day}
                                -
                                <time itemprop="date"
                                      datetime="{$widgetUpcomingAgendaLimited.end_date|date:'Y-m-d\TH:i:s'}">{$widgetUpcomingAgendaLimited.end_date|date:"D. d F 'y":{$LANGUAGE}|ucfirst}</time>
                            {/option:!widgetUpcomingAgendaLimited.same_day}
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <a href="{$widgetUpcomingAgendaLimited.full_url}"
                               title="{$widgetUpcomingAgendaLimited.title}">{$widgetUpcomingAgendaLimited.title}</a>
                        </div>
                    </div>
                </li>
            {/iteration:widgetUpcomingAgendaLimited}
        </ul>
        <a class="btn btn-default pull-right" href="{$var|geturlforblock:'Agenda'}">{$lblAllAgendaItems|ucfirst} <i
                    class="fa fa-long-arrow-right"></i></a>
    </section>
{/option:widgetUpcomingAgendaLimited}
