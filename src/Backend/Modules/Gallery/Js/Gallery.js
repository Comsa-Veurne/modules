/**
 * Interaction for the galleria module
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
jsBackend.gallery =
{
    // constructor
    init: function ()
    {
        // do meta
        if ($('#title').length > 0) $('#title').doMeta();

        $('#all-images').click(function ()
        {
            if ($(this).prop('checked') == true)
            {
                $('input[id^=delete]').prop('checked', true);
            }
            else
            {
                $('input[id^=delete]').prop('checked', false);
            }
        })
    }
};

$(jsBackend.gallery.init);
