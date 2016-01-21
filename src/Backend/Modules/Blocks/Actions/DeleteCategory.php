<?php

namespace Backend\Modules\Blocks\Actions;

use Backend\Core\Engine\Base\ActionDelete;
use Backend\Core\Engine\Model;
use Backend\Modules\Blocks\Engine\Model as BackendBlocksModel;

/**
 * This action will delete a category
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class DeleteCategory extends ActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if ($this->id == null || !BackendBlocksModel::existsCategory($this->id)) {
            $this->redirect(
                Model::createURLForAction('categories') . '&error=non-existing'
            );
        }

        // fetch the category
        $this->record = (array)BackendBlocksModel::getCategory($this->id);

        // delete item
        BackendBlocksModel::deleteCategory($this->id);
        Model::triggerEvent($this->getModule(), 'after_delete_category', array('item' => $this->record));

        // category was deleted, so redirect
        $this->redirect(
            Model::createURLForAction('categories') . '&report=deleted-category&var=' .
            urlencode($this->record['title'])
        );
    }
}