{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblCatalog|ucfirst}: {$lblCategories|ucfirst}</h2>

    <div class="buttonHolderRight">
        <a href="{$var|geturl:'add_category'}" class="button icon iconAdd"><span>{$lblAddCategory|ucfirst}</span></a>
    </div>
</div>

{form:filter}
    <p class="oneLiner">
        <label for="category">{$msgShowOnlyCategoriesUnderCategory}</label>
        &nbsp;{$ddmCategory} {$ddmCategoryError}
    </p>
{/form:filter}

{option:dataGrid}
    <div class="dataGridHolder">
        {$dataGrid}
    </div>
{/option:dataGrid}

{option:!dataGrid}
{$msgNoCategories|ucfirst}
{/option:!dataGrid}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}