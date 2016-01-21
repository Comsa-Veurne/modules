<?php
namespace Backend\Modules\Mailengine\Actions;

use Backend\Core\Engine\Base\Action as BackendBaseAction;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Mailengine\Engine\Model as BackendMailengineModel;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the Preview action
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Preview extends BackendBaseAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        $this->loadData();
        $this->parse();
        $this->display();
    }

    /**
     * Load the item data
     */
    protected function loadData()
    {
        $this->id = $this->getParameter('id', 'int', null);

        if ($this->id == null || !BackendMailengineModel::exists($this->id)) {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }

        $this->record = BackendMailengineModel::getPreview($this->id);
        echo $this->record;
        die();


    }

    /**
     * Parse the page
     */
    protected function parse()
    {
        $this->tpl->assign("record", $this->record);
    }
}