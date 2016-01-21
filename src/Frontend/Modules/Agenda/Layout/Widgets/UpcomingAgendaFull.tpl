{*
	variables that are available:
	- {$widgetUpcomingAgendaFull}:
*}

{option:widgetUpcomingAgendaFull}
    <section id="UpcomingAgendaItems">
        <header>
            <h3>{$lblUpcomingAgendaItems|ucfirst}</h3>
        </header>
        <ul class="list-unstyled">
            {iteration:widgetUpcomingAgendaFull}
                <li>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6">
                            <time itemprop="date"
                                  datetime="{$widgetUpcomingAgendaFull.begin_date|date:'Y-m-d\TH:i:s'}">{$widgetUpcomingAgendaFull.begin_date|date:"D. d F 'y":{$LANGUAGE}|ucfirst}</time>
                            {option:!widgetUpcomingAgendaFull.same_day}
                                -
                                <time itemprop="date"
                                      datetime="{$widgetUpcomingAgendaFull.end_date|date:'Y-m-d\TH:i:s'}">{$widgetUpcomingAgendaFull.end_date|date:"D. d F 'y":{$LANGUAGE}|ucfirst}</time>
                            {/option:!widgetUpcomingAgendaFull.same_day}
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <a href="{$widgetUpcomingAgendaFull.full_url}"
                               title="{$widgetUpcomingAgendaFull.title}">{$widgetUpcomingAgendaFull.title}</a>
                        </div>
                    </div>
                </li>
            {/iteration:widgetUpcomingAgendaFull}
        </ul>
        <footer>
            <a class="btn btn-default pull-right" href="{$var|geturlforblock:'Agenda'}">{$lblAllAgendaItems|ucfirst} <i
                        class="fa fa-long-arrow-right"></i></a>
    </section>
{/option:widgetUpcomingAgendaFull}
