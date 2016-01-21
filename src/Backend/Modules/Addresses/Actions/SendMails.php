<?php

namespace Backend\Modules\Addresses\Actions;

use Backend\Core\Engine\Base\Action as BackendBaseAction;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Addresses\Engine\Model as BackendAddressesModel;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */
/**
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class SendMails extends BackendBaseAction
{
    /**
     * Execute the actions
     */
    public function execute()
    {
        parent::execute();

        //--Get all the addresses
        $addresses = BackendAddressesModel::getAllAddresses(1);

        foreach ($addresses as &$address) {
            $address = BackendAddressesModel::get($address['id']);

            foreach ($address as &$row) {
                $row = $row == "" ? "-" : $row;
            }
        }

        foreach ($addresses as $address) {
            set_time_limit(10);
            if (filter_var($address['email'], FILTER_VALIDATE_EMAIL) && $address['send_mail'] == 0) {

                //--Send mail for the address
                BackendMailer::addEmail("Nieuwe website Namev.be met uw eigen bedrijfs-pagina", BACKEND_MODULE_PATH . '/layout/templates/mails/send_email.tpl', $address, 'waldo@comsa.be', $address['company']);
//								BackendMailer::addEmail("Nieuwe website Namev.be met uw eigen bedrijfs-pagina", BACKEND_MODULE_PATH . '/layout/templates/mails/send_email.tpl', $address, 'info@namev.be', $address['company']);
//				BackendMailer::addEmail("Nieuwe website Namev.be met uw eigen bedrijfs-pagina", BACKEND_MODULE_PATH . '/layout/templates/mails/send_email.tpl', $address, $address['email'], $address['company']);

                BackendModel::getContainer()->get('database')->update('addresses', array("send_mail" => 1), 'id = ?', (int)$address['id']);
                die();
            }
        }
        //--Update the address row when e-mail is send
    }
}