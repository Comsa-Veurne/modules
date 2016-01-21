{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
	<h2>{$lblMailengine|ucfirst}: {$lblEdit}</h2>
</div>

{form:edit}



	<div class="tabs">
		<ul>
			<li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
            {option:users}
			<li><a href="#tabUsers">{$lblUsers|ucfirst}</a></li>
            {/option:users}
		</ul>

		<div id="tabContent">
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td id="leftColumn">

                        {* Main content *}
                        <div class="box">
                            <div class="option">
                                <div class="horizontal">
                                    <label for="title">{$lblTitle|ucfirst}</label>
                                    {$txtTitle} {$txtTitleError}
                                </div>
                            </div>
                        </div>
					</td>

					<td id="sidebar"></td>
				</tr>
			</table>
		</div>
        {option:users}
        <div id="tabUsers">
            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td id="leftColumn">

                        {* Main content *}
                        <div class="box">

                            <div class="option">
                                    <ul class="inputList">
                                        {iteration:users}<li>{$users.chkUsers} <label for="{$users.id}">{$users.label|ucfirst}</label></li>{/iteration:users}
                                    </ul>
                                {$chkUsersError}
                            </div>
                        </div>

                    </td>

                    <td id="sidebar"></td>
                </tr>
            </table>
        </div>
        {/option:users}
    </div>

	<div class="fullwidthOptions">
		<a href="{$var|geturl:'delete_group'}&amp;id={$item.id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
			<span>{$lblDelete|ucfirst}</span>
		</a>
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblSave|ucfirst}" />
		</div>
	</div>

	<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
		<p>
			{$msgConfirmDelete|sprintf:{$item.title}}
		</p>
	</div>
{/form:edit}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}