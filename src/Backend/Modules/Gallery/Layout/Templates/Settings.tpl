{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblModuleSettings|ucfirst}: {$lblGallery}</h2>
</div>

{form:settings}
    <div class="box">
        <div class="heading">
            <h3>{$lblGeneralSettings|ucfirst}</h3>
        </div>
        <div class="options">
            <label for="rssTitle">{$lblResolutions|ucfirst} (e.g. 200x100,300x200,300x,400x,500x400)</label>
            {$txtResolutions} {$txtResolutionsError}
        </div>
        <div class="options">
            <ul class="inputList">
                <li>
                    <label for="gallery">{$lblGallery|ucfirst}</label>{$chkGallery}
                </li>
                <li>
                    <label for="slideshow">{$lblSlideshow|ucfirst}</label>{$chkSlideshow}
                </li>
            </ul>
        </div>
    </div>
    <div class="box">
        <div class="horizontal"></div>
    </div>
    <div class="fullwidthOptions">
        <div class="buttonHolderRight">
            <input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}"/>
        </div>
    </div>
{/form:settings}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}