{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
	<h2>{$lblMailengine|ucfirst}: {$lblEdit}</h2>
</div>

{form:edit}
    <label for="title">{$lblSubject|ucfirst}</label>
{$txtSubject} {$txtSubjectError}

	<div id="pageUrl">
		<div class="oneLiner">
			{option:detailURL}<p><span><a target="_blank" href="{$detailURL}?id={$item.id}">{$detailURL}?id=<span id="generatedUrl">{$item.id}</span></a></span></p>{/option:detailURL}
			{option:!detailURL}<p class="infoMessage">{$errNoModuleLinked}</p>{/option:!detailURL}
		</div>
	</div>

	<div class="tabs">
		<ul>
			<li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
			<li><a href="#tabPreview">{$lblPreview|ucfirst}</a></li>
			<li><a href="#tabSEO">{$lblSEO|ucfirst}</a></li>
		</ul>

		<div id="tabContent">
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td id="leftColumn">

						{* Main content *}
						<div class="box">
							<div class="heading">
								<h3><abbr title="{$lblRequiredField}">*</abbr></h3>
							</div>
							<div class="optionsRTE">
                                {$txtText} {$txtTextError}
							</div>
                            <span class="helpTxt">{$msgMailUserVars}</span>
                        </div>

					</td>

					<td id="sidebar">
						<div id="publishOptions" class="box">
							<div class="heading">
								<h3>{$lblStatus|ucfirst}</h3>
							</div>

							<div class="options">
                                <ul class="inputList">
                                    {iteration:hidden}
                                        <li>
                                            {$hidden.rbtHidden}
                                            <label for="{$hidden.id}">{$hidden.label}</label>
                                        </li>
                                    {/iteration:hidden}
                                </ul>
							</div>
						</div>
                        <div id="publishOptions" class="box">
                            <div class="heading">
                                <h3>{$lblTemplate|ucfirst}</h3>
                            </div>

                            <div class="options">
                                {$ddmTemplateId} {$ddmTemplateIdError}
                            </div>
                        </div>

                        <div id="publishOptions" class="box">
                            <div class="heading">
                                <h3>{$lblShowOnWebsite|ucfirst}</h3>
                            </div>

                            <div class="options">
                                <ul class="inputList">
                                    {iteration:show_on_website}
                                        <li>
                                            {$show_on_website.rbtShowOnWebsite}
                                            <label for="{$show_on_website.id}">{$show_on_website.label}</label>
                                        </li>
                                    {/iteration:show_on_website}
                                </ul>
                            </div>
                        </div>
					</td>
				</tr>
			</table>
		</div>


        <div id="tabPreview">
            <iframe src="{$iframe}" style="width:800px;height:600px;border:0;"></iframe>
        </div>
		<div id="tabSEO">
			{include:{$BACKEND_CORE_PATH}/layout/templates/seo.tpl}
		</div>
	</div>

	<div class="fullwidthOptions">
		<a href="{$var|geturl:'delete'}&amp;id={$item.id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
			<span>{$lblDelete|ucfirst}</span>
		</a>
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblSave|ucfirst}" />
		</div>
	</div>

	<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
		<p>
			{$msgConfirmDelete|sprintf:{$item.subject}}
		</p>
	</div>
{/form:edit}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}