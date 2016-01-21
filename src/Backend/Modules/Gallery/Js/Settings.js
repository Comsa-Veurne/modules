/**
 * Interaction for the galleria module
 *
 */
(function($)
{
    $.fn.resolutionBox = function(options)
    {
        // define defaults
        var defaults =
        {
            splitChar: ',',
            emptyMessage: '',
            errorMessage: 'Add the tag before submitting',
            addLabel: 'add',
            removeLabel: 'delete',
            params: {},
            canAddNew: false,
            showIconOnly: true,
            multiple: true
        };

        // extend options
        options = $.extend(defaults, options);

        // loop all elements
        return this.each(function()
        {
            // define some vars
            var id = $(this).attr('id');
            var elements = get();
            var blockSubmit = false;
            var timer = null;

            // reset label, so it points to the correct item
            $('label[for="' + id + '"]').attr('for', 'addValue-' + id);

            // bind submit
            $(this.form).submit(function(e)
            {
                // hide before..
                $('#errorMessage-'+ id).remove();

                if(blockSubmit && $('#addValue-' + id).val().replace(/^\s+|\s+$/g, '') !== '')
                {
                    // show warning
                    $('#addValue-'+ id).parents('.oneLiner').append('<span style="display: none;" id="errorMessage-'+ id +'" class="formError">'+ options.errorMessage +'</span>');

                    // clear other timers
                    clearTimeout(timer);

                    // we need the timeout otherwise the error is show every time the user presses enter in the tagbox
                    timer = setTimeout(function() { $('#errorMessage-'+ id).show(); }, 200);
                }

                return !blockSubmit;
            });

            // build replace html
            var html = 	'<div class="tagsWrapper">' +
                '	<div class="oneLiner">' +
                '		<p><input class="inputText dontSubmit" id="addValue-' + id + '" name="addValue-' + id + '" type="text" /></p>' +
                '		<div class="buttonHolder">' +
                '			<a href="#" id="addButton-' + id + '" class="button icon iconAdd disabledButton';

            if(options.showIconOnly) html += ' iconOnly';

            html += 	'">' +
            '				<span>' + options.addLabel + '</span>' +
            '			</a>' +
            '		</div>' +
            '	</div>' +
            '	<div id="elementList-' + id + '" class="tagList">' +
            '	</div>' +
            '</div>';

            // hide current element
            $(this).css('visibility', 'hidden').css('position', 'absolute').css('top', '-9000px').css('left', '-9000px').attr('tabindex', '-1');

            // prepend html
            $(this).before(html);

            // add elements list
            build();

            // bind autocomplete if needed
            if(!$.isEmptyObject(options.params))
            {
                $('#addValue-' + id).autocomplete(
                    {
                        delay: 200,
                        minLength: 2,
                        source: function(request, response)
                        {
                            $.ajax(
                                {
                                    data: $.extend(options.params, { term: request.term }),
                                    success: function(data, textStatus)
                                    {
                                        // init var
                                        var realData = [];

                                        // alert the user
                                        if(data.code != 200 && jsBackend.debug)
                                        {
                                            alert(data.message);
                                        }

                                        if(data.code == 200)
                                        {
                                            for(var i in data.data)
                                            {
                                                realData.push(
                                                    {
                                                        label: data.data[i].name,
                                                        value: data.data[i].name
                                                    });
                                            }
                                        }

                                        // set response
                                        response(realData);
                                    }
                                });
                        }
                    });
            }

            // bind keypress on value-field
            $('#addValue-' + id).bind('keyup', function(e)
            {
                blockSubmit = true;

                // grab code
                var code = e.which;

                // remove error message
                $('#errorMessage-'+ id).remove();

                // enter of splitchar should add an element
                if(code == 13 || $(this).val().indexOf(options.splitChar) != -1)
                {
                    // hide before..
                    $('#errorMessage-'+ id).remove();

                    // prevent default behaviour
                    e.preventDefault();
                    e.stopPropagation();

                    // add element
                    add();
                }

                // disable or enable button
                if($(this).val().replace(/^\s+|\s+$/g, '') === '')
                {
                    blockSubmit = false;
                    $('#addButton-' + id).addClass('disabledButton');
                }
                else $('#addButton-' + id).removeClass('disabledButton');
            });

            // bind click on add-button
            $('#addButton-' + id).bind('click', function(e)
            {
                // dont submit
                e.preventDefault();
                e.stopPropagation();

                // add element
                add();
            });

            // bind click on delete-button
            $('.deleteButton-' + id).live('click', function(e)
            {
                // dont submit
                e.preventDefault();
                e.stopPropagation();

                // remove element
                remove($(this).data('id'));
            });

            // add an element
            function add()
            {
                blockSubmit = false;

                // init some vars
                var value = $('#addValue-' + id).val().replace(/^\s+|\s+$/g, '').replace(options.splitChar, '');
                var patternCheck = /^\d{1,}[x]{1,1}\d{0,}$/;
                if(patternCheck.test(value))
                {
                    var inElements = false;

                    // if multiple arguments aren't allowed, clear before adding
                    if (!options.multiple) elements = [];

                    // reset box
                    $('#addValue-' + id).val('').focus();
                    $('#addButton-' + id).addClass('disabledButton');

                    // remove error message
                    $('#errorMessage-' + id).remove();

                    // only add new element if it isn't empty
                    if (value !== '')
                    {
                        // already in elements?
                        for (var i in elements)
                        {
                            if (value == elements[i]) inElements = true;
                        }

                        // only add if not already in elements
                        if (!inElements)
                        {
                            // add elements
                            elements.push(utils.string.stripForTag(value));

                            // set new value
                            $('#' + id).val(elements.join(options.splitChar));

                            // rebuild element list
                            build();
                        }
                    }
                }else{
                    $('#addValue-' + id).val('').focus();
                    $('#addButton-' + id).addClass('disabledButton');
                }
            }

            // build the list
            function build()
            {
                // init var
                var html = '';

                // no items and message given?
                if(elements.length === 0 && options.emptyMessage !== '') html = '<p class="helpTxt">' + options.emptyMessage + '</p>';

                // items available
                else
                {
                    // start html
                    html = '<ul>';

                    // loop elements
                    for(var i in elements)
                    {
                        var value = utils.string.stripForTag(elements[i]);

                        html += '	<li><span><strong>' + value + '</strong>' +
                        '		<a href="#" class="deleteButton-' + id + '" data-id="' + value + '" title="' + utils.string.stripForTag(options.removeLabel) + ' ' + value + '">' + options.removeLabel + '</a></span>' +
                        '	</li>';
                    }

                    // end html
                    html += '</ul>';
                }

                // set html
                $('#elementList-' + id).html(html);
            }

            // get all items
            function get()
            {
                // get chunks
                var chunks = $('#' + id).val().split(options.splitChar);
                var elements = [];

                // loop elements and trim them from spaces
                for(var i in chunks)
                {
                    value = chunks[i].replace(/^\s+|\s+$/g, '');
                    if(value !== '') elements.push(value);
                }

                return elements;
            }

            // remove an item
            function remove(value)
            {
                // get index for element
                var index = $.inArray(String(value), elements);

                // remove element
                if(index > -1) elements.splice(index, 1);

                // set new value
                $('#' + id).val(elements.join(options.splitChar));

                // rebuild element list
                build();
            }
        });
    };
})(jQuery);

jsBackend.gallerySettings =
{
    // constructor
    init: function ()
    {
        // do meta
        if ($('#title').length > 0) $('#title').doMeta();

        if($('input.tagBox').length > 0)
        {
            $('input.tagBox').resolutionBox(
                {
                    emptyMessage: jsBackend.locale.msg('NoResolutions', 'Gallery'),
                    errorMessage: jsBackend.locale.err('AddResolutionBeforeSubmitting', 'Gallery'),
                    addLabel: utils.string.ucfirst(jsBackend.locale.lbl('Add')),
                    removeLabel: utils.string.ucfirst(jsBackend.locale.lbl('Delete')),
                    params: { },
                    showIconOnly: false
                });
        }
    }
};

$(jsBackend.gallerySettings.init);