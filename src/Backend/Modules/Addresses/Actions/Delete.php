<?php

namespace Backend\Modules\Addresses\Actions;

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Addresses\Engine\Model as BackendAddressesModel;
use Backend\Modules\Search\Engine\Model as BackendSearchModel;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the elete-action, it deletes an item
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Delete extends BackendBaseActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if ($this->id !== null && BackendAddressesModel::exists($this->id)) {
            parent::execute();
            $this->record = (array)BackendAddressesModel::get($this->id);

            BackendAddressesModel::delete($this->id);
            BackendAddressesModel::deleteGroupsFromAddress($this->id);


            // delete the image
            \SpoonFile::delete(FRONTEND_FILES_PATH . '/Addresses/Images/Source/' . $this->record['image']);

            BackendSearchModel::removeIndex($this->getModule(), $this->id);

            BackendModel::triggerEvent($this->getModule(), 'after_delete', array('id' => $this->id));

            $this->redirect(BackendModel::createURLForAction('index') . '&report=deleted&var=' . urlencode($this->record['id']));
        } else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
    }
}