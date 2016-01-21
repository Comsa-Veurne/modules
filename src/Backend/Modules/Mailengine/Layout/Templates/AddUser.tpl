{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblMailengine|ucfirst} - {$lblUsers}: {$lblAdd}</h2>
</div>

{form:add}

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
    <div class="fullwidthOptions">
        <div class="buttonHolderRight">
            <input id="addButton" class="inputButton button mainButton" type="submit" name="add"
                   value="{$lblAdd|ucfirst}"/>
        </div>
    </div>
{/form:add}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}