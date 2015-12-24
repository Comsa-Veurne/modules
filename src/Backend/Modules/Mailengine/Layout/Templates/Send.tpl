{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblMailengine|ucfirst} - {$lblSend}</h2>
</div>

<h3>{$item.subject}</h3>


{option:form_preview}
{form:send_preview}
<table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr>
        <td id="leftColumn">

            {* Main content *}

            <div class="box">
                <div class="heading">
                    <h3>{$lblGroups|ucfirst}</h3>
                </div>
                <div class="options">
                    <p>{$msgChooseGroups}</p>
                    {option:groups}
                    <ul class="inputList">
                        {iteration:groups}
                        <li>{$groups.chkGroups}
                            <label for="{$groups.id}">{$groups.label|ucfirst}</label>
                        </li>
                        {/iteration:groups}
                    </ul>
                    {/option:groups}
                    {$chkGroupsError}
                </div>
            </div>

            {option:profilesCount}
            <div class="box">
                <div class="heading">
                    <h3>{$lblProfileGroups|ucfirst}</h3>
                </div>
                <div class="options">

                    <label for="profilesAll">{$chkProfilesAll}{$lblAllActiveProfiles} ({$profilesCount})</label>
                </div>
                {option:profileGroups}
                <div class="options profile-groups">
                    <p>{$msgChooseProfileGroups}</p>
                    <ul class="inputList">
                        {iteration:profileGroups}
                        <li>{$profileGroups.chkProfileGroups}
                            <label for="{$profileGroups.id}">{$profileGroups.label|ucfirst}</label>
                        </li>
                        {/iteration:profileGroups}
                    </ul>
                    {$chkProfileGroupsError}
                </div>
                {/option:profileGroups}
            </div>
            {/option:profilesCount}

            <div class="box">
                <div class="heading">
                    <h3>{$lblDate|ucfirst}</h3>
                </div>
                <div class="options">
                    <div class="oneLiner">
                        <p class="firstChild">
                            <label for="startDate">{$lblStartDate|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
                            {$txtStartDate}
                        </p>

                        <p>
                            <label for="startTime">{$lblAt}</label>
                            {$txtStartTime}
                        </p>
                        {$txtStartDateError} {$txtStartTimeError}
                    </div>
                </div>
            </div>
        </td>

        <td id="sidebar">
        </td>
    </tr>
</table>
<div class="fullwidthOptions">
    <div class="buttonHolderLeft">
        <input id="addButton" class="inputButton button mainButton" type="submit" name="send_preview"
               value="{$lblSend|ucfirst}"/>
    </div>
</div>
{/form:send_preview}
{/option:form_preview}


{option:form_review}
{form:send_review}
<table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr>
        <td id="leftColumn">

            {* Main content *}
            <div class="box">

                {* Main content *}
                <div class="box">

                    <div class="option">
                        <p>{$msgMessageWillBeSent|sprintf:{$countUsers},{$labelUsers}}</p>
                        {option:errorUsers}
                        <p class="errorMessage" style="padding-top:10px;padding-left:10px;margin-bottom: 10px;">
                            {$errNoUsers}</p>

                        <p>
                            <a href="{$back}">{$lblBack|ucfirst}</a>
                        </p>
                        {/option:errorUsers}

                        {$hidProfilesAll}
                        {$hidProfileGroups}
                        {$hidGroups}
                        {$hidStartDate}
                        {$hidStartTime}
                    </div>
                </div>
            </div>
        </td>

        <td id="sidebar">

        </td>
    </tr>
</table>
<div class="fullwidthOptions">
    {option:!errorUsers}
    <div class="buttonHolderLeft">
        <input id="addButton" class="inputButton button mainButton" type="submit" name="send_review"
               value="{$lblSend|ucfirst}"/>
    </div>
    {/option:!errorUsers}
</div>
{/form:send_review}
{/option:form_review}


{option:ready}
<div>
    <p>{$msgMailingReadyToSend}</p>
</div>
{/option:ready}


{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}