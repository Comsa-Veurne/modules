{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblAddresses|ucfirst} - {$lblGroup}: {$lblAdd}</h2>
</div>

{form:add}
    <div id="pageUrl">
        <div class="oneLiner">
            {option:detailURL}<p><span><a href="{$detailURL}">{$detailURL}/<span id="generatedUrl"></span></a></span>
                </p>{/option:detailURL}
            {option:!detailURL}<p class="infoMessage">{$errNoModuleLinked}</p>{/option:!detailURL}
        </div>
    </div>
    <div class="tabs">
        <ul>
            <li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
            <li><a href="#tabAddresses">{$lblAddresses|ucfirst}</a></li>
            <li><a href="#tabSEO">{$lblSEO|ucfirst}</a></li>
        </ul>

        <div id="tabContent">
            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td id="leftColumn">

                        {* Main content *}
                        <div class="box">
                            <div class="option">
                                <div class="horizontal">
                                    <label for="title">{$lblTitle|ucfirst}</label>
                                    {$txtTitle} {$txtTitleError}
                                </div>
                            </div>
                            <div class="option">
                                <div class="horizontal">
                                    <label for="title">{$lblGroupParent|ucfirst}</label>
                                    {$ddmGroup} {$ddmGroupError}
                                </div>
                            </div>
                        </div>

                    </td>

                    <td id="sidebar">

                    </td>
                </tr>
            </table>
        </div>
        <div id="tabAddresses">
            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td id="leftColumn">

                        {* Main content *}
                        <div class="box">

                            <div class="option">
                                {option:addresses}
                                    <ul class="inputList">
                                        {iteration:addresses}
                                            <li>{$addresses.chkAddresses}
                                            <label for="{$addresses.id}">{$addresses.label|ucfirst}</label>
                                            </li>{/iteration:addresses}
                                    </ul>
                                {$chkAddressesError}
                                {/option:addresses}
                                {option:!addresses}
                                {$msgNoAddresses}
                                {/option:!addresses}
                            </div>
                        </div>

                    </td>

                    <td id="sidebar"></td>
                </tr>
            </table>
        </div>
        <div id="tabSEO">
            {include:{$BACKEND_CORE_PATH}/layout/templates/seo.tpl}
        </div>
    </div>
    <div class="fullwidthOptions">
        <div class="buttonHolderRight">
            <input id="addButton" class="inputButton button mainButton" type="submit" name="add"
                   value="{$lblAdd|ucfirst}"/>
        </div>
    </div>
{/form:add}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}