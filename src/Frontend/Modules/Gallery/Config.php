<?php

namespace Frontend\Modules\Gallery;

use Frontend\Core\Engine\Base\Config as FrontendBaseConfig;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the configuration-object
 *
 * @author John Poelman <john.poelman@bloobz.be>
 */
final class Config extends FrontendBaseConfig
{
	/**
	 * The default action
	 *
	 * @var string
	 */
	protected $defaultAction = 'Index';

	/**
	 * The disabled actions
	 *
	 * @var array
	 */
	protected $disabledActions = array();
}