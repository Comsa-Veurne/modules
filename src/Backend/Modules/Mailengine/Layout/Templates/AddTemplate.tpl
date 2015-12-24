{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblMailengine|ucfirst}: {$lblAdd}</h2>
</div>

{form:add}
    <label for="title">{$lblTitle|ucfirst}</label>
{$txtTitle} {$txtTitleError}
    <div class="tabs">
        <ul>
            <li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
        </ul>

        <div id="tabContent">
            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td id="leftColumn">

                        {* Main content *}
                        <div class="box">

                            <p>
                                <label for="title">{$lblFrom|ucfirst} {$lblEmail|ucfirst}
                                    <abbr title="{$lblRequiredField}">*</abbr></label>
                                {$txtFromEmail} {$txtFromEmailError}

                                <label for="title">{$lblFrom|ucfirst} {$lblName|ucfirst}
                                    <abbr title="{$lblRequiredField}">*</abbr></label>
                                {$txtFromName} {$txtFromNameError}

                                <label for="title">{$lblReply|ucfirst} {$lblEmail|ucfirst}
                                    <abbr title="{$lblRequiredField}">*</abbr></label>
                                {$txtReplyEmail} {$txtReplyEmailError}

                                <label for="title">{$lblReply|ucfirst} {$lblName|ucfirst}
                                    <abbr title="{$lblRequiredField}">*</abbr></label>
                                {$txtReplyName} {$txtReplyNameError}
                            </p>
                        </div>
                        <div class="box">
                            <div class="heading">
                                <h3>{$lblTemplate|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></h3>
                            </div>
                            <div class="optionsRTE">
                                {$txtTemplate} {$txtTemplateError}
                            </div>
                            <span class="helpTxt">{$msgMailSquares}</span>
                        </div>
                        <div class="box">
                            <div class="heading">
                                <h3>{$lblCss|ucfirst}</h3>
                            </div>
                            <div class="options">
                                {$txtCss} {$txtCssError}
                            </div>
                        </div>

                    </td>

                    <td id="sidebar">
                        <div id="publishOptions" class="box">
                            <div class="heading">
                                <h3>{$lblStatus|ucfirst}</h3>
                            </div>

                            <div class="options">
                                <ul class="inputList">
                                    {iteration:hidden}
                                        <li>
                                            {$hidden.rbtHidden}
                                            <label for="{$hidden.id}">{$hidden.label}</label>
                                        </li>
                                    {/iteration:hidden}
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

    </div>
    <div class="fullwidthOptions">
        <div class="buttonHolderRight">
            <input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblSave|ucfirst}"/>
        </div>
    </div>
{/form:add}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}