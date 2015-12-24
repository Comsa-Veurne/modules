<?php
namespace Backend\Modules\Media;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\Config as BaseConfig;


/**
 * This is the configuration-object for the media module
 *
 * @author Waldo Cosman <waldo@comsa.be>
 * @author Nick Vandevenne <nick@comsa.be>
 */
final class Config extends BaseConfig
{
	/**
	 * The default action
	 *
	 * @var string
	 */
	protected $defaultAction = 'index';

	/**
	 * The disabled actions
	 *
	 * @var array
	 */
	protected $disabledActions = array();
}
