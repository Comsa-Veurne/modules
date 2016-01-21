<?php

namespace Backend\Modules\Media\Ajax;

use Backend\Core\Engine\Base\AjaxAction;
use Backend\Modules\Media\Engine\Helper as BackendMediaHelper;
use Backend\Core\Engine\Template;

/**
 * Get all mediaitems on server and returns a html list
 *
 * @author Nick Vandevenne <nick@comsa.be>
 */
class GetLink extends AjaxAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        //--Create template object
        $tpl = new Template();
        //--Add list of all mediaitems to template
        $tpl->assign('mediaItems', BackendMediaHelper::getAllMediaItems());
        //--Get html list
        $html = $tpl->getContent(BACKEND_MODULES_PATH . '/Media/Layout/Templates/Ajax/Link.tpl');

        // success output
        $this->output(self::OK, $html, '');
    }
}