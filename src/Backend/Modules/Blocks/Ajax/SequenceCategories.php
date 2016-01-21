<?php

namespace Backend\Modules\Blocks\Ajax;

use Backend\Core\Engine\Base\AjaxAction;
use Backend\Modules\Blocks\Engine\Model as BackendBlocksModel;

/**
 * Alters the sequence of Blocks articles
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class SequenceCategories extends AjaxAction
{
    public function execute()
    {
        parent::execute();

        // get parameters
        $newIdSequence = trim(\SpoonFilter::getPostValue('new_id_sequence', null, '', 'string'));

        // list id
        $ids = (array)explode(',', rtrim($newIdSequence, ','));

        // loop id's and set new sequence
        foreach ($ids as $i => $id) {
            $item['id'] = $id;
            $item['sequence'] = $i + 1;

            // update sequence
            if (BackendBlocksModel::existsCategory($id)) {
                BackendBlocksModel::updateCategory($item);
            }
        }

        // success output
        $this->output(self::OK, null, 'sequence updated');
    }
}