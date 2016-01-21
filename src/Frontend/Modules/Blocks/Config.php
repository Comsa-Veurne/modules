<?php

namespace Frontend\Modules\Blocks;

use Frontend\Core\Engine\Base\Config as BaseConfig;

/**
 * This is the configuration-object for the Blocks module
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
final class Config extends BaseConfig
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
