{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>
        {$lblMailengine|ucfirst} {$lblUsers}
    </h2>

    <div class="buttonHolderRight">
        <a href="{$var|geturl:'add_user'}" class="button icon iconAdd" title="{$lblAddUser|ucfirst}">
            <span>{$lblAddUser|ucfirst}</span>
        </a>
        <a href="{$var|geturl:'import_users'}" class="button icon iconImport"><span>{$lblImportUsers|ucfirst}</span></a>
        <a href="{$var|geturl:'export_users'}&id={$id}" class="button icon iconExport"><span>{$lblExportUsers|ucfirst}</span></a>
    </div>
</div>

{form:filter}
    <div class="dataFilter">
        <table>
            <tbody>
            <tr>
                <td>
                    <div class="options">
                        <p>
                            <label for="email">{$lblEmail|ucfirst}</label>
                            {$txtEmail} {$txtEmailError}
                        </p>
                    </div>
                </td>
                <td>
                    <div class="options">
                        <p>
                            <label for="status">{$lblName|ucfirst}</label>
                            {$txtName} {$txtNameError}
                        </p>
                    </div>
                </td>
                {option:ddmGroup}
                    <td>
                        <div class="options">
                            <p>
                                <label for="group">{$lblGroup|ucfirst}</label>
                                {$ddmGroup} {$ddmGroupError}
                            </p>
                        </div>
                    </td>
                {/option:ddmGroup}
            </tr>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="99">
                    <div class="options">
                        <div class="buttonHolder">
                            <input id="search" class="inputButton button mainButton" type="submit" name="search"
                                   value="{$lblUpdateFilter|ucfirst}"/>
                        </div>
                    </div>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
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