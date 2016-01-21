{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblCatalog|ucfirst}: {$lblAddCategory}</h2>
</div>

{form:addCategory}
    <div class="tabs">
        <ul>
            <li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
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

        <div id="tabSEO">
            {include:{$BACKEND_CORE_PATH}/Layout/Templates/Seo.tpl}
        </div>
    </div>
    <div class="fullwidthOptions">
        <div class="buttonHolderRight">
            <input id="addButton" class="inputButton button mainButton" type="submit" name="add"
                   value="{$lblAddCategory|ucfirst}"/>
        </div>
    </div>
{/form:addCategory}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}