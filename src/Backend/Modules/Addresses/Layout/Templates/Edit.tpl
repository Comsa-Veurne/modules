{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblAddresses|ucfirst}: {$lblEdit}</h2>
</div>

{form:edit}
    <div id="pageUrl">
        <div class="oneLiner">
            {option:detailURL}<p><span><a href="{$detailURL}/{$item.url}">{$detailURL}/<span
                                id="generatedUrl">{$item.url}</span></a></span></p>{/option:detailURL}
            {option:!detailURL}<p class="infoMessage">{$errNoModuleLinked}</p>{/option:!detailURL}
        </div>
    </div>
    <div class="tabs">
        <ul>
            <li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
            <li><a href="#tabSEO">{$lblSEO|ucfirst}</a></li>
        </ul>

        <div id="tabContent">
            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td id="leftColumn">

                        {* Main content *}
                        <div class="box">
                            <p>

                                <label for="firstname">{$lblCompany|ucfirst}</label>
                                {$txtCompany} {$txtCompanyError}

                                <label for="name">{$lblName|ucfirst}</label>
                                {$txtName} {$txtNameError}

                                <label for="firstname">{$lblFirstname|ucfirst}</label>
                                {$txtFirstname} {$txtFirstnameError}

                                <label for="email">{$lblEmail|ucfirst}</label>
                                {$txtEmail} {$txtEmailError}

                                <label for="address">{$lblAddress|ucfirst}</label>
                                {$txtAddress} {$txtAddressError}

                                <label for="zipcode">{$lblZipcode|ucfirst}</label>
                                {$txtZipcode} {$txtZipcodeError}

                                <label for="city">{$lblCity|ucfirst} </label>
                                {$txtCity} {$txtCityError}

                                <label for="country">{$lblCountry|ucfirst} </label>
                                {$ddmCountry} {$ddmCountryError}

                                <label for="phone">{$lblPhone|ucfirst} </label>
                                {$txtPhone} {$txtPhoneError}

                                <label for="fax">{$lblFax|ucfirst} </label>
                                {$txtFax} {$txtFaxError}

                                <label for="website">{$lblWebsite|ucfirst} </label>
                                {$txtWebsite} {$txtWebsiteError}

                                <label for="vat">{$lblVat|ucfirst} </label>
                                {$txtVat} {$txtVatError}

                                <label for="remark">{$lblRemark|ucfirst} </label>
                                {$txtRemark} {$txtRemarkError}

                                {*<label for="assort">{$lblAssortiment|ucfirst} </label>
                                {$txtAssort} {$txtAssortError}

                                <label for="assort">{$lblOpen|ucfirst} </label>
                                {$txtOpen} {$txtOpenError}

                                <label for="assort">{$lblClosed|ucfirst} </label>
                                {$txtClosed} {$txtClosedError}

                                <label for="assort">{$lblVisit|ucfirst} </label>
                                {$txtVisit} {$txtVisitError}

                                <label for="assort">{$lblSize|ucfirst} </label>
                                {$txtSize} {$txtSizeError}*}

                                {* <label for="assort">{$lblZipcodes|ucfirst} </label>
                                 {$txtZipcodes} {$txtZipcodesError}*}
                            </p>
                        </div>

                        {*<div class="box">

                            <div class="heading">
                                <h3>{$lblText|ucfirst}</h3>
                            </div>
                            <div class="optionsRTE">
                                {$txtText} {$txtTextError}
                            </div>
                        </div>*}

                        <div class="box">
                            <div class="heading">
                                <h3>{$lblImage|ucfirst}</h3>
                            </div>
                            <div class="options clearfix">
                                {option:item.image}
                                    <p class="imageHolder">
                                        <img src="{$FRONTEND_FILES_URL}/addresses/images/128x128/{$item.image}"
                                             width="128" height="128" alt="{$lblImage|ucfirst}"/>
                                        <label for="deleteImage">{$chkDeleteImage} {$lblDelete|ucfirst}</label>
                                        {$chkDeleteImageError}
                                    </p>
                                {/option:item.image}
                                <p>
                                    <label for="image">{$lblImage|ucfirst}</label>
                                    {$fileImage} {$fileImageError}
                                </p>
                            </div>
                        </div>

                        <div class="box">

                            {iteration:fieldLanguages}
                                <div class="heading">
                                    <h3>
                                        <label for="text_{$fieldLanguages.key}">{$lblText|ucfirst} {$fieldLanguages.language|ucfirst}</label><br/>
                                    </h3>
                                </div>
                                <div class="optionsRTE">
                                    {$fieldLanguages.text}
                                </div>
                            {/iteration:fieldLanguages}

                        </div>

                        {* <div class="box">
                             {iteration:fieldLanguages}
                                 <div class="heading">
                                     <h3>
                                         <label>{$lblOpeningHours|ucfirst} {$fieldLanguages.language|ucfirst}</label>
                                     </h3>
                                 </div>
                                 <div class="optionsRTE">
                                     *}{*<label for="description_{$fieldLanguages.key}">{$fieldLanguages.language|ucfirst}</label><br/>*}{*
                                    {$fieldLanguages.opening_hours}
                                </div>
                            {/iteration:fieldLanguages}
                        </div>*}

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
                        <div class="box">
                            <div class="heading">
                                <h3>{$lblGroups|ucfirst}</h3>
                            </div>
                            <div class="options">
                                {option:groups}
                                {$groups2|funk:'Backend\Modules\Addresses\Actions\Edit':'CreateMultipleCheckboxes':{$groups2selected}}
                                    {*
                                                                        <ul class="inputList">
                                                                            {iteration:groups}
                                                                                <li>{$groups.chkGroups}
                                                                                <label for="{$groups.id}">{$groups.label|ucfirst}</label>
                                                                                </li>{/iteration:groups}
                                                                        </ul>*}
                                {$chkGroupsError}
                                {/option:groups}
                                {option:!groups}
                                {$msgNoGroups}
                                {/option:!groups}

                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div id="tabSEO">
            {include:{$BACKEND_CORE_PATH}/layout/templates/seo.tpl}
        </div>
    </div>
    <div class="fullwidthOptions">
        <a href="{$var|geturl:'delete'}&amp;id={$item.id}" data-message-id="confirmDelete"
           class="askConfirmation button linkButton icon iconDelete">
            <span>{$lblDelete|ucfirst}</span>
        </a>
        <div class="buttonHolderRight">
            <input id="addButton" class="inputButton button mainButton" type="submit" name="add"
                   value="{$lblSave|ucfirst}"/>
        </div>
    </div>
    <div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
        <p>
            {$msgConfirmDelete|sprintf:{$item.name}}
        </p>
    </div>
{/form:edit}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}