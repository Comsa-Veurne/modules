<?php

namespace Backend\Modules\Addresses\Actions;

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Addresses\Engine\Model as BackendAddressesModel;

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
class DeleteGroup extends BackendBaseActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if ($this->id !== null && BackendAddressesModel::existsGroup($this->id)) {
            parent::execute();
            $this->record = (array)BackendAddressesModel::getGroup($this->id);

            BackendAddressesModel::deleteGroup($this->id);

            BackendAddressesModel::deleteAddressesFromGroup($this->id);

            BackendModel::triggerEvent($this->getModule(), 'after_delete_group', array('id' => $this->id));

            $this->redirect(BackendModel::createURLForAction('groups') . '&report=deleted&var=' . urlencode($this->record['title']));
        } else $this->redirect(BackendModel::createURLForAction('groups') . '&error=non-existing');
    }
}