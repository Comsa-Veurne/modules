<?php
namespace Frontend\Modules\Mailengine\Engine;

use Frontend\Core\Engine\Model as FrontendModel;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the mailengine module
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Model
{
    /**
     *
     * Encrypt variable
     *
     * @param $strId
     *
     * @return string
     */
    public static function encryptId($strId)
    {
        return base64_encode($strId);
    }

    /**
     *
     * Decrypt variable
     *
     * @param $strId
     *
     * @return string
     */
    public static function decryptId($strId)
    {
        return base64_decode($strId);
    }

    /**
     *
     * Add open-mail to the database
     *
     * @param $data
     */
    public static function insertMailOpen($data)
    {
        $objDate = new \DateTime();
        $data['created_on'] = $objDate->format("Y-m-d H:i:s");

        FrontendModel::getContainer()->get('database')->insert('mailengine_stats_mail_opened', $data);
    }

    /**
     *
     * Add open-mail to the database
     *
     * @param $data
     */
    public static function insertLinkClicked($data)
    {
        $objDate = new \DateTime();
        $data['created_on'] = $objDate->format("Y-m-d H:i:s");

        FrontendModel::getContainer()->get('database')->insert('mailengine_stats_link_clicked', $data);
    }

    /**
     *
     * Get a link
     *
     * @param $id
     *
     * @return mixed
     */
    public static function getLink($id)
    {
        return FrontendModel::getContainer()->get('database')->getRecord("	SELECT * from mailengine_send_links AS i
														WHERE id = ?", array($id));
    }

    /**
     *
     * Check if the email already is subscribed
     *
     * @param $email
     *
     * @return bool
     */
    public static function isSubscribed($email)
    {
        $record = FrontendModel::getContainer()->get('database')->getRecord("	SELECT id
														FROM mailengine_users
														WHERE email= ? AND active=?", array($email, 'Y'));

        //--Check if record exists
        if (is_array($record)) {
            return true;
        }

        return false;
    }

    /**
     *
     * Subscribe the email
     *
     * @param $email
     *
     * @return bool
     */
    public static function subscribe($email)
    {
        $record = FrontendModel::getContainer()->get('database')->getRecord('	SELECT id
														FROM mailengine_users
														WHERE email= ?', array($email));
        //--Check if record exists
        if (is_array($record)) {
            $data = array();
            $data['active'] = 'Y';

            //--Update record
            FrontendModel::getContainer()->get('database')->update('mailengine_users', $data, 'id=' . $record["id"]);

            return $record["id"];
        } else {
            //--Insert email
            $data = array();
            $data['email'] = $email;
            $data['name'] = $email;
            $data['active'] = 'Y';
            $data['language'] = FRONTEND_LANGUAGE;
            $data['created_on'] = FrontendModel::getUTCDate();

            //--Add email
            return FrontendModel::getContainer()->get('database')->insert('mailengine_users', $data);
        }
    }

    /**
     *
     * Unsubscribe the email
     *
     * @param $email
     *
     * @return bool
     */
    public static function unsubscribe($email)
    {
        $record = FrontendModel::getContainer()->get('database')->getRecord('	SELECT id
														FROM mailengine_users
														WHERE email= ?', array($email));
        //--Check if record exists
        if (is_array($record)) {
            $data = array();
            $data['active'] = 'N';
            $data['unsubscribe_on'] = FrontendModel::getUTCDate();

            //--Update record
            FrontendModel::getContainer()->get('database')->update('mailengine_users', $data, 'id=' . $record["id"]);

            //--Delete the groups for the user
            self::deleteGroupFromUser($record['id']);

            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * Delete groups from a user
     *
     * @param $data
     *
     * @return int
     */
    public static function deleteGroupFromUser($id)
    {
        FrontendModel::getContainer()->get('database')->delete('mailengine_users_group', 'user_id = ?', (int)$id);
    }

    /**
     *
     * Add user to group
     *
     * @param $data
     *
     * @return int
     */
    public static function insertUserToGroup($data)
    {
        return (int)FrontendModel::getContainer()->get('database')->insert('mailengine_users_group', $data);
    }

    /**
     *
     * Get the mailings for the website.
     *
     * @return array
     */
    public static function getSendMailingsForWebiste()
    {

        return (array)FrontendModel::getContainer()->get('database')->getRecords('	SELECT i.id, i.subject, UNIX_TIMESTAMP(i.start_time) AS start_time FROM mailengine_send_mails AS i
															WHERE show_on_website = ?
															GROUP BY mail_id
															ORDER BY start_time DESC', array('Y'));
    }

    /**
     *
     * Check if the send mailing exists
     *
     * @param $id
     */
    public static function extistSend($id)
    {
        return (boolean)FrontendModel::getContainer()->get('database')->getRecord('	SELECT i.id FROM mailengine_send_mails AS i
																WHERE id = ? AND show_on_website = ?', array($id, 'Y'));
    }

    /**
     *
     * Get the send-mail
     *
     * @param $id
     *
     * @return array
     */
    public static function getSend($id)
    {
        $return = (array)FrontendModel::getContainer()->get('database')->getRecord('	SELECT i.id, i.subject, UNIX_TIMESTAMP(i.start_time) AS start_time, i.text FROM mailengine_send_mails AS i
																WHERE id = ? AND show_on_website = ?', array($id, 'Y'));

        $return['text'] = str_replace('[USERID]', self::encryptId(0), $return['text']);

        return $return;
    }
}