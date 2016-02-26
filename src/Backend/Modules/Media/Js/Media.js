/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Interaction for the media module
 *
 * @author Waldo Cosman <waldo@comsa.be>
 * @author Nick Vandevenne <nick@comsa.be>
 */
jsBackend.Media =
{
    // constructor
    init: function () {
        //--Initialise uploadify
        //jsBackend.Media.uploadify();
        jsBackend.Media.bindSortable();
        jsBackend.Media.bindDeleteFile();
        jsBackend.Media.bindDeleteFiles();
        jsBackend.Media.bindRenameFile();
        jsBackend.Media.plupload();

        $('#all-images').click(function () {
            if ($(this).prop('checked') == true) {
                $('input[class^=check]').prop('checked', true);
            }
            else {
                $('input[class^=check]').prop('checked', false);
            }
        })

        var wWidth = $(window).width();
        var dWidth = wWidth * 0.8;
        var wHeight = $(window).height();
        var dHeight = wHeight * 0.8;

        $("#dialog").dialog({
            autoOpen: false,
            show: {
                effect: "fade",
                duration: 300
            },
            hide: {
                effect: "fade",
                duration: 300
            },
            width: dWidth,
            height: dHeight,
            open: function (event, ui) {
                $("body").css({
                    overflow: 'hidden'
                });
                $(".ui-widget-overlay").css({
                    background: "rgb(0, 0, 0)",
                    opacity: ".50 !important",
                    filter: "Alpha(Opacity=50)",
                });
            },
            beforeClose: function (event, ui) {
                $("#encloser").removeClass('ui-widget-overlay')
                $("body").css({overflow: 'inherit'})
            }
        });

        $('#addLink').click(
            function (e) {
                e.preventDefault();
                $("#dialog").dialog("open");

                $("#encloser").addClass('ui-widget-overlay')
                $("#dialog").html("<div class='loader' style='margin-top: 50px'><span></span><span></span><span></span><span></span><span></span></div>");
                $.ajax(
                    {
                        data: {
                            fork: {action: 'GetLink', module: 'Media'},
                        },
                        success: function (data, textStatus) {
                            //--Check if the response is correct
                            if (data.code == 200) {
                                $("#dialog").html('');
                                $("#dialog").append(data.data);

                                $("#linkSearch").keyup(function () {

                                    var that = this, $allListElements = $("#dialog").find('ul > li');

                                    var $matchingListElements = $allListElements.filter(function (i, li) {
                                        var listItemText = $(li).text().toUpperCase(), searchText = that.value.toUpperCase();
                                        return ~listItemText.indexOf(searchText);
                                    });

                                    $allListElements.hide();
                                    $matchingListElements.show();

                                });

                                $('#addLinks').click(
                                    function (e)				// on stop sorting
                                    {
                                        e.preventDefault();

                                        var ids = [];
                                        var lis = $('.checkLink:checked');
                                        lis.each(function () {
                                            ids.push(this.id);
                                        });
                                        $.ajax(
                                            {
                                                data: {
                                                    fork: {action: 'AddLinks', module: 'Media'},
                                                    ids: ids,
                                                    'mediaModule': jsData.Media.mediaModule,
                                                    'mediaAction': jsData.Media.mediaAction,
                                                    'mediaId': jsData.Media.mediaId,
                                                    'mediaType': jsData.Media.mediaType
                                                },
                                                success: function (data, textStatus) {
                                                    //--Check if the response is correct
                                                    if (data.code == 200) {
                                                        // Called when all files are either uploaded or failed
                                                        //--Generate random number for the refresh
                                                        var randomNumber = Math.floor(Math.random() * 11)

                                                        //--Delete #-identifier
                                                        var strLocation = window.location.href.substr(0, window.location.href.indexOf('#'));

                                                        //--Redirect page with randomnumber + #tabMedia identifier
                                                        window.location.replace(strLocation + "&random=" + randomNumber + "#tabMedia");
                                                    }

                                                    //--If there is an error, alert the message
                                                    if (data.code != 200 && jsBackend.debug) {
                                                        alert(data.message);
                                                    }

                                                },
                                                error: function (XMLHttpRequest, textStatus, errorThrown) {
                                                    // revert
                                                    $(this).sortable('cancel');

                                                    // show message
                                                    jsBackend.messages.add('error', 'adding files failed.');

                                                    // alert the user
                                                    if (jsBackend.debug) {
                                                        alert(textStatus);
                                                    }
                                                }
                                            })
                                    }
                                );
                            }

                            //--If there is an error, alert the message
                            if (data.code != 200 && jsBackend.debug) {
                                alert(data.message);
                            }
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown) {
                            // revert
                            $(this).sortable('cancel');

                            // show message
                            jsBackend.messages.add('error', 'get link failed.');

                            // alert the user
                            if (jsBackend.debug) {
                                alert(textStatus);
                            }
                        }
                    });
            }
        );

        $('#addVideo').click(
            function (e) {
                e.preventDefault();

                $.ajax(
                    {
                        data: {
                            fork: {action: 'AddVideo', module: 'Media'},
                            'video':$('#video').val(),
                            'mediaModule': jsData.Media.mediaModule,
                            'mediaAction': jsData.Media.mediaAction,
                            'mediaId': jsData.Media.mediaId,
                            'mediaType': jsData.Media.mediaType
                        },
                        success: function (resp, textStatus) {
                            //--Check if the response is correct
                            if (resp.code == 200) {
                                $('#video').val('');

                                var data = resp.data;

                                if (data[0] == 3) {
                                    $('.videos').append(data[1]);
                                }

                                var id = $(data[1]).attr('id');

                                $('#' + id).find('.delete').click(
                                    function (e)				// on stop sorting
                                    {
                                        e.preventDefault();
                                        deleteF(this);
                                    });

                                $('#' + id).find('.filename').click(
                                    function (e) {
                                        e.preventDefault();
                                        rename(this);
                                    }
                                );

                                // Called when all files are either uploaded or failed
                                //--Generate random number for the refresh
                                //var randomNumber = Math.floor(Math.random() * 11)
                            }

                            //--If there is an error, alert the message
                            if (resp.code != 200 && jsBackend.debug) {
                                alert(data.message);
                            }

                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown) {
                            // revert
                            $(this).sortable('cancel');

                            // show message
                            jsBackend.messages.add('error', 'adding video failed.');

                            // alert the user
                            if (jsBackend.debug) {
                                alert(textStatus);
                            }
                        }
                    })
            }
        );
    },
    uploadify: function () {
        $('#images').uploadify({
            'swf': '/src/Backend/Modules/Media/Swf/uploadify.swf',
            'buttonText': jsBackend.locale.lbl('ChooseImages'),
            'width': 218,
            'height': 24,
            'uploader': '/backend/ajax',
            'formData': {
                'fork[module]': "Media",
                'fork[action]': "Uploadify",
                'fork[language]': jsBackend.current.language,
                'mediaModule': jsData.Media.mediaModule,
                'mediaAction': jsData.Media.mediaAction,
                'mediaId': jsData.Media.mediaId,
                'mediaType': jsData.Media.mediaType
            },
            'fileObjName': 'images',
            'onQueueComplete': function (queueData) {
                //--Generate random number for the refresh
                var randomNumber = Math.floor(Math.random() * 11)

                //--Delete #-identifier
                var strLocation = window.location.href.substr(0, window.location.href.indexOf('#'));

                //--Redirect page with randomnumber + #tabMedia identifier
                window.location.replace(strLocation + "&random=" + randomNumber + "#tabMedia");
            }
        });
    },
    bindSortable: function () {
        //--Add sortable to the galleria-lists
        $('ul.media').sortable(
            {
                handle: 'img',
                tolerance: 'pointer',
                stop: function (e, ui)				// on stop sorting
                {
                    var arrIds = Array();

                    //--Loop the children
                    $(this).children('li').each(function (index, element) {
                        //--Get the id from the element and push it into an array
                        arrIds.push($(element).attr('id').substr(3));
                    });

                    //--Create a string of the array with a , delimeter.
                    var strIds = arrIds.join(',');

                    //--Create ajax-call
                    $.ajax(
                        {
                            data: {
                                fork: {action: 'MediaSequence', module: 'Media'},
                                ids: strIds
                            },
                            success: function (data, textStatus) {
                                //--Check if the response is correct
                                if (data.code == 200) {
                                    jsBackend.messages.add('success', jsBackend.locale.msg('SequenceSaved'));
                                }

                                //--If there is an error, alert the message
                                if (data.code != 200 && jsBackend.debug) {
                                    alert(data.message);
                                }

                            },
                            error: function (XMLHttpRequest, textStatus, errorThrown) {
                                // revert
                                $(this).sortable('cancel');

                                // show message
                                jsBackend.messages.add('error', 'alter sequence failed.');

                                // alert the user
                                if (jsBackend.debug) {
                                    alert(textStatus);
                                }
                            }
                        });
                }
            });
    },
    plupload: function () {
        var uploader = $("#uploader").plupload({
            // General settings
            runtimes: 'html5,flash,html4',
            url: "/backend/ajax",
            file_data_name: 'images',
            // Maximum file size
            max_file_size: '20mb',
            autostart: true,
            multipart_params: {
                'fork[module]': "Media",
                'fork[action]': "Plupload",
                'fork[language]': jsBackend.current.language,
                'mediaModule': jsData.Media.mediaModule,
                'mediaAction': jsData.Media.mediaAction,
                'mediaId': jsData.Media.mediaId,
                'mediaType': jsData.Media.mediaType
            },

            //chunk_size: '1mb',

            // Resize images on clientside if we can
            resize: {
                width: 1920,
                height: 1920,
                quality: 80,
                crop: false // crop to exact dimensions
            },

            // Specify what files to browse for
            filters: [
                {title: "Image files", extensions: "jpg,jpeg,gif,png"},
                {title: "PDF files", extensions: "pdf"},
                {title: "Documents", extensions: "txt,rtf,csv,doc,docx,xls,xlsx,ppt,pptx"},
                {title: "Archives", extensions: "zip,rar,tar,gz,gzip,7z"},
                {title: "Files", extensions: "svg,ttf,html,css,eps,gzip,mp3,wmv,mpeg,mpg,mp4,aac,xml"}
            ],

            // Rename files by clicking on their titles
            rename: true,

            // Sort files
            sortable: true,

            // Enable ability to drag'n'drop files onto the widget (currently only HTML5 supports that)
            dragdrop: true,

            // Views to activate
            views: {
                list: true,
                thumbs: true, // Show thumbs
                active: 'thumbs'
            },

            // Flash settings
            flash_swf_url: '/src/Backend/Modules/Gallery/Swf/Moxie.swf',

            // Silverlight settings
            //silverlight_xap_url: 'https://cdnjs.cloudflare.com/ajax/libs/plupload/2.1.8/Moxie.xap',

            init: {

                UploadComplete: function (up, files) {
                    // Called when all files are either uploaded or failed
                    //--Generate random number for the refresh
                    var randomNumber = Math.floor(Math.random() * 11)

                    //--Delete #-identifier
                    var strLocation = window.location.href.substr(0, window.location.href.indexOf('#'));

                    //--Redirect page with randomnumber + #tabMedia identifier
                    //window.location.replace(strLocation + "&random=" + randomNumber + "#tabMedia");
                },
                FileUploaded: function (up, files, response) {
                    var data = JSON.parse(response.response).data;

                    if (data[0] == 1) {
                        $('.images').append(data[1]);
                    }
                    else {
                        $('.files').append(data[1]);
                    }

                    var id = $(data[1]).attr('id');

                    $('#' + id).find('.delete').click(
                        function (e)				// on stop sorting
                        {
                            e.preventDefault();
                            deleteF(this);
                        });

                    $('#' + id).find('.filename').click(
                        function (e) {
                            e.preventDefault();
                            rename(this);
                        }
                    );
                }
            },
        });
    },
    bindDeleteFile: function () {
        //--Add sortable to the galleria-lists
        $('.delete').click(
            function (e)				// on stop sorting
            {
                e.preventDefault();
                deleteF(this);
            });
    },
    bindDeleteFiles: function () {
        $('.deleteSelected').click(
            function (e)				// on stop sorting
            {
                $this = $(this);
                var mid = $this.data('messageId');

                e.preventDefault();

                var ids = [];

                var lis = $('.check:checked').parent();
                lis.children('.delete').each(function () {
                    ids.push(this.id);
                });
                //--Create ajax-call
                $('#' + mid).dialog(
                    {
                        autoOpen: false,
                        draggable: false,
                        resizable: false,
                        modal: true,
                        buttons: [
                            {
                                text: utils.string.ucfirst(jsBackend.locale.lbl('OK')),
                                click: function () {
                                    // unbind the beforeunload event
                                    $(window).off('beforeunload');

                                    $(this).dialog('close');
                                    $.ajax(
                                        {
                                            data: {
                                                fork: {action: 'DeleteFiles', module: 'Media'},
                                                ids: ids
                                            },
                                            success: function (data, textStatus) {
                                                //--Check if the response is correct
                                                if (data.code == 200) {
                                                    lis.remove();
                                                    jsBackend.messages.add('success', jsBackend.locale.msg('FilesDeleted'));
                                                }

                                                //--If there is an error, alert the message
                                                if (data.code != 200 && jsBackend.debug) {
                                                    alert(data.message);
                                                }

                                            },
                                            error: function (XMLHttpRequest, textStatus, errorThrown) {
                                                // revert
                                                $(this).sortable('cancel');

                                                // show message
                                                jsBackend.messages.add('error', 'delete failed.');

                                                // alert the user
                                                if (jsBackend.debug) {
                                                    alert(textStatus);
                                                }
                                            }
                                        });
                                }
                            },
                            {
                                text: utils.string.ucfirst(jsBackend.locale.lbl('Cancel')),
                                click: function () {
                                    $(this).dialog('close');
                                }
                            }
                        ],
                        open: function (e) {
                            // set focus on first button
                            if ($(this).next().find('button').length > 0) $(this).next().find('button')[0].focus();
                        }
                    })
                ;
                $('#' + mid).dialog('open');
            });
    },
    bindRenameFile: function () {
        $('.filename').click(
            function (e) {
                e.preventDefault();
                rename(this);
            }
        );
    }
}

