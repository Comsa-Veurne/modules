<?php
namespace Frontend\Modules\Mailengine\Actions;

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Mailengine\Engine\Model as FrontendMailengineModel;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the Image-action, it will display the overview of mailengine posts
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class MailengineImage extends FrontendBaseBlock
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        //--Get the id
        $id = $this->URL->getParameter(1);

        //--check if the id is not empty
        if (empty($id)) {
            $this->redirect(FrontendNavigation::getURL(404));
        }

        //--Explode the id
        $ids = explode("-", $id);

        //--check if the id contains 2 elements
        if (count($ids) != 2) {
            $this->redirect(FrontendNavigation::getURL(404));
        }

        //--Get the ids and decrypt
        $send_id = (int)FrontendMailengineModel::decryptId($ids[0]);
        $user_id = (int)FrontendMailengineModel::decryptId($ids[1]);

        //--check if the ids are integers
        if ($send_id <= 0) {
            $this->redirect(FrontendNavigation::getURL(404));
        }

        if ($user_id > 0) {
            $data = array();
            $data["send_id"] = $send_id;
            $data["user_id"] = $user_id;

            //--Add open-mail to the database
            FrontendMailengineModel::insertMailOpen($data);
        }

        //--Create an empty image
        $this->createImage();

        //--Stop the script
        die();
    }

    /*
    *
    * Create an image
    *
    */
    protected function createImage()
    {
        //--Nieuwe afbeelding maken
        $objImg = imagecreatetruecolor(1, 1);
        $objBlack = imagecolorallocate($objImg, 0, 0, 0);

        //--Achtergrond transparant maken
        imagecolortransparent($objImg, $objBlack);

        //-- Afbeelding outputen naar de browser
        header('Content-Type: image/gif');

        imagegif($objImg);
        imagedestroy($objImg);
    }
}