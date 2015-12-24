/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Interaction for the mailengine module
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
jsBackend.mailengine =
{
    // constructor
    init: function ()
    {

        $(jsBackend.mailengine.chartMailsOpenedByDay);
        $(jsBackend.mailengine.chartMailsOpenedByHour);
        $(jsBackend.mailengine.chartLinksClickedTotal);
        $(jsBackend.mailengine.chartLinksClickedByDay);

        // do meta
        if ($('#subject').length > 0) $('#subject').doMeta();

        $("#profilesAll").click(function ()
        {
            if ($(this).attr("checked") == "checked")
            {
                $("div.profile-groups").slideUp();
                $('div.profile-groups input[type=checkbox]').attr('checked', false);

            }
            else
            {
                $("div.profile-groups").slideDown();
            }
        })

    },

    chartMailsOpenedByDay: function ()
    {
        //--Show the highchart on the whole page (only added in the first function)
        $("div.tabs ul li a").click(function ()
        {
            $(window).resize();
        })


        if ($('#mailsOpenedByDay div.series').length > 0)
        {
            // Get the values from the template
            title = JSON.parse($('#mailsOpenedByDay div.title').text());
            xAxis = JSON.parse($('#mailsOpenedByDay div.xAxis').text());
            series = JSON.parse($('#mailsOpenedByDay div.series').text());

            var chart = new Highcharts.Chart(
                {
                    title: {
                        text: title
                    },
                    chart: {
                        renderTo: 'mailsOpenedByDay',
                        type: 'column',
                        animation: 'swing'
                    },
                    plotOptions: {
                        series: {
                            animation: {
                                duration: 1000,
                                easing: 'linear'
                            }
                        }
                    },
                    xAxis: {
                        categories: xAxis
                    },
                    yAxis: {
                        title: {
                            text: ''
                        }
                    },
                    series: [series]
                });

            //--Needed to redraw the graph
            $(window).resize();
        }
    },
    chartMailsOpenedByHour: function ()
    {
        if ($('#mailsOpenedByHour div.series').length > 0)
        {
            // Get the values from the template
            title = JSON.parse($('#mailsOpenedByHour div.title').text());
            xAxis = JSON.parse($('#mailsOpenedByHour div.xAxis').text());
            series = JSON.parse($('#mailsOpenedByHour div.series').text());

            var chart = new Highcharts.Chart(
                {
                    title: {
                        text: title
                    },
                    chart: {
                        renderTo: 'mailsOpenedByHour',
                        type: 'column',
                        animation: 'swing'
                    },
                    plotOptions: {
                        series: {
                            animation: {
                                duration: 1000,
                                easing: 'linear'
                            }
                        }
                    },
                    xAxis: {
                        categories: xAxis
                    },
                    yAxis: {
                        title: {
                            text: ''
                        }
                    },
                    series: [series]
                });

            //--Needed to redraw the graph
            $(window).resize();
        }
    },
    chartLinksClickedTotal: function ()
    {
        if ($('#linksClickedTotal div.series').length > 0)
        {
            // Get the values from the template
            title = JSON.parse($('#linksClickedTotal div.title').text());
            xAxis = JSON.parse($('#linksClickedTotal div.xAxis').text());
            yAxis = JSON.parse($('#linksClickedTotal div.yAxis').text());
            series = JSON.parse($('#linksClickedTotal div.series').text());

            var chart = new Highcharts.Chart(
                {
                    title: {
                        text: title
                    },
                    chart: {
                        renderTo: 'linksClickedTotal',
                        type: 'column',
                        animation: 'swing'
                    },
                    plotOptions: {
                        series: {
                            animation: {
                                duration: 1000,
                                easing: 'linear'
                            }
                        }
                    },
                    xAxis: {
                        categories: [xAxis]
                    },
                    yAxis: {
                        title: {
                            text: yAxis
                        }
                    },
                    series: series
                });

            //--Needed to redraw the graph
            $(window).resize();
        }
    },
    chartLinksClickedByDay: function ()
    {
        if ($('#linksClickedByDay div.title').length > 0)
        {
            // Get the values from the template
            title = JSON.parse($('#linksClickedByDay div.title').text());
            xAxis = JSON.parse($('#linksClickedByDay div.xAxis').text());
            yAxis = JSON.parse($('#linksClickedByDay div.yAxis').text());
            series = JSON.parse($('#linksClickedByDay div.series').text());

            var chart = new Highcharts.Chart(
                {
                    title: {
                        text: title
                    },
                    chart: {
                        renderTo: 'linksClickedByDay',
                        type: 'column',
                        animation: 'swing'
                    },
                    plotOptions: {
                        series: {
                            animation: {
                                duration: 1000,
                                easing: 'linear'
                            }
                        }
                    },
                    xAxis: {
                        categories: xAxis
                    },
                    yAxis: {
                        title: {
                            text: yAxis
                        }
                    },
                    series: series
                });

            //--Needed to redraw the graph
            $(window).resize();
        }
    }
}

$(jsBackend.mailengine.init);
