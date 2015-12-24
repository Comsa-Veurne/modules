{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
	<h2>{$lblMailengine|ucfirst}: {$lblSettings}</h2>
</div>

{form:settings}
    <div class="box horizontal">
        <div class="heading">
            <h3>{$lblSettings|ucfirst}</h3>
        </div>
        <div class="options">
            <p>
                <label for="defaultGroup">{$lblDefaultGroupSubscribe|ucfirst}</label>
                {$ddmDefaultGroup} {$ddmDefaultGroupError}
            </p>
        </div>
    </div>

    <div class="fullwidthOptions">
        <div class="buttonHolderRight">
            <input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
        </div>
    </div>
{/form:settings}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}