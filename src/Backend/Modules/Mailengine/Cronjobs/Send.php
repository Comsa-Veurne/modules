<?php

namespace Backend\Modules\Mailengine\Cronjobs;

use Backend\Core\Engine\Base\Cronjob as BackendBaseCronjob;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Mailengine\Engine\Model as BackendMailengineModel;

/**
 * This cronjob will send the mailings which are waiting
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Send extends BackendBaseCronjob
{

    /**
     * Execute the action
     */
    public function execute()
    {

        parent::execute();

        //--Get the mailings which are ready to send
        $mails = BackendMailengineModel::getWaitingMailings();

        if (!empty($mails)) {
            //--Loop the mails
            foreach ($mails as $mail) {

                //--Get mailer-email to send the mail to
                $arrFrom = BackendModel::getModuleSetting('Core', 'mailer_from');

                //--Create variables array
                $variables = array();
                $variables['sentOn'] = time();
                $variables['dateFormatLong'] = BackendModel::getModuleSetting('Core', 'date_format_long') . " " . BackendModel::getModuleSetting('Core', 'time_format');
                $variables['subject'] = $mail['subject'];

                //--Send start mail

                /* $message = \Common\Mailer\Message::newInstance(
                     'Mailing started "' . $mail['subject'] . '"'
                 )
                     ->setFrom(array($arrFrom['email'] => $arrFrom['name']))
                     ->setTo(array($arrFrom['email']))
                     ->parseHtml(
                         BACKEND_MODULES_PATH . '/Modules/Mailengine/Layout/Templates/Mails/MailingStart.tpl',
                         $variables
                     )
                 ;
                 $this->get('mailer')->send($message);*/


//				$this->get('mailer')->addEmail('Mailing started "' . $mail['subject'] . '"', BACKEND_PATH . '/Modules/Mailengine/Layout/Templates/Mails/MailingStart.tpl', $variables, $arrFrom["email"], $arrFrom["name"]);

                //--Insert mail in stats
                $data = array();
                $data['id'] = $mail['id'];
                $data['mail_id'] = $mail['mail_id'];
                $data['domain'] = $mail['domain'];
                $data['subject'] = $mail['subject'];
                $data['text'] = $mail['text'];
                $data['start_time'] = $mail['start_time'];
                $data['end_time'] = $mail['end_time'];
                $data['from_name'] = $mail['from_name'];
                $data['from_email'] = $mail['from_email'];
                $data['reply_name'] = $mail['reply_name'];
                $data['reply_email'] = $mail['reply_email'];
                BackendMailengineModel::insertMailToStats($data);

                $mail['from_name'] = html_entity_decode($mail['from_name']);
                $mail['reply_name'] = html_entity_decode($mail['reply_name']);

                //--Update status
                BackendMailengineModel::updateStatusMailing($mail['id'], array('status' => 'busy'));

                //--Get the users for the mailing
                $users = BackendMailengineModel::getUsersForWaitingMail($mail['id']);

                if (!empty($users)) {
                    $count = 0;
                    //--Loop the users
                    foreach ($users as $user) {

                        //--Translate the text and subject with the user-vars
                        $text = BackendMailengineModel::translateUserVars($mail['text'], $user);
                        $subject = BackendMailengineModel::translateUserVars($mail['subject'], $user);

                        //--Send the mail
                        if (BackendMailengineModel::sendMail(html_entity_decode($subject), $text, $user['email'], $user['name'], $mail)) {

                            $data = array();
                            $data['send_id'] = $mail['id'];
                            $data['user_id'] = $user['id'];

                            //--Save the send-data for the mails
                            BackendMailengineModel::insertMailUsers($data);
                        }

                        //--Add count
                        $count++;

                        //--Let the script sleep for an instant after sending x-numbers of mails
                        if ($count % 50 == 0) {
                            sleep(5);
                            set_time_limit(120);
                        }
                    }

                    //--Update status
                    BackendMailengineModel::updateStatusMailing($mail['id'], array('status' => 'finished', 'end_time' => BackendModel::getUTCDate()));

                    //--Create variables array
                    $variables = array();
                    $variables['sentOn'] = time();
                    $variables['dateFormatLong'] = BackendModel::getModuleSetting('Core', 'date_format_long') . " " . BackendModel::getModuleSetting('Core', 'time_format');
                    $variables['subject'] = $mail['subject'];
                    $variables['users'] = count($users);

                    /*$message = \Common\Mailer\Message::newInstance(
                        'Mailing ended "' . $mail['subject'] . '"'
                    )
                        ->setFrom(array($arrFrom['email'] => $arrFrom['name']))
                        ->setTo(array($arrFrom['email']))
                        ->parseHtml(
                            BACKEND_MODULES_PATH . '/Modules/Mailengine/Layout/Templates/Mails/MailingEnd.tpl',
                            $variables
                        )
                    ;
                    $this->get('mailer')->send($message);*/

                    //--Send start mail
//					$this->get('mailer')->addEmail('Mailing ended "' . $mail['subject'] . '"', BACKEND_PATH . '/Modules/Mailengine/Layout/Templates/Mails/MailingEnd.tpl', $variables, $arrFrom["email"], $arrFrom["name"]);

                }
            }
        }
    }
}