{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblMailengine|ucfirst} - {$lblImportEmails}</h2>
</div>
{option:!hideForm}
{form:import}
    <div class="box">

        <div class="heading">
            <h3>{$lblFileEmails|ucfirst}</h3>
        </div>

        <div class="options">

            {$fileCsv} {$fileCsvError}
            <label for="download">Download
                <a href="{$var|geturl:'export_demo'}">{$lblExampleFile}</a>.</label>

        </div>
    </div>
    <div class="box">

        <div class="heading">
            <h3>{$lblGroups|ucfirst}</h3>
        </div>

        <div class="options">

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
    <div class="box">

        <div class="heading">
            <h3>{$lblLanguage|ucfirst}</h3>
        </div>

        <div class="options">

            {$ddmLanguages}
            {$ddmLanguagesError}

        </div>
    </div>
    <div class="fullwidthOptions">
        <div class="buttonHolderRight">
            <input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblImportEmails|ucfirst}"/>
        </div>
    </div>
{/form:import}
{/option:!hideForm}
{option:return}
    <div class="box">
        <div class="heading">
            <h3>{$lblImportSucceeded|ucfirst}</h3>
        </div>
        <div class="options">

            <label><strong>{$lblEmailsAdded|ucfirst}:</strong></label>
            {$return.successInserted}<br/><br/>
            <label><strong>{$lblInvalidEmails|ucfirst}:</strong></label>
            {$return.errorEmail}<br/><br/>
            <label><strong>
                    {$lblEmailsAlreadyExists|ucfirst}:</strong></label>
            {$return.errorAlreadyExists}<br/><br/>


            <a href="{$var|geturl:'import_users'}" class=""><span>{$lblImportNewFile|ucfirst}</span></a>
        </div>

    </div>
{/option:return}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}