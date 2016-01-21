<?php

namespace Frontend\Modules\Agenda;

use Frontend\Core\Engine\Base\Config as FrontendBaseConfig;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the configuration-object for the agenda module
 *
 * @author Wouter Verstuyf <info@webflow.be>
 * @author Tim van Wolfswinkel <tim@webleads.nl>
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