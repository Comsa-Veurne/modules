<?php

namespace Backend\Modules\Addresses\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Modules\Addresses\Engine\Model as BackendAddressesModel;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Reorder images
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class AddressesSequence extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        //--Get the ids and split them
        $ids = explode(',', trim(SpoonFilter::getPostValue('ids', null, '', 'string')));
        $group_id = SpoonFilter::getPostValue('group_id', null, '', 'int');

        //--Check if the id is not empty
        if (!empty($ids)) {
            //--Set the sequence to 1
            $sequence = 1;

            //--Loop the id's
            foreach ($ids as $address_id) {
                //--Set the item array
                $item = array();

                $item["sequence"] = $sequence;

                BackendAddressesModel::exists($address_id) ? BackendAddressesModel::updateSequence($address_id, $group_id, $item) : null;

                //--Add the sequence for each id
                $sequence++;

            }

        }

        // success output
        $this->output(self::OK, null, 'sequence updated');
    }
}