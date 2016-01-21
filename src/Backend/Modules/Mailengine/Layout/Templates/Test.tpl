{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblMailengine|ucfirst} - {$lblTestEmail}</h2>
</div>


{form:add}
    <div id="tabContent">
        <table border="0" cellspacing="0" cellpadding="0" width="100%">
            <tr>
                <td id="leftColumn">

                    {* Main content *}
                    <div class="box">
                        <div class="option">
                            <div class="horizontal">
                                <p>{$msgMultipleEmail}</p>
                                <label for="email">{$lblEmail|ucfirst}</label>
                                {$txtEmail} {$txtEmailError}
                            </div>
                        </div>
                    </div>
                </td>

                <td id="sidebar">
                </td>
            </tr>
        </table>
    </div>
    <div class="fullwidthOptions">
        <div class="buttonHolderRight">
            <input id="addButton" class="inputButton button mainButton" type="submit" name="add"
                   value="{$lblSend|ucfirst}"/>
        </div>
    </div>
{/form:add}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}