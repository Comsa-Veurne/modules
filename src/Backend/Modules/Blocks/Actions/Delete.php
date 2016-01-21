<?php

namespace Backend\Modules\Blocks\Actions;

use Backend\Core\Engine\Base\ActionDelete;
use Backend\Core\Engine\Model;
use Backend\Modules\Blocks\Engine\Model as BackendBlocksModel;

/**
 * This is the delete-action, it deletes an item
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Delete extends ActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if ($this->id !== null && BackendBlocksModel::exists($this->id)) {
            parent::execute();
            $this->record = (array)BackendBlocksModel::get($this->id);

            BackendBlocksModel::delete($this->id);

            Model::triggerEvent(
                $this->getModule(), 'after_delete',
                array('id' => $this->id)
            );

            $this->redirect(
                Model::createURLForAction('Index') . '&report=deleted&var=' .
                urlencode($this->record['title'])
            );
        } else $this->redirect(Model::createURLForAction('Index') . '&error=non-existing');
    }
}