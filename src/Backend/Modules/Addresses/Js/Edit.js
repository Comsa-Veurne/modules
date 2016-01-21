/**
 * Interaction for the galleria-module
 *
 *
 *
 */
jsBackend.addresses =
{

    init: function()
    {
        //--Initialise sortable
        jsBackend.addresses.bindSortable();

    },

    bindSortable: function()
    {
        //--Add sortable to the galleria-lists
        $('ul.addresses').sortable(
            {
                handle: 'span',
                tolerance: 'pointer',
                stop: function(e, ui)				// on stop sorting
                {
                    var arrIds = new Array();

                    //--Loop the children
                    $(this).children('li').each(function(index, element)
                    {
                        //--Get the id from the element and push it into an array
                        arrIds.push($(element).attr('id').substr(12));
                    });

                    //--Create a string of the array with a , delimeter.
                    var strIds = arrIds.join(',');

                    //--Create ajax-call
                    $.ajax(
                        {
                            data:
                            {
                                fork: { action: 'addresses_sequence' },
                                ids: strIds,
                                group_id: $("#id").val()

                            },
                            success: function(data, textStatus)
                            {
                                //--Check if the response is correct
                                if(data.code == 200)
                                {
                                    jsBackend.messages.add('success', jsBackend.locale.lbl('SequenceSaved'));
                                }

                                //--If there is an error, alert the message
                                if(data.code != 200 && jsBackend.debug){ alert(data.message); }

                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown)
                            {
                                // revert
                                $(this).sortable('cancel');

                                // show message
                                jsBackend.messages.add('error', 'alter sequence failed.');

                                // alert the user
                                if(jsBackend.debug){ alert(textStatus); }
                            }
                        })
                }
            });
    }
};

$(jsBackend.addresses.init);