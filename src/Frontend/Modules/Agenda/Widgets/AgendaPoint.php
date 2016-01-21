<?php

namespace Frontend\Modules\Agenda\Widgets;

use Frontend\Core\Engine\Base\Widget as FrontendBaseWidget;
use Frontend\Modules\Agenda\Engine\Model as FrontendAgendaModel;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a widget for one agenda point
 *
 * @author Nick Vandevenne <nick@comsa.be>
 */
class AgendaPoint extends FrontendBaseWidget
{
    private $record;

    /**
     * Execute the extra
     */
    public function execute()
    {
        parent::execute();
        $this->loadTemplate();

        $this->loadData();
        $this->parse();
    }

    private function loadData()
    {
        if (isset($this->data['id'])) {
            $this->record = FrontendAgendaModel::getById($this->data['id']);
            $this->record['allow_subscriptions'] = ($this->record['allow_subscriptions'] == 'Y');
        }
    }

    /**
     * Parse
     */
    private function parse()
    {
        $this->tpl->assign('point', $this->record);
    }
}