/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Interaction for the galleria module
 *
 * @author John Poelman <john.poelman@bloobz.be>
 * @author Waldo Cosman <waldo@comsa.be>
 */
jsFrontend.gallery =
{
	// constructor
	init: function()
	{
        //--Initialize colorbox to the gallery
        $('ul.gallery-gallery li a').colorbox({rel:'group', maxHeight: '80%', maxWidth: '80%'});


        //--Initialize slidehow
        $('ul.gallery-slideshow').cycle();

        $('button.more-pictures').click(function() {
            $(this).prev('ul').children('li').slideDown( "fast" );
            $(this).next('button.less-pictures').show();
            $(this).hide();
        });

        $('button.less-pictures').click(function() {
            $(this).prev().prev('ul').children('li:nth-child(n + 5)').slideUp( "fast" );
            $(this).prev('button.more-pictures ').show();
            $(this).hide();
        });
	}
}

$(jsFrontend.gallery.init);
