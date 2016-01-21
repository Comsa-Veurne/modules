{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblAddresses|ucfirst}</h2>

    <div class="buttonHolderRight">
        <a href="{$var|geturl:'add_group'}" class="button icon iconAdd" title="{$lblAdd|ucfirst}">
            <span>{$lblAdd|ucfirst}</span>
        </a>
    </div>
</div>

{form:filter}
    <p class="oneLiner">
        <label for="group">{$msgShowOnlyGroupsUnderGroup}:</label>
        &nbsp;{$ddmGroup} {$ddmGroupError}
    </p>
{/form:filter}

{option:dataGrid}
    <div class="dataGridHolder">
        {$dataGrid}
    </div>
{/option:dataGrid}

{option:!dataGrid}
{$msgNoItems}
{/option:!dataGrid}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}