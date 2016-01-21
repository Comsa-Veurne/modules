<?php
namespace Frontend\Modules\Mailengine\Widgets;

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Modules\Mailengine\Engine\Model as FrontendMailengineModel;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a frontend widget
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Mailings extends FrontendBaseWidget
{
    /**
     * @var array
     */
    private $record;

    /**
     * Exceute the action
     */
    public function execute()
    {
        parent::execute();

        $this->loadTemplate();
        $this->loadData();

        $this->parse();
    }

    /**
     * Load the data
     */
    private function loadData()
    {
        $this->record = FrontendMailengineModel::getSendMailingsForWebiste();
    }

    /**
     * Parse the widget
     */
    protected function parse()
    {
        $this->tpl->assign('widgetMailings', $this->record);
    }
}