$(jsBackend.Media.init);

function rename(elem) {
    var input = $('<input />', {
        'type': 'text',
        'name': 'unique',
        'class': 'inputText',
        'style': 'width:122px;',
        'value': $(elem).html()
    });
    var div = $('<span />', {
        'style': 'display:block; min-height:40px;'
    });
    input.blur(
        function (e) {
            var elemSet = $('<span />');
            elemSet.attr('style', 'display: block; height: 40px;');
            elemSet.click(
                function (e) {
                    e.preventDefault();
                    rename(this);
                });
            $.ajax(
                {
                    data: {
                        fork: {action: 'RenameFile', module: 'Media'},
                        id: $(this).parents().eq(2).find('.delete').attr('id'),
                        name: $(this).val()
                    },
                    success: function (data, textStatus) {
                        //--Check if the response is correct
                        if (data.code == 200) {
                            elemSet.parent().parent().find('.url').attr("href", data.data);

                            jsBackend.messages.add('success', jsBackend.locale.msg('FileRename'));

                        }

                        //--If there is an error, alert the message
                        if (data.code != 200 && jsBackend.debug) {
                            alert(data.message);
                        }

                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        // revert
                        $(this).sortable('cancel');

                        // show message
                        jsBackend.messages.add('error', 'rename failed.');

                        // alert the user
                        if (jsBackend.debug) {
                            alert(textStatus);
                        }
                    }
                });
            $(this).parent().parent().append(elemSet.html($(this).val()));
            $(this).parent().remove();
        }
    );
    div.append(input);
    $(elem).parent().append(div);
    $(elem).remove();
    input.focus();
}

