{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblCatalog|ucfirst}: {$msgEditCategory|sprintf:{$item.title}}</h2>
</div>

{form:editCategory}
    <div class="tabs">
        <ul>
            <li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
            <li><a href="#tabMedia">{$lblMedia|ucfirst}</a></li>
            <li><a href="#tabSEO">{$lblSEO|ucfirst}</a></li>
        </ul>

        <div id="tabContent">
            <div class="box">
                <div class="heading">
                    <h3><label for="title">{$lblTitle|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label></h3>
                </div>
                <div class="options">
                    {*$txtTitle} {$txtTitleError*}
                    {iteration:fieldLanguages}
                        <label for="text_{$fieldLanguages.key}">{$fieldLanguages.language|ucfirst}</label>
                    {$fieldLanguages.title}
                        <br/>
                        <br/>
                    {/iteration:fieldLanguages}
                </div>
                {iteration:fieldLanguages}
                    <div class="options horizontal">
                        <label for="text_{$fieldLanguages.key}">{$lblBallText|ucfirst} {$fieldLanguages.language|ucfirst}</label>
                        {$fieldLanguages.balltext}
                    </div>
                {/iteration:fieldLanguages}
                <div class="options horizontal">
                    <label for="balltext">{$lblBallColor|ucfirst}</label>
                    {$ddmBallcolor} {$ddmBallcolorError}
                </div>
                <div class="options">
                    {option:item.image}
                        <p class="image">
                        <p><img src="{$FRONTEND_FILES_URL}/Catalog/categories/150x150/{$item.image}"/></p>
                        <label for="deleteImage">{$chkDeleteImage} {$lblDelete|ucfirst}</label>
                    {$chkDeleteImageError}
                        </p>
                    {/option:item.image}
                    <label for="image">{$lblImage|ucfirst}</label>
                    {$fileImage} {$fileImageError}
                </div>
                <div class="options">
                    <label for="category">{$lblInCategory|ucfirst}</label>
                    {$ddmParentId} {$ddmParentIdError}
                </div>
            </div>
            <div class="box">
                {iteration:fieldLanguages}
                    <div class="heading">
                        <h3>
                            <label for="text_{$fieldLanguages.key}">{$lblDescription|ucfirst} {$fieldLanguages.language|ucfirst}</label><br/>
                        </h3>
                    </div>
                    <div class="optionsRTE">
                        {$fieldLanguages.description}
                    </div>
                {/iteration:fieldLanguages}
            </div>

            <div class="box">
                {iteration:fieldLanguages}
                    <div class="heading">
                        <h3>
                            <label for="text_{$fieldLanguages.key}">{$lblSummary|ucfirst} {$fieldLanguages.language|ucfirst}</label><br/>
                        </h3>
                    </div>
                    <div class="optionsRTE">
                        {$fieldLanguages.summary}
                    </div>
                {/iteration:fieldLanguages}
            </div>
        </div>
        <div id="tabMedia">
            {include:{$BACKEND_MODULES_PATH}/Media/Layout/Templates/Media.tpl}
        </div>
        <div id="tabSEO">
            {include:{$BACKEND_CORE_PATH}/Layout/Templates/Seo.tpl}
        </div>
    </div>
    <div class="fullwidthOptions">
        {option:showDelete}
            <a href="{$var|geturl:'delete_category'}&amp;id={$item.id}" data-message-id="confirmDelete"
               class="askConfirmation button linkButton icon iconDelete">
                <span>{$lblDelete|ucfirst}</span>
            </a>
            <div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
                <p>
                    {$msgConfirmDeleteCategory|sprintf:{$item.title}}
                </p>
            </div>
        {/option:showDelete}

        <div class="buttonHolderRight">
            <input id="editButton" class="inputButton button mainButton" type="submit" name="edit"
                   value="{$lblSave|ucfirst}"/>
        </div>
    </div>
{/form:editCategory}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
