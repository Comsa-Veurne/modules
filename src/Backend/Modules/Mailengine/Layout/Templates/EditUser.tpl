{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblMailengine|ucfirst} - {$lblUsers}: {$lblEdit}</h2>
</div>

{form:edit}

{* Main content *}
    <div class="box">

        <div class="heading">
            <h3>{$lblAddEmail}</h3>
        </div>
        <div class="options">
            <div class="horizontal">

                <label for="name">{$lblName|ucfirst}</label>
                {$txtName} {$txtNameError}
            </div>
            <div class="horizontal">

                <label for="email">{$lblEmail|ucfirst}</label>
                {$txtEmail} {$txtEmailError}
            </div>
        </div>
    </div>
{option:groups}
    <div class="box">
        <div class="heading">
            <h3>{$lblGroups|ucfirst}</h3>
        </div>
        <div class="options">
            <ul class="inputList">
                {iteration:groups}
                    <li>{$groups.chkGroups}
                    <label for="{$groups.id}">{$groups.label|ucfirst}</label>
                    </li>{/iteration:groups}
            </ul>
            {$chkGroupsError}
        </div>
    </div>
{/option:groups}
    <div class="box">
        <div class="heading">
            <h3>{$lblStatus|ucfirst}</h3>
        </div>

        <div class="options">
            <ul class="inputList">
                {iteration:active}
                    <li>
                        {$active.rbtActive}
                        <label for="{$active.id}">{$active.label}</label>
                    </li>
                {/iteration:active}
            </ul>
            {option:item.unsubscribe_on}
            {$lblUnsubscribedOn|ucfirst} {$item.unsubscribe_on}
            {/option:item.unsubscribe_on}
        </div>
    </div>
    <div class="fullwidthOptions">
        <a href="{$var|geturl:'delete_user'}&amp;id={$item.id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
            <span>{$lblDelete|ucfirst}</span>
        </a>

        <div class="buttonHolderRight">
            <input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblSave|ucfirst}"/>
        </div>
    </div>
    <div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
        <p>
            {$msgConfirmDelete|sprintf:{$item.email}}
        </p>
    </div>
{/form:edit}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}