function deleteF(elem) {
    var mid = $(elem).data('messageId');

    var id = $(elem).attr('id');
    var li = $(elem);
    $('#' + mid).dialog(
        {
            autoOpen: false,
            draggable: false,
            resizable: false,
            modal: true,
            buttons: [
                {
                    text: utils.string.ucfirst(jsBackend.locale.lbl('OK')),
                    click: function () {
                        // unbind the beforeunload event
                        $(window).off('beforeunload');

                        $(this).dialog('close');
                        $.ajax(
                            {
                                data: {
                                    fork: {action: 'DeleteFile', module: 'Media'},
                                    id: id
                                },
                                success: function (data, textStatus) {
                                    //--Check if the response is correct
                                    if (data.code == 200) {
                                        li.parent().remove();
                                        jsBackend.messages.add('success', jsBackend.locale.msg('FileDeleted'));
                                    }

                                    //--If there is an error, alert the message
                                    if (data.code != 200 && jsBackend.debug) {
                                        alert(data.message);
                                    }

                                },
                                error: function (XMLHttpRequest, textStatus, errorThrown) {
                                    // revert
                                    $(this).sortable('cancel');

                                    // show message
                                    jsBackend.messages.add('error', 'delete failed.');

                                    // alert the user
                                    if (jsBackend.debug) {
                                        alert(textStatus);
                                    }
                                }
                            })
                    }
                },
                {
                    text: utils.string.ucfirst(jsBackend.locale.lbl('Cancel')),
                    click: function () {
                        $(this).dialog('close');
                    }
                }
            ],
            open: function (e) {
                // set focus on first button
                if ($(this).next().find('button').length > 0) $(this).next().find('button')[0].focus();
            }
        });

    $('#' + mid).dialog('open');
}