{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblBlocks|ucfirst}: {$lblEdit}</h2>
</div>

{form:edit}
    <label for="title">{$lblTitle|ucfirst}</label>
    {$txtTitle} {$txtTitleError}

    <div id="pageUrl">
        <div class="oneLiner">
            {option:detailURL}<p><span><a href="{$detailURL}/{$item.url}">{$detailURL}/<span id="generatedUrl">{$item.url}</span></a></span></p>{/option:detailURL}
            {option:!detailURL}<p class="infoMessage">{$errNoModuleLinked}</p>{/option:!detailURL}
        </div>
    </div>


    <div class="tabs">
        <ul>
            <li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
            <li><a href="#tabSEO">{$lblSEO|ucfirst}</a></li>
        </ul>

        <div id="tabContent">
            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td id="leftColumn">

                        <div class="box">
                            <div class="heading">
                                <h3>
                                    <label for="text">{$lblText|ucfirst}</label>
                                </h3>
                            </div>
                            <div class="optionsRTE">
                                {$txtText} {$txtTextError}
                            </div>
                        </div>

                        <div class="box">
                            <div class="heading">
                                <h3>
                                    <label for="link">{$lblLink|ucfirst}</label>
                                </h3>
                            </div>
                            <div class="options">
                                {*{$txtLink} {$txtLinkError}*}
                                {$rbtRedirectError}
                                <ul class="inputList radiobuttonFieldCombo">
                                    {iteration:redirect}
                                        <li>
                                            <label for="{$redirect.id}">{$redirect.rbtRedirect} {$redirect.label}</label>
                                            {option:redirect.isInternal}
                                                <label for="internalRedirect" class="visuallyHidden">{$redirect.label}</label>
                                            {$ddmInternalRedirect} {$ddmInternalRedirectError}
                                                <span class="helpTxt">{$msgHelpInternalRedirect}</span>
                                            {/option:redirect.isInternal}

                                            {option:redirect.isExternal}
                                                <label for="externalRedirect" class="visuallyHidden">{$redirect.label}</label>
                                            {$txtExternalRedirect} {$txtExternalRedirectError}
                                                <span class="helpTxt">{$msgHelpExternalRedirect}</span>
                                            {/option:redirect.isExternal}
                                        </li>
                                    {/iteration:redirect}
                                </ul>
                            </div>
                        </div>

                        <div class="box">
                            <div class="heading">
                                <h3>
                                    <label for="linktext">{$lblLinktext|ucfirst}</label>
                                </h3>
                            </div>
                            <div class="options">
                                {$txtLinktext} {$txtLinktextError}
                            </div>
                        </div>
                            <div class="box">
                                <div class="heading">
                                    <h3>
                                        <label for="image">{$lblImage|ucfirst}</label>
                                    </h3>
                                </div>
                                <div class="options">
                                    {option:item.image}
                                        <p><img src="{$FRONTEND_FILES_URL}/Blocks/image/128x128/{$item.image}"/></p>
                                    {/option:item.image}
                                    <p>
                                        {$fileImage} {$fileImageError}
                                    </p>
                                </div>
                            </div>


                    </td>

                    <td id="sidebar">

                            <div class="box">
                                <div class="heading">
                                    <h3>
                                        {$lblHidden|ucfirst}
                                    </h3>
                                </div>
                                <div class="options">
                                    <ul class="inputList">
                                        {iteration:hidden}
                                            <li>
                                                {$hidden.rbtHidden}
                                                <label for="{$hidden.id}">{$hidden.label|ucfirst}</label>
                                            </li>
                                        {/iteration:hidden}
                                    </ul>
                                </div>
                            </div>

                            <div class="box">
                                <div class="heading">
                                    <h3>
                                        <label for="categoryId">{$lblCategory|ucfirst}</label>
                                    </h3>
                                </div>
                                <div class="options">
                                    {$ddmCategoryId} {$ddmCategoryIdError}
                                </div>
                            </div>
                    </td>
                </tr>
            </table>
        </div>

        <div id="tabSEO">
            {include:{$BACKEND_CORE_PATH}/Layout/Templates/Seo.tpl}
        </div>

    </div>

    <div class="fullwidthOptions">
        <a href="{$var|geturl:'delete'}&amp;id={$item.id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
            <span>{$lblDelete|ucfirst}</span>
        </a>
        <div class="buttonHolderRight">
            <input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblSave|ucfirst}" />
        </div>
    </div>

    <div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
        <p>
            {$msgConfirmDelete|sprintf:{$item.title}}
        </p>
    </div>
{/form:edit}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
