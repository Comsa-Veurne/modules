/**
 * Interaction for the galleria-module
 *
 *
 *
 */
jsBackend.gallery =
{
    init: function ()
    {
        //--Initialise sortable
        jsBackend.gallery.bindSortable();
        jsBackend.gallery.bindDeleteFile();
        jsBackend.gallery.bindDeleteFiles();
        jsBackend.gallery.bindRenameFile();
        //--Initialise uploadify
        jsBackend.gallery.plupload();

        $('#all-images').click(function ()
        {
            if ($(this).prop('checked') == true)
            {
                $('input[class^=check]').prop('checked', true);
            }
            else
            {
                $('input[class^=check]').prop('checked', false);
            }
        })
    },
    bindSortable: function ()
    {
        //--Add sortable to the galleria-lists
        $('ul.gallery').sortable(
            {
                handle: 'img',
                tolerance: 'pointer',
                stop: function (e, ui)				// on stop sorting
                {
                    var arrIds = Array();

                    //--Loop the children
                    $(this).children('li').each(function (index, element)
                    {
                        //--Get the id from the element and push it into an array
                        arrIds.push($(element).attr('id').substr(3));
                    });

                    //--Create a string of the array with a , delimeter.
                    var strIds = arrIds.join(',');

                    //--Create ajax-call
                    $.ajax(
                        {
                            data: {
                                fork: {action: 'ImagesSequence'},
                                ids: strIds
                            },
                            success: function (data, textStatus)
                            {
                                //--Check if the response is correct
                                if (data.code == 200)
                                {
                                    jsBackend.messages.add('success', jsBackend.locale.lbl('SequenceSaved'));
                                }

                                //--If there is an error, alert the message
                                if (data.code != 200 && jsBackend.debug)
                                {
                                    alert(data.message);
                                }

                            },
                            error: function (XMLHttpRequest, textStatus, errorThrown)
                            {
                                // revert
                                $(this).sortable('cancel');

                                // show message
                                jsBackend.messages.add('error', 'alter sequence failed.');

                                // alert the user
                                if (jsBackend.debug)
                                {
                                    alert(textStatus);
                                }
                            }
                        })
                }
            });
    },
    plupload: function ()
    {
        var uploader = $("#uploader").plupload({
            // General settings
            runtimes: 'html5,flash,html4',
            url: "/backend/ajax",
            file_data_name: 'images',
            // Maximum file size
            max_file_size: '20mb',
            autostart: true,
            multipart_params: {
                "fork[module]": "Gallery",
                "fork[action]": "Plupload",
                "fork[language]": jsBackend.current.language,
                "id": jsData.Gallery.id
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
                UploadComplete: function (up, files)
                {
                    // Called when all files are either uploaded or failed
                    var randomNumber = Math.floor(Math.random() * 11)

                    //window.location.replace("/private/" + jsBackend.current.language + "/gallery/edit_album?token=true&id=" + jsData.Gallery.id + "&report=added-images&random=" + randomNumber + "#tabImages");
                },
                FileUploaded: function (up, files, response)
                {
                    var data = JSON.parse(response.response).data;

                    $('.gallery').append(data);

                    var id = $(data).attr('id');

                    $('#' + id).find('.delete').click(
                        function (e)				// on stop sorting
                        {
                            e.preventDefault();
                            deleteF(this);
                        });

                    $('#' + id).find('.filename').click(
                        function (e)
                        {
                            e.preventDefault();
                            rename(this);
                        }
                    );
                }
            },
        });
    },
    bindDeleteFile: function ()
    {
        //--Add sortable to the galleria-lists
        $('.delete').click(
            function (e)
            {
                e.preventDefault();
                deleteF(this);
            }
        );
    },
    bindDeleteFiles: function ()
    {
        $('.deleteSelected').click(
            function (e)				// on stop sorting
            {
                $this = $(this);
                var mid = $this.data('messageId');

                e.preventDefault();

                var ids = [];

                var lis = $('.check:checked').parent();
                lis.children('.delete').each(function ()
                {
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
                                click: function ()
                                {
                                    // unbind the beforeunload event
                                    $(window).off('beforeunload');

                                    $(this).dialog('close');
                                    $.ajax(
                                        {
                                            data: {
                                                fork: {action: 'DeleteFiles', module: 'Gallery'},
                                                ids: ids
                                            },
                                            success: function (data, textStatus)
                                            {
                                                //--Check if the response is correct
                                                if (data.code == 200)
                                                {
                                                    lis.remove();
                                                    jsBackend.messages.add('success', jsBackend.locale.msg('FilesDeleted'));
                                                }

                                                //--If there is an error, alert the message
                                                if (data.code != 200 && jsBackend.debug)
                                                {
                                                    alert(data.message);
                                                }

                                            },
                                            error: function (XMLHttpRequest, textStatus, errorThrown)
                                            {
                                                // revert
                                                $(this).sortable('cancel');

                                                // show message
                                                jsBackend.messages.add('error', 'delete failed.');

                                                // alert the user
                                                if (jsBackend.debug)
                                                {
                                                    alert(textStatus);
                                                }
                                            }
                                        })
                                }
                            },
                            {
                                text: utils.string.ucfirst(jsBackend.locale.lbl('Cancel')),
                                click: function ()
                                {
                                    $(this).dialog('close');
                                }
                            }
                        ],
                        open: function (e)
                        {
                            // set focus on first button
                            if ($(this).next().find('button').length > 0) $(this).next().find('button')[0].focus();
                        }
                    })
                ;
                $('#' + mid).dialog('open');
            });
    },
    bindRenameFile: function ()
    {
        $('.filename').click(
            function (e)
            {
                e.preventDefault();
                rename(this);
            }
        );

    }
}

