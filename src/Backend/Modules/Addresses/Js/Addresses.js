/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Interaction for the addresses module
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
jsBackend.addresses =
{
	// constructor
	init: function()
	{
		// do meta
		if($('#company').length > 0) $('#company').doMeta();

        if($('#title').length > 0) $('#title').doMeta();

		$filter = $('#filter');
		$filterGroup = $('#filter #group');

		$filterGroup.on('change', function (e)
		{
			$filter.submit();
		});
	}
};

$(jsBackend.addresses.init);
