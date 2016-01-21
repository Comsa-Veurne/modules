{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="pageTitle">
    <h2>{$lblGallery|ucfirst}: {$lblCategories|ucfirst}</h2>
    {option:showGalleryAddCategory}
        <div class="buttonHolderRight">
            <a href="{$var|geturl:'add_category'}"
               class="button icon iconAdd"><span>{$lblAddCategory|ucfirst}</span></a>
        </div>
    {/option:showGalleryAddCategory}
</div>
{option:dataGrid}
    <div class="dataGridHolder">
        {$dataGrid}
    </div>
{/option:dataGrid}
{option:!dataGrid}{$msgNoCategories|ucfirst}{/option:!dataGrid}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}