$(jsBackend.gallery.init);

function rename(elem)
{
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
        function (e)
        {
            var elemSet = $('<span />');
            elemSet.attr('style', 'display: block; height: 40px;');
            elemSet.click(
                function (e)
                {
                    e.preventDefault();
                    rename(this);
                });
            $.ajax(
                {
                    data: {
                        fork: {action: 'RenameFile', module: 'Gallery'},
                        id: $(this).parents().eq(2).find('.delete').attr('id'),
                        name: $(this).val()
                    },
                    success: function (data, textStatus)
                    {
                        //--Check if the response is correct
                        if (data.code == 200)
                        {
                            elemSet.parent().parent().find('.url').attr("href", data.data);

                            jsBackend.messages.add('success', jsBackend.locale.msg('FileRename'));

                        }

                        //--If there is an error, alert the message
                        if (data.code != 200 && jsBackend.debug)
                        {
                            alert(data.message);
                        }

                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown)
                    {
                        // revert
                        $(this).sortable('cancel');


                        // show message
                        jsBackend.messages.add('error', 'rename failed.');

                        // alert the user
                        if (jsBackend.debug)
                        {
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

function deleteF(elem)
{
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
                    click: function ()
                    {
                        // unbind the beforeunload event
                        $(window).off('beforeunload');

                        $(this).dialog('close');

                        $.ajax(
                            {
                                data: {
                                    fork: {action: 'DeleteFile', module: 'Gallery'},
                                    id: id
                                },
                                success: function (data, textStatus)
                                {
                                    //--Check if the response is correct
                                    if (data.code == 200)
                                    {
                                        li.parent().remove();
                                        jsBackend.messages.add('success', jsBackend.locale.msg('FileDeleted'));
                                    }

                                    //--If there is an error, alert the message
                                    if (data.code != 200 && jsBackend.debug)
                                    {
                                        alert(data.message);
                                    }

                                },
                                error: function (XMLHttpRequest, textStatus, errorThrown)
                                {
                                    // revert
                                    $(this).sortable('cancel');

                                    // show message
                                    jsBackend.messages.add('error', 'delete failed.');

                                    // alert the user
                                    if (jsBackend.debug)
                                    {
                                        alert(textStatus);
                                    }
                                }
                            })
                    }
                },
                {
                    text: utils.string.ucfirst(jsBackend.locale.lbl('Cancel')),
                    click: function ()
                    {
                        $(this).dialog('close');
                    }
                }
            ],
            open: function (e)
            {
                // set focus on first button
                if ($(this).next().find('button').length > 0) $(this).next().find('button')[0].focus();
            }
        });
    $('#' + mid).dialog('open');

    //--Create ajax-call
}