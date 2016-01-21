jsFrontend.catalogFrontpage =
{
    init: function ()
    {
        $(document).on('cycle-post-initialize', function (event, opts)
        {
            setTimeout(function()
            {
                $('.cycle-slide').hover(function ()
                {
                    $("li[id^=catalog-showimage-]").hide();
                    $id = $(this).data('catalog-id');
                    $('#catalog-showimage-' + $id).css('visibility', 'visible');
                    $('#catalog-showimage-' + $id).show();
                });
            }, 1000);
        });

        $('.cycle-carousel').cycle();
    }
}

$(jsFrontend.catalogFrontpage.init);