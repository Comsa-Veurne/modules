<div>
    <div class="box">
        <div class="heading">
            <h3>{$lblUploadImage}</h3>
        </div>
        <div class="box">
            <div id="uploader">
                <p>Your browser doesn't have Flash, Silverlight or HTML5 support.</p>
            </div>
        </div>
    </div>

    <div class="buttonHolderRight clearfix" style="margin: 10px 0;">
        <a href="" id="addLink" class="button icon iconAdd"><span>{$lblAddLink|ucfirst}</span></a>
    </div>

    <div class="box">
        <div class="heading">
            <h3>{$lblImages|ucfirst}</h3>
        </div>
        <div class="box">
            <ul class="media images">
                {option:mediaItems.images}
                    {include:{$BACKEND_MODULES_PATH}/Media/Layout/Templates/Ajax/Image.tpl}
                {/option:mediaItems.images}
            </ul>
        </div>
    </div>
    <div class="clearfix">&nbsp;</div>
    <div class="box">
        <div class="heading">
            <h3>{$lblFiles|ucfirst}</h3>
        </div>
        <div class="box">
            <ul class="media files">
                {option:mediaItems.files}
                    {include:{$BACKEND_MODULES_PATH}/Media/Layout/Templates/Ajax/File.tpl}
                {/option:mediaItems.files}
            </ul>
        </div>
    </div>

    <div class="clearfix">&nbsp;</div>
    <div>
        <span class="deleteCheck">
            <input type="checkbox" id="all-images" name="all-images"/>
            <label for="all-images">{$lblSelectedAll}</label>
        </span>
        <a href="" class="deleteSelected button icon iconDelete" data-message-id="confirmDeleteSelected">{$lblDeleteSelected|ucfirst}</a>
        <div id="confirmDeleteSelected" title="{$lblDelete|ucfirst}?" style="display: none;">
            <p>
                {$lblDeleteSelectedFiles|ucfirst}
            </p>
        </div>
    </div>
</div>

<div id="dialog" title="">
    <div class="loader">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
    </div>
</div>

<script type="text/javascript" src="/src/Backend/Modules/Media/Js/moxie.min.js"></script>
<script type="text/javascript" src="/src/Backend/Modules/Media/Js/plupload.full.min.js"></script>
<script type="text/javascript" src="/src/Backend/Modules/Media/Js/jquery.ui.plupload/jquery.ui.plupload.js"></script>
<script type="text/javascript" src="/src/Backend/Modules/Media/Js/i18n/nl.js"></script>
<script type="text/javascript" src="/src/Backend/Modules/Media/Js/Media.js"></script>

<link rel="stylesheet" href="/src/Backend/Modules/Media/Layout/Css/jquery.ui.plupload/jquery-ui.css"/>
<link rel="stylesheet" href="/src/Backend/Modules/Media/Layout/Css/jquery.ui.plupload/jquery.ui.plupload.css"/>
<link rel="stylesheet" href="/src/Backend/Modules/Media/Layout/Css/Media.css"/>