{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>
        {$lblAddresses|ucfirst} - {$lblUpdateLatLng}
    </h2>
</div>
<div class="box">
    <label for="">{$lblAddressesWithoutLatLng}: </label>
    <strong>{$number_of_addressess}</strong>
</div>

{option:response}
    <div class="box">
        <div class="heading">
            <h3>{$lblAddressesChanged}</h3>
        </div>
        <div class="options">
            {$response}
        </div>
    </div>
{/option:response}

{option:responseError}
    <div class="box">
        <div class="heading">
            <h3>{$lblAddressesChangedError}</h3>
        </div>
        <div class="options">
            {$responseError}
        </div>
    </div>
{/option:responseError}

{form:update}
    <div class="box">
        <div class="heading">
            <h3>{$lblUpdate|ucfirst}</h3>
        </div>
        <div class="options">
            <label for="numberOfItems">{$lblNumberOfItemsToUpdate|ucfirst}: </label>
            {$ddmNumberOfItems} {$ddmNumberOfItemsError}
        </div>
    </div>
    <div class="fullwidthOptions">
        <div class="buttonHolderLeft">
            <input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblAddLatLng|ucfirst}"/>
        </div>
    </div>
{/form:update}


{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}