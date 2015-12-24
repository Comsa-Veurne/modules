{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblMailengine|ucfirst} {$lblStats}</h2>
</div>

<div class="box">
    <div class="heading">
        <h3>{$lblGeneral|ucfirst}</h3>
    </div>
    <div class="options">
        <strong>{$lblSubject|ucfirst}</strong>: {$record.subject}<br/>
        <strong>{$lblSendDate|ucfirst}</strong>: {$record.date}<br/>
        <strong>{$lblMailsSend|ucfirst}</strong>: {$record.users}<br/>
        <strong>{$lblUniqueMailsOpened|ucfirst}</strong>: {$record.percentage} %
    </div>
</div>

<div class="tabs">
    <ul>
        <li><a href="#tabMail">{$lblMailing|ucfirst}</a></li>
        <li><a href="#tabLinks">{$lblLinks|ucfirst}</a></li>
        <li><a href="#tabOverlay">{$lblOverlay|ucfirst}</a></li>
    </ul>

    <div id="tabMail">
        <div class="options">
            <div id="mailsOpenedByDay">
                <div class="title hidden">{$mailsOpenedByDayChart.title}</div>
                <div class="xAxis hidden">{$mailsOpenedByDayChart.xAxis}</div>
                <div class="series hidden">{$mailsOpenedByDayChart.series}</div>
            </div>

            <div id="mailsOpenedByHour">
                <div class="title hidden">{$mailsOpenedByHourChart.title}</div>
                <div class="xAxis hidden">{$mailsOpenedByHourChart.xAxis}</div>
                <div class="series hidden">{$mailsOpenedByHourChart.series}</div>
            </div>
        </div>
    </div>

    <div id="tabLinks">
        <div class="options">
            <div id="linksClickedTotal">
                <div class="title hidden">{$linksClickedTotalChart.title}</div>
                <div class="xAxis hidden">{$linksClickedTotalChart.xAxis}</div>
                <div class="yAxis hidden">{$linksClickedTotalChart.yAxis}</div>
                <div class="series hidden">{$linksClickedTotalChart.series}</div>
            </div>

            {option:linksClickedByDayChart}
                <div id="linksClickedByDay">
                    <div class="title hidden">{$linksClickedByDayChart.title}</div>
                    <div class="yAxis hidden">{$linksClickedByDayChart.yAxis}</div>
                    <div class="xAxis hidden">{$linksClickedByDayChart.xAxis}</div>
                    <div class="series hidden">{$linksClickedByDayChart.series}</div>
                </div>
            {/option:linksClickedByDayChart}
        </div>
    </div>
    <div id="tabOverlay">
        <iframe src="{$iframe}" style="width:800px;height:600px;border:0;"></iframe>
    </div>
</div>

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}