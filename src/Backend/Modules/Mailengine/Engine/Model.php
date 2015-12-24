<?php
namespace Backend\Modules\Mailengine\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Exception;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Csv as BackendCSV;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Core\Engine\TemplateModifiers as BackendTemplateModifiers;

/**
 * In this file we store all generic functions that we will be using in the mailengine module
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Model
{
	const QRY_DATAGRID_BROWSE_USERS = 'SELECT i.id, i.name, i.email, i.active
		 								FROM mailengine_users AS i';

	const QRY_DATAGRID_BROWSE_USERS_IN_GROUP = 'SELECT i.id, i.name, i.email, i.active
		 								FROM mailengine_users AS i
		 								INNER JOIN mailengine_users_group AS u ON u.user_id = i.id
		 								WHERE u.group_id = ?';

	const QRY_EXPORT_USERS = 'SELECT i.email, i.name, i.created_on
		 								FROM mailengine_users AS i
		 								WHERE i.active=?';

	const QRY_EXPORT_USERS_IN_GROUP = 'SELECT i.email, i.name, i.created_on
		 								FROM mailengine_users AS i
		 								INNER JOIN mailengine_users_group AS u ON u.user_id = i.id
		 								WHERE u.group_id = ? AND  i.active=?';

	const QRY_DATAGRID_BROWSE_GROUPS = 'SELECT i.id, i.title, COUNT(m.user_id) AS users
										FROM mailengine_groups AS i
										LEFT JOIN mailengine_users_group AS m ON m.group_id = i.id
										GROUP BY i.id';

	const QRY_DATAGRID_BROWSE_TEMPLATES = '	SELECT i.id, i.title, CONCAT(i.from_email, " (",i.from_name,")") AS from_address, CONCAT(i.reply_email, " (",i.reply_name,")") AS reply_address
											FROM mailengine_templates AS i';

	const QRY_DATAGRID_BROWSE_MAILS = '	SELECT i.id, i.subject, UNIX_TIMESTAMP(i.created_on) AS date
										FROM mailengine_mails AS i';

	/*const QRY_DATAGRID_BROWSE_MAIL_STATS = 'SELECT i.id, i.subject, UNIX_TIMESTAMP(i.start_time) AS date, COUNT(DISTINCT u.user_id) AS users, COUNT(DISTINCT o.user_id) AS opened,count(DISTINCT o.user_id) AS opened, ROUND((COUNT(DISTINCT o.user_id)/count(DISTINCT u.user_id)) * 100) AS percentage
											FROM mailengine_stats_mail AS i
												LEFT JOIN mailengine_stats_mail_users AS u ON u.send_id = i.id
												LEFT JOIN mailengine_stats_mail_opened AS o ON o.send_id = i.id
											GROUP BY i.id';*/

	const QRY_DATAGRID_BROWSE_MAIL_STATS = 'SELECT i.id, i.subject, UNIX_TIMESTAMP(i.start_time) AS date, COUNT(DISTINCT u.user_id) AS users
											FROM mailengine_stats_mail AS i
												LEFT JOIN mailengine_stats_mail_users AS u ON u.send_id = i.id
											GROUP BY i.id';

	/**
	 * Delete a certain item
	 *
	 * @param int $id
	 */
	public static function delete($id)
	{
		BackendModel::getContainer()->get('database')->delete('mailengine_mails', 'id = ?', (int)$id);
	}

	/**
	 * Checks if a certain item exists
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	public static function exists($id)
	{
		return (bool)BackendModel::getContainer()->get('database')->getVar('SELECT 1
			 FROM mailengine_mails AS i
			 WHERE i.id = ?
			 LIMIT 1', array((int)$id));
	}

	/**
	 * Fetches a certain item
	 *
	 * @param int $id
	 *
	 * @return array
	 */
	public static function get($id)
	{
		return (array)BackendModel::getContainer()->get('database')->getRecord('SELECT i.*, m.url
			 FROM mailengine_mails AS i
			 INNER JOIN meta AS m ON i.meta_id = m.id
			 WHERE i.id = ?', array((int)$id));
	}

	/**
	 * Retrieve the unique url for an item
	 *
	 * @param string $url
	 * @param int [optional] $id
	 *
	 * @return string
	 */
	public static function getUrl($url, $id = null)
	{
		// redefine Url
		$url = \SpoonFilter::urlise((string)$url);

		// get db
		$db = BackendModel::getContainer()->get('database');

		// new item
		if($id === null)
		{
			$numberOfItems = (int)$db->getVar('SELECT 1
				 FROM mailengine_mails AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ?
				 LIMIT 1', array(BL::getWorkingLanguage(), $url));

			// already exists
			if($numberOfItems != 0)
			{
				// add number
				$url = BackendModel::addNumber($url);

				// try again
				return self::getUrl($url);
			}
		}
		// current item should be excluded
		else
		{
			$numberOfItems = (int)$db->getVar('SELECT 1
				 FROM mailengine_mails AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ? AND i.id != ?
				 LIMIT 1', array(BL::getWorkingLanguage(), $url, $id));

			// already exists
			if($numberOfItems != 0)
			{
				// add number
				$url = BackendModel::addNumber($url);

				// try again
				return self::getUrl($url, $id);
			}
		}

		// return the unique Url!
		return $url;
	}

	/**
	 * Insert an item in the database
	 *
	 * @param array $data
	 *
	 * @return int
	 */
	public static function insert(array $data)
	{
		$data['created_on'] = BackendModel::getUTCDate();

		return (int)BackendModel::getContainer()->get('database')->insert('mailengine_mails', $data);
	}

	/**
	 * Updates an item
	 *
	 * @param int $id
	 * @param array $data
	 */
	public static function update($id, array $data)
	{
		$data['edited_on'] = BackendModel::getUTCDate();

		BackendModel::getContainer()->get('database')->update('mailengine_mails', $data, 'id = ?', (int)$id);
	}

	/**
	 * Delete a certain item
	 *
	 * @param int $id
	 */
	public static function deleteUser($id)
	{
		BackendModel::getContainer()->get('database')->delete('mailengine_users', 'id = ?', (int)$id);
	}

	/**
	 * Checks if a certain item exists
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	public static function existsUser($id)
	{
		return (bool)BackendModel::getContainer()->get('database')->getVar('SELECT 1
			 FROM mailengine_users AS i
			 WHERE i.id = ?
			 LIMIT 1', array((int)$id));
	}

	/**
	 * Fetches a certain item
	 *
	 * @param int $id
	 *
	 * @return array
	 */
	public static function getUser($id)
	{
		return (array)BackendModel::getContainer()->get('database')->getRecord('SELECT i.*
			 FROM mailengine_users AS i
			 WHERE i.id = ?', array((int)$id));
	}

	/**
	 * Fetches a certain item
	 *
	 * @param int $email
	 *
	 * @return array
	 */
	public static function getUserFromEmail($email)
	{
		return (array)BackendModel::getContainer()->get('database')->getRecord('SELECT i.*
			 FROM mailengine_users AS i
			 WHERE i.email = ?', array((string)$email));
	}

	/**
	 * Insert an item in the database
	 *
	 * @param array $data
	 *
	 * @return int
	 */
	public static function insertUser(array $data)
	{
		$data['created_on'] = BackendModel::getUTCDate();

		return (int)BackendModel::getContainer()->get('database')->insert('mailengine_users', $data);
	}

	/**
	 * Updates an item
	 *
	 * @param int $id
	 * @param array $data
	 */
	public static function updateUser($id, array $data)
	{
		if(isset($data['active']) && $data['active'] == 'Y')
		{
			$data['unsubscribe_on'] = '0000-00-00 00:00:00';
		}
		BackendModel::getContainer()->get('database')->update('mailengine_users', $data, 'id = ?', (int)$id);
	}

	/**
	 * Delete a certain item
	 *
	 * @param int $id
	 */
	public static function deleteGroup($id)
	{
		BackendModel::getContainer()->get('database')->delete('mailengine_groups', 'id = ?', (int)$id);
	}

	/**
	 * Checks if a certain item exists
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	public static function existsGroup($id)
	{
		return (bool)BackendModel::getContainer()->get('database')->getVar('SELECT 1
			 FROM mailengine_groups AS i
			 WHERE i.id = ?
			 LIMIT 1', array((int)$id));
	}

	/**
	 * Fetches a certain item
	 *
	 * @param int $id
	 *
	 * @return array
	 */
	public static function getGroup($id)
	{
		return (array)BackendModel::getContainer()->get('database')->getRecord('SELECT i.*
			 FROM mailengine_groups AS i
			 WHERE i.id = ?', array((int)$id));
	}

	/**
	 * Insert an item in the database
	 *
	 * @param array $data
	 *
	 * @return int
	 */
	public static function insertGroup(array $data)
	{
		$data['created_on'] = BackendModel::getUTCDate();

		return (int)BackendModel::getContainer()->get('database')->insert('mailengine_groups', $data);
	}

	/**
	 * Updates an item
	 *
	 * @param int $id
	 * @param array $data
	 */
	public static function updateGroup($id, array $data)
	{
		BackendModel::getContainer()->get('database')->update('mailengine_groups', $data, 'id = ?', (int)$id);
	}

	/**
	 *
	 * Get all the users
	 *
	 * @return mixed
	 */
	public static function getAllUsers()
	{
		$users = BackendModel::getContainer()->get('database')->getRecords("SELECT i.id, i.email, i.name
													FROM mailengine_users AS i");

		return $users;
	}

	/**
	 *
	 * Get all the groups
	 *
	 * @return mixed
	 */
	public static function getAllGroups()
	{
		$groups = BackendModel::getContainer()->get('database')->getRecords("SELECT i.id, i.title
													FROM mailengine_groups AS i");

		return $groups;
	}

	/**
	 *
	 * Get all the groups for a dropdown
	 *
	 * @param $count
	 *
	 * @return mixed
	 */
	public static function getAllGroupsForDropdown($count = false)
	{
		if($count == false)
		{
			$groups = BackendModel::getContainer()->get('database')->getPairs("SELECT i.id, i.title
														FROM mailengine_groups AS i");
		}
		else
		{
			$groups = BackendModel::getContainer()->get('database')->getPairs("	SELECT i.id, CONCAT(i.title, ' (', count(u.user_id), ')') AS title
														FROM mailengine_groups AS i
															INNER JOIN mailengine_users_group AS u ON u.group_id = i.id
														GROUP BY i.id");
		}

		return $groups;
	}

	/**
	 *
	 * Get all the profiles
	 *
	 * @param $count
	 *
	 * @return mixed
	 */
	public static function getAllProfiles()
	{
		$return = BackendModel::getContainer()->get('database')->getRecords("SELECT i.id, i.email, i.display_name AS name
														FROM profiles AS i
														WHERE status = ?", array("active"));

		return $return;
	}

	/**
	 *
	 * Get all the profilegroups for a dropdown
	 *
	 * @param $count
	 *
	 * @return mixed
	 */
	public static function getAllProfileGroupsForDropdown($count = false)
	{
		if($count == false)
		{
			$groups = BackendModel::getContainer()->get('database')->getPairs("SELECT i.id, i.name
														FROM profiles_groups AS i");
		}
		else
		{
			$groups = BackendModel::getContainer()->get('database')->getPairs("	SELECT i.id, CONCAT(i.name, ' (', count(u.profile_id), ')') AS title
														FROM profiles_groups AS i
															INNER JOIN profiles_groups_rights AS u ON u.group_id = i.id
															INNER JOIN profiles AS p ON p.id= u.profile_id
														WHERE status=?
														GROUP BY i.id", array('active'));
		}

		return $groups;
	}

	/**
	 *
	 * Get all the users for a group
	 *
	 * @param $id
	 *
	 * @return array()
	 */
	public static function getUsersForGroup($id)
	{

		$users = BackendModel::getContainer()->get('database')->getPairs("SELECT i.user_id, u.email
													FROM mailengine_users_group AS i
													INNER JOIN mailengine_users AS u ON u.id = i.user_id
													WHERE i.group_id = ? AND u.active=?", array($id, 'Y'));

		return $users;
	}

	/**
	 *
	 * Get all the groups for a user
	 *
	 * @param $id
	 *
	 * @return array()
	 */
	public static function getGroupsForUser($id)
	{

		$users = BackendModel::getContainer()->get('database')->getPairs("SELECT i.group_id, g.title
													FROM mailengine_users_group AS i
													INNER JOIN mailengine_groups AS g ON g.id = i.group_id
													WHERE i.user_id = ?", array($id));

		return $users;
	}

	/**
	 *
	 * Delete users from a group
	 *
	 * @param $id
	 *
	 * @return int
	 */
	public static function deleteUserFromGroup($id)
	{
		BackendModel::getContainer()->get('database')->delete('mailengine_users_group', 'group_id = ?', (int)$id);
	}

	/**
	 *
	 * Delete groups from a user
	 *
	 * @param $id
	 *
	 * @return int
	 */
	public static function deleteGroupFromUser($id)
	{
		BackendModel::getContainer()->get('database')->delete('mailengine_users_group', 'user_id = ?', (int)$id);
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
		return (int)BackendModel::getContainer()->get('database')->insert('mailengine_users_group', $data);
	}

	/**
	 * Delete a certain item
	 *
	 * @param int $id
	 */
	public static function deleteTemplate($id)
	{
		BackendModel::getContainer()->get('database')->delete('mailengine_templates', 'id = ?', (int)$id);
	}

	/**
	 * Checks if a certain item exists
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	public static function existsTemplate($id)
	{
		return (bool)BackendModel::getContainer()->get('database')->getVar('SELECT 1
			 FROM mailengine_templates AS i
			 WHERE i.id = ?
			 LIMIT 1', array((int)$id));
	}

	/**
	 * Fetches a certain item
	 *
	 * @param int $id
	 *
	 * @return array
	 */
	public static function getTemplate($id)
	{
		return (array)BackendModel::getContainer()->get('database')->getRecord('SELECT i.*
			 FROM mailengine_templates AS i
			 WHERE i.id = ?', array((int)$id));
	}

	/**
	 * Insert an item in the database
	 *
	 * @param array $data
	 *
	 * @return int
	 */
	public static function insertTemplate(array $data)
	{
		$data['created_on'] = BackendModel::getUTCDate();

		return (int)BackendModel::getContainer()->get('database')->insert('mailengine_templates', $data);
	}

	/**
	 * Updates an item
	 *
	 * @param int $id
	 * @param array $data
	 */
	public static function updateTemplate($id, array $data)
	{
		BackendModel::getContainer()->get('database')->update('mailengine_templates', $data, 'id = ?', (int)$id);
	}

	/**
	 * Fetches all templates for the dropdown
	 *
	 * @return array
	 */
	public static function getAllTemplatesForDropdown()
	{
		return (array)BackendModel::getContainer()->get('database')->getPairs('SELECT i.id, i.title
			 FROM mailengine_templates AS i
			 WHERE i.hidden', array('N'));
	}

	/**
	 *
	 * Get the unique emails for several groups
	 *
	 * @param $groups
	 *
	 * @return array
	 */
	public static function getUniqueEmailsFromGroups($groups)
	{
		if(is_numeric($groups))
		{
			$groups = (array)$groups;
		}

		if(empty($groups) || empty($groups[0]))
		{
			return array();
		}

		return (array)BackendModel::getContainer()->get('database')->getRecords('	SELECT i.id, i.email, i.name
															FROM mailengine_users AS i
																INNER JOIN mailengine_users_group AS ug ON ug.user_id = i.id
															WHERE ug.group_id IN (' . implode(',', $groups) . ') AND i.active = ?
															GROUP BY i.id', array('Y'));
	}

	/**
	 *
	 * Get the unique emails for several groups
	 *
	 * @param $groups
	 *
	 * @return array
	 */
	public static function getUniqueEmailsFromProfileGroups($groups)
	{

		if(is_numeric($groups))
		{
			$groups = (array)$groups;
		}

		if(empty($groups) || empty($groups[0]))
		{
			return array();
		}

		return (array)BackendModel::getContainer()->get('database')->getRecords('	SELECT i.id, i.email, i.display_name AS name
															FROM profiles AS i
																INNER JOIN profiles_groups_rights AS pg ON pg.profile_id = i.id
															WHERE pg.group_id IN (' . implode(',', $groups) . ') AND i.status = ?
															GROUP BY i.id', array('active'));
	}

	/**
	 *
	 * Get the unique emails for the profiles
	 *
	 * @param $groups
	 *
	 * @return array
	 */
	public static function getUniqueEmailsFromProfiles()
	{
		return (array)BackendModel::getContainer()->get('database')->getRecords('	SELECT i.id, i.email, i.display_name AS name
															FROM profiles AS i
															WHERE  i.status = ?
															GROUP BY i.id', array('active'));
	}

	/**
	 *
	 * Get the preview
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public static function getPreview($id)
	{

		//--Get mailing
		$mailing = self::get($id);

		//--Get the template
		$template = self::getTemplate($mailing['template_id']);

		$text = self::createMail($mailing, $template);

		return $text;
	}

	/**
	 *
	 * Insert mailing in the database to send with the cronjob
	 *
	 * @param $id
	 *
	 * @param $start
	 *
	 * @return array()
	 */
	public static function insertMailingInReadyToSendDatabase($id, $start = "")
	{
		//--Get mailing
		$mailing = self::get($id);

		//--Get the template
		$template = self::getTemplate($mailing['template_id']);

		//--Create basic mail
		$text = self::createMail($mailing, $template);

		//--Check start time
		$start = $start == "" ? BackendModel::getUTCDate() : $start;

		//--Create array to insert the mailing
		$mailingInsert = array();
		$mailingInsert['mail_id'] = $id;
		$mailingInsert['domain'] = $_SERVER['HTTP_HOST'];
		$mailingInsert['subject'] = $mailing['subject'];
		$mailingInsert['text'] = $text;
		$mailingInsert['status'] = "waiting";
		$mailingInsert['show_on_website'] = $mailing['show_on_website'];
		$mailingInsert['start_time'] = $start;
		$mailingInsert['from_name'] = $template['from_name'];
		$mailingInsert['from_email'] = $template['from_email'];
		$mailingInsert['reply_name'] = $template['reply_name'];
		$mailingInsert['reply_email'] = $template['reply_email'];

		//--Insert in Db
		$mailingInsertId = (int)BackendModel::getContainer()->get('database')->insert('mailengine_send_mails', $mailingInsert);

		//--Translate the url's
		$text = self::translateUrls($mailingInsertId, $text);

		//--Add open-image tag.
		$text = self::addOpenTag($mailingInsertId, $text);

		//--Translate HOST
		$text = str_replace("[[HOST]]", $_SERVER['HTTP_HOST'], $text);

		//--Update mailing with the translated text
		BackendModel::getContainer()->get('database')->update("mailengine_send_mails", array("text" => $text), "id=$mailingInsertId");

		return $mailingInsertId;
	}

	/**
	 *
	 * Replace the links with coded links
	 *
	 * @param $id
	 * @param $text
	 *
	 * @return array()
	 */
	public static function translateUrls($id, $text)
	{
		//--Get the module-url
		$urlBlock = BackendModel::getURLForBlock('Mailengine', 'MailengineClick');

		//--Search all url's with single quote
		$strPattern = "/href='([a-zA-Z0-9\S]*)'/";
		preg_match_all($strPattern, $text, $arrMatchesEnkelQuote);

		//--Loop all single quotes
		if(!empty($arrMatchesEnkelQuote) && !empty($arrMatchesEnkelQuote[1]))
		{
			foreach($arrMatchesEnkelQuote[1] as $strMatch)
			{
				//--Save links in an array
				$arrMatches[] = $strMatch;
			}
		}

		//--Search all url's with double quote
		$strPattern = '/href="([a-zA-Z0-9\S]*)"/';
		preg_match_all($strPattern, $text, $arrMatchesDubbelQuote);

		//--Loop all single quotes
		if(!empty($arrMatchesDubbelQuote) && !empty($arrMatchesDubbelQuote[1]))
		{
			foreach($arrMatchesDubbelQuote[1] as $strMatch)
			{
				//--Save links in an array
				$arrMatches[] = $strMatch;
			}
		}

		//--Loop all url's (single and double quotes)
		if(!empty($arrMatches))
		{

			//--Teller initialiseren
			$intTeller = 1;

			//--Loop all the url's and calculate position
			foreach($arrMatches as $strMatch)
			{
				//--Split the text  (double quote)
				$arrExplode = explode('href="' . $strMatch . '"', $text);

				if(empty($arrExplode))
				{
					//--Split the text (single quote)
					$arrExplode = explode("href='" . $strMatch . "'", $text);
				}

				//--Check if split succeeded
				if(!empty($arrExplode))
				{
					//--Loop all the explodes
					foreach($arrExplode as $intKey => $strExplode)
					{
						if($intKey == 0)
						{
							//--Save translated link
							$data = array();
							$data["send_id"] = $id;
							$data["url"] = $strMatch;

							$intLinkId = self::insertTranslatedLink($data);

							//--Encrypt the id
							$strLinkId = self::encryptId($intLinkId) . "-[[USERID]]";

							//--Create link
							$strLink = "http://[[HOST]]" . $urlBlock . "/" . $strLinkId;

							//--Adapt mail with the explode
							$text = $strExplode . 'href="' . $strLink . '" target="_blank"';
						}
						else
						{
							//--2nd part is just added (without the url because this was already translated in the first part
							if($intKey == 1)
							{
								$text .= $strExplode;
							}
							else
							{
								//--It's not the first loop, but everything can just be added after each other.  This is to give simular url's different ids.
								$text .= 'href="' . $strMatch . '" target="_blank"' . $strExplode;
							}
						}
					}
				}

				//--Count
				$intTeller++;
			}
		}

		return $text;
	}

	public static function addOpenTag($id, $text)
	{
		//--Get the module-url
		$urlBlock = BackendModel::getURLForBlock('Mailengine', 'MailengineImage');

		//--Create link
		$tag = "http://[[HOST]]/" . $urlBlock . "/" . self::encryptId($id) . "-[[USERID]]";

		return str_replace('</body>', '<img src="' . $tag . '"/></body>', $text);
	}

	/**
	 * Insert the link in the database
	 *
	 * @param array $data
	 *
	 * @return int
	 */
	public static function insertTranslatedLink(array $data)
	{
		return (int)BackendModel::getContainer()->get('database')->insert('mailengine_send_links', $data);
	}

	/**
	 * Insert the user for the mailing in the database
	 *
	 * @param array $data
	 *
	 * @return int
	 */
	public static function insertUserInReadyToSendDatabase(array $data)
	{
		return (int)BackendModel::getContainer()->get('database')->insert('mailengine_send_users', $data);
	}

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
	function decryptId($strId)
	{
		return base64_decode($strId);
	}

	/**
	 *
	 * Save the users for the mailing that will be send.
	 *
	 * @param $id
	 * @param $groups
	 */
	public static function insertUsersInReadyToSendDatabase($id, $groups, $profileGroups, $profilesAll)
	{
		$groups = explode(',', $groups);
		$profileGroups = explode(',', $profileGroups);
		$users = array();
		$usersTemp = array();

		//--Get the users from the group
		$usersTemp = self::getUniqueEmailsFromGroups($groups);

		//--Get the unique users for the different groups
		$users = array_merge($users, $usersTemp);

		//--Check if all the profiles need to be added
		if($profilesAll == 1)
		{
			//--Get all the profiles
			$usersTemp = self::getUniqueEmailsFromProfiles();

			//--Merge
			$users = array_merge($users, $usersTemp);
		}
		else
		{
			//--Get the unique users for the different profile groups
			$usersTemp = self::getUniqueEmailsFromProfileGroups($profileGroups);

			//--Merge
			$users = array_merge($users, $usersTemp);
		}

		//--Loop all the users and set the e-mail as key to remove duplicate e-mails
		$usersTemp = array();
		foreach($users as $user)
		{
			if(!isset($usersTemp[$user['email']]))
			{
				$usersTemp[$user['email']] = $user;
			}
		}

		//--Reset users-array to the unduplicate array
		$users = $usersTemp;

		$counter = 1;
		foreach($users as $user)
		{

			$data = array();

			$data['send_id'] = $id;
			$data['user_id'] = $counter; //$user['id'];
			$data['email'] = $user['email'];
			$data['name'] = $user['name'];
			$data['params'] = "";

			//--Add user to the database
			self::insertUserInReadyToSendDatabase($data);

			//--Add the counter
			$counter++;
		}
	}

	/*
	*
	* Get the mailings which are waiting to be send
	*
	*/
	public static function getWaitingMailings()
	{

		return BackendModel::getContainer()->get('database')->getRecords("	SELECT i.* FROM mailengine_send_mails AS i
														WHERE status=? AND start_time <= NOW()", array('waiting'));
	}

	/**
	 *
	 * Get the users for the waiting mail
	 *
	 */
	public static function getUsersForWaitingMail($id)
	{
		return BackendModel::getContainer()->get('database')->getRecords("	SELECT i.* FROM mailengine_send_users AS i
														WHERE send_id=? ", array((int)$id));
	}

	/**
	 *
	 * Translate the user id
	 *
	 * @param $text
	 * @param $user
	 *
	 * @return array()
	 */
	public static function translateUserVars($text, $user)
	{

		//--Translate the USERID with de encrypted userid
		$text = str_replace('[[USERID]]', self::encryptId($user['user_id']), $text);

		//--Replace the name
		$text = str_replace('[[NAME]]', $user['name'], $text);

		//--Replace the email
		$text = str_replace('[[EMAIL]]', $user['email'], $text);

		return $text;
	}

	public static function sendMail($subject, $text, $email_receipient, $name_receipient, $mail)
	{
		$message = \Common\Mailer\Message::newInstance($subject);
		$message->setTo(array($email_receipient => (\SpoonFilter::isEmail($name_receipient) ? null : $name_receipient)));
		$message->setFrom(array($mail['from_email'] => $mail['from_name']));
		$message->setReplyTo(array($mail['reply_email'] => $mail['reply_name']));
		$message->setBody($text, 'text/html');
		$message->addPart(strip_tags($text));
		$message->setCharset(SPOON_CHARSET);

		BackendModel::get('mailer')->send($message);

		// mailer type
//		$mailerType = BackendModel::getModuleSetting('Core', 'mailer_type', 'mail');
//
//		// create new SpoonEmail-instance
//		$email = new \SpoonEmail();
//		$email->setTemplateCompileDirectory(BACKEND_CACHE_PATH . '/compiled_templates');
//
//		// send via SMTP
//		if($mailerType == 'smtp')
//		{
//
//
//			// get settings
//			$SMTPServer = BackendModel::getModuleSetting('Core', 'smtp_server');
//			$SMTPPort = BackendModel::getModuleSetting('Core', 'smtp_port', 25);
//			$SMTPUsername = BackendModel::getModuleSetting('Core', 'smtp_username');
//			$SMTPPassword = BackendModel::getModuleSetting('Core', 'smtp_password');
//
//			// set server and connect with SMTP
//			$email->setSMTPConnection($SMTPServer, $SMTPPort, 10);
//
//			// set authentication if needed
//			if($SMTPUsername !== null && $SMTPPassword !== null) $email->setSMTPAuth($SMTPUsername, $SMTPPassword);
//		}
//
//		// set some properties
//		$email->setFrom($mail['from_email'], $mail['from_name']);
//
//		//--If the receipient name is an email, set name to null
//		if(\SpoonFilter::isEmail($name_receipient))
//		{
//			$name_receipient = null;
//		}
//
//		$email->addRecipient($email_receipient, $name_receipient);
//		$email->setReplyTo($mail['reply_email'], $mail['reply_name']);
//		$email->setSubject($subject);
//		$email->setHTMLContent($text);
//		$email->setCharset(SPOON_CHARSET);
//		$email->setContentTransferEncoding('base64');
//		$email->setPlainContent();

		// send the email
//		return $email->send();
	}

	/**
	 *
	 * Update the status of the mailing
	 *
	 * @param $id
	 * @param $data
	 */
	public static function updateStatusMailing($id, $data)
	{
		BackendModel::getContainer()->get('database')->update('mailengine_send_mails', $data, 'id = ?', (int)$id);
	}

	/**
	 *
	 * Add user to mail-stats to the database
	 *
	 * @param $data
	 */
	public static function insertMailUsers($data)
	{
		$data['created_on'] = BackendModel::getUTCDate();

		BackendModel::getContainer()->get('database')->insert('mailengine_stats_mail_users', $data);
	}

	/**
	 *
	 * Insert mail in the stats database
	 *
	 * @param $data
	 */
	public static function insertMailToStats($data)
	{
		BackendModel::getContainer()->get('database')->insert('mailengine_stats_mail', $data);
	}

	/**
	 * Check if the mail-stats exists
	 *
	 * @param $id
	 * @param $id
	 *
	 * @return array
	 */
	public static function existsStatsMail($id)
	{
		return (bool)BackendModel::getContainer()->get('database')->getVar('SELECT 1
			 FROM mailengine_stats_mail AS i
			 WHERE i.id = ?
			 LIMIT 1', array((int)$id));
	}

	/**
	 *
	 * Get the mail stats
	 *
	 * @param $id
	 *
	 * @return array
	 */
	public static function getStatsMail($id)
	{
		$return = (array)BackendModel::getContainer()->get('database')->getRecord('SELECT i.*, UNIX_TIMESTAMP(i.start_time) AS date, COUNT(DISTINCT u.user_id) AS users
														FROM mailengine_stats_mail AS i
															LEFT JOIN mailengine_stats_mail_users AS u ON u.send_id = i.id
														WHERE i.id = ?
														GROUP BY i.id', array((int)$id));

		$returnOpened = (array)BackendModel::getContainer()->get('database')->getRecord('SELECT i.*,  COUNT(DISTINCT o.user_id) AS opened
														FROM mailengine_stats_mail AS i
															LEFT JOIN mailengine_stats_mail_opened AS o ON o.send_id = i.id
														WHERE i.id = ?
														GROUP BY i.id', array((int)$id));


		//--Format date
		$return["date"] = BackendDataGridFunctions::getLongDate($return["date"]);
		$return["percentage"] = BackendTemplateModifiers::formatNumber(round($returnOpened["opened"] / $return['users'] * 100, 0));

		return $return;
	}

	/**
	 *
	 * Get the stats for a mail for opening the mail by day
	 *
	 * @param $id
	 *
	 * @return array
	 */
	public static function getStatsMailOpenedByDay($id)
	{

		$return = (array)BackendModel::getContainer()->get('database')->getRecords('SELECT COUNT(i.user_id) AS opened, DATE_FORMAT(i.created_on, "%Y%m%d") AS date_group_by
																FROM mailengine_stats_mail_opened AS i
															WHERE i.send_id = ?
															GROUP by date_group_by', array((int)$id));

		$xAxis = array();
		$seriesData = array();

		//--Loop the dates and format the date
		foreach($return as &$row)
		{
			$xAxis[] = substr($row['date_group_by'], 6, 2) . "-" . substr($row['date_group_by'], 4, 2) . "-" . substr($row['date_group_by'], 0, 4);
			$seriesData[] = (int)$row['opened'];
		}

		$mailOpenedByDateChart = array();
		$mailOpenedByDateChart['title'] = json_encode(ucfirst(BL::getLabel('MailsOpenedByDay', 'Mailengine')));
		$mailOpenedByDateChart['xAxis'] = json_encode($xAxis);
		$mailOpenedByDateChart['series'] = json_encode(array('name' => ucfirst(BL::getLabel('MailsOpened', 'Mailengine')), 'data' => $seriesData));

		return $mailOpenedByDateChart;
	}

	/**
	 *
	 * Get the stats for a mail for opening the mail by hour
	 *
	 * @param $id
	 *
	 * @return array
	 */
	public static function getStatsMailOpenedByHour($id)
	{

		$return = (array)BackendModel::getContainer()->get('database')->getRecords('SELECT COUNT(i.user_id) AS opened, DATE_FORMAT(i.created_on, "%H") AS date_group_by
																FROM mailengine_stats_mail_opened AS i
															WHERE i.send_id = ?
															GROUP by date_group_by
															ORDER BY date_group_by', array((int)$id));

		$xAxis = array();
		$seriesData = array();

		$hour = 0;
		while($hour < 24)
		{
			//--Alter the hour (add 0 before single digit hour)
			$hourCalculation = str_pad($hour, 2, "0", STR_PAD_LEFT);

			//--Set opened
			$opened = 0;

			//--Loop the dates and format the date
			foreach($return as &$row)
			{
				if($hourCalculation == $row['date_group_by'])
				{
					$opened = (int)$row['opened'];
				}
			}

			//--Add the data
			$xAxis[] = $hourCalculation . ':00';
			$seriesData[] = $opened;

			//--Add the hour
			$hour++;
		}

		$mailOpenedByDateChart = array();
		$mailOpenedByDateChart['title'] = json_encode(ucfirst(BL::getLabel('MailsOpenedByHour', 'Mailengine')));
		$mailOpenedByDateChart['xAxis'] = json_encode($xAxis);
		$mailOpenedByDateChart['series'] = json_encode(array('name' => ucfirst(BL::getLabel('MailsOpened', 'Mailengine')), 'data' => $seriesData));

		return $mailOpenedByDateChart;
	}

	/**
	 *
	 * Get the total links from the send mail
	 *
	 * @param $id
	 *
	 * @return array
	 */
	public static function getStatsLinksClickedTotalArray($id)
	{
		return (array)BackendModel::getContainer()->get('database')->getRecords('SELECT i.id, count(l.id) as clicked, i.url
															FROM mailengine_send_links AS i
																LEFT JOIN mailengine_stats_link_clicked AS l ON l.link_id = i.id
															WHERE i.send_id = ?
															group by i.id', array((int)$id));
	}

	/**
	 *
	 * Get the stats for the clicked links
	 *
	 * @param $id
	 *
	 * @return array
	 */
	public static function getStatsLinksClickedTotal($id)
	{

		//--Get the return values of the query
		$return = self::getStatsLinksClickedTotalArray($id);

		//--Loop the dates and format the date
		$series = array();
		foreach($return as $row)
		{
			$series[] = array('name' => str_replace('http://', '', $row['url']), 'data' => array((int)$row['clicked']));
		}

		$mailLinksClickedTotalChart = array();
		$mailLinksClickedTotalChart['title'] = json_encode(ucfirst(BL::getLabel('LinksClickedTotal', 'Mailengine')));
		$mailLinksClickedTotalChart['yAxis'] = json_encode(ucfirst(BL::getLabel('NumberOfLinksClicked', 'Mailengine')));

		$mailLinksClickedTotalChart['xAxis'] = json_encode(ucfirst(BL::getLabel('LinksClicked', 'Mailengine')));
		$mailLinksClickedTotalChart['series'] = json_encode($series);

		return $mailLinksClickedTotalChart;
	}

	/**
	 *
	 * Get the stats for the clicked links by day
	 *
	 * @param $id
	 *
	 * @return array
	 */
	public static function getStatsLinksClickedByDay($id)
	{

		$return = (array)BackendModel::getContainer()->get('database')->getRecords('SELECT i.id, count(i.id) as clicked, DATE_FORMAT(l.created_on, "%Y%m%d") AS date_group_by, i.url
															FROM mailengine_send_links AS i
																LEFT JOIN mailengine_stats_link_clicked AS l ON l.link_id = i.id
															WHERE i.send_id = ?
															GROUP BY i.id, date_group_by
															ORDER BY date_group_by ASC, i.id ASC', array((int)$id));
		$dates = array();
		$linksStats = array();
		$linksStatsTemp = array();
		$series = array();
		$xAxis = array();

		//--Loop the dates and format the date
		foreach($return as &$row)
		{
			if(!empty($row['date_group_by']))
			{
				$dates[$row['date_group_by']] = 1;
				$linksStatsTemp[$row['date_group_by']][$row['id']] = array('clicked' => (int)$row['clicked'], 'url' => str_replace('http://', '', $row['url']));
			}
		}

		//--Calculate the first date
		//--Get the keys
		$datesKeys = array_keys($dates);

		//--Get the first key
		$firstDate = array_shift($datesKeys);
		while($firstDate == "")
		{
			$firstDate = array_shift($datesKeys);

			if(count($datesKeys) == 0)
			{
				return false;
				break;
			}
		}

		//--Set date
		$objDate = new \DateTime(substr($firstDate, 0, 4) . '/' . substr($firstDate, 4, 2) . '/' . substr($firstDate, 6, 2));
		$objDateNow = new \DateTime();

		//--While the date is smaller then today, look for other links/dates
		while($objDate->format('Ymd') <= $objDateNow->format('Ymd'))
		{
			if(isset($linksStatsTemp[$objDate->format('Ymd')]))
			{
				//--Transfer data from one array to another
				$linksStats[$objDate->format('Ymd')] = $linksStatsTemp[$objDate->format('Ymd')];

				//--Delete the key
				unset($linksStatsTemp[$objDate->format('Ymd')]);
			}
			else
			{
				//--Set value of the array to empty
				$linksStats[$objDate->format('Ymd')] = array();
			}
			//--Check if there are still dates in the array, if not, stop the while loop
			if(count($linksStatsTemp) == 0)
			{
				break;
			}

			//-- Add day
			$objDate->modify('+ 1 day');
		}

		//--Create xAxis (loop all the dates)
		foreach($linksStats as $key => $stats)
		{
			$xAxis[] = substr($key, 6, 2) . '/' . substr($key, 4, 2) . '/' . substr($key, 0, 4);
		}

		//--Get all links
		$links = self::getAllLinks($id);

		//--Create data(series) for the graph
		foreach($links as $link)
		{
			//--Create empty array
			$data = array();

			//--Loop all the stats
			foreach($linksStats as $stats)
			{
				//--Check if there are stats for this link
				if(isset($stats[$link['id']]))
				{
					//--Add clicks to the data array
					$data[] = $stats[$link['id']]['clicked'];
				}
				else
				{
					//--No clicks found, so add 0
					$data[] = 0;
				}
			}

			//--Add data to array with all the data
			$series[] = array('name' => str_replace("http://", "", $link['url']), 'data' => $data);
		}

		$mailLinksClickedByDayChart = array();
		$mailLinksClickedByDayChart['title'] = json_encode(ucfirst(BL::getLabel('LinksClickedByDay', 'Mailengine')));
		$mailLinksClickedByDayChart['yAxis'] = json_encode(ucfirst(BL::getLabel('NumberOfLinksClicked', 'Mailengine')));
		$mailLinksClickedByDayChart['xAxis'] = json_encode($xAxis);
		$mailLinksClickedByDayChart['series'] = json_encode($series);

		return $mailLinksClickedByDayChart;
	}

	/**
	 *
	 * Get all the sended links
	 *
	 * @param (int) $id
	 *
	 * @return mixed
	 */
	public static function getAllLinks($id)
	{
		$return = BackendModel::getContainer()->get('database')->getRecords('SELECT *
													FROM mailengine_send_links AS i
													WHERE i.send_id = ?
													ORDER BY i.id', array($id));

		return $return;
	}

	/**
	 *
	 * Export users
	 *
	 * @param int $id
	 */
	public static function exportUsers($id = 0)
	{

		if($id > 0)
		{
			$users = BackendModel::getContainer()->get('database')->getRecords(self::QRY_EXPORT_USERS_IN_GROUP, array($id, 'Y'));
		}
		else
		{
			$users = BackendModel::getContainer()->get('database')->getRecords(self::QRY_EXPORT_USERS, array('Y'));
		}

		// set the filename and path
		$filename = 'addresses-mailengine' . \SpoonDate::getDate('YmdHi') . '.csv';
		$path = BACKEND_CACHE_PATH . '/mailengine/' . $filename;

		// generate the CSV and download the file
		BackendCSV::arrayToFile($path, $users, array(BL::lbl('Email'), BL::lbl('Name'), BL::lbl('Created')), null, ';', '"', true);
	}

	public static function exportDemo()
	{
		// set the filename and path
		$filename = 'export_demo.csv';
		$path = BACKEND_CACHE_PATH . '/mailengine/' . $filename;

		$users = array(array('example1@test.be', "Example 1"), array('example2@test.be', "Example 2"), array('example3@test.be', "Example 3"), array('example4@test.be', "Example 4"),);

		BackendCSV::arrayToFile($path, $users, array(BL::lbl('Email'), BL::lbl('Name')), null, ';', '"', true);
	}

	/**
	 *
	 * Check if the user already linked to the group
	 *
	 * @param $userId
	 * @param $groupId
	 *
	 * @return bool
	 */
	public static function existsUserGroup($userId, $groupId)
	{
		return (bool)BackendModel::getContainer()->get('database')->getVar('SELECT 1
			 FROM mailengine_users_group AS i
			 WHERE i.user_id = ? AND i.group_id = ?
			 LIMIT 1', array((int)$userId, (int)$groupId));
	}

	/**
	 * Create the basic mail
	 *
	 * @param $mailing
	 * @param $template
	 */
	public static function createMail($mailing, $template)
	{
		//--Replace the mail from the template
		$text = str_replace("[[MAIL]]", $mailing["text"], $template["template"]);

		//--Replace unsubscribe
		$unsubscribe = "http://" . $_SERVER['HTTP_HOST'] . BackendModel::getURLForBlock('Mailengine', 'MailengineUnsubscribe');
		$text = str_replace("[[UNSUBSCRIBE]]", $unsubscribe, $text);

		//--Replace the SUBJECT tag in the mailing
		$text = str_replace("[[SUBJECT]]", $mailing['subject'], $text);

		//--Add html-tags
		$text = '<html><head><title>' . $mailing['subject'] . '</title><style>' . $template['css'] . '</style></head><body>' . $text . '</body></html>';

		//--Replace relative paths from the images.
		$text = str_replace('src="/src/Frontend/', 'src="http://' . $_SERVER['HTTP_HOST'] . '/src/Frontend/', $text);
		$text = str_replace("src='/src/Frontend/", "src='http://" . $_SERVER['HTTP_HOST'] . "/src/Frontend/", $text);

		$text = str_replace('href="/src/Frontend/', 'href="http://' . $_SERVER['HTTP_HOST'] . '/src/Frontend/', $text);
		$text = str_replace("href='/src/Frontend/", "href='http://" . $_SERVER['HTTP_HOST'] . "/src/Frontend/", $text);

		return $text;
	}

	public static function getStatsOverlay($id)
	{

		//--Get the mail
		$stats = self::getStatsMail($id);

		$mail = $stats['text'];

		//--Get the total stats from the clicked links
		$links = self::getStatsLinksClickedTotalArray($id);

		$total = 0;

		//--Calculate the total
		foreach($links as $link)
		{
			$total += $link["clicked"];
		}

		//--Loop the links
		foreach($links as $link)
		{

			//--Encrypt the id
			$encrypt = self::encryptId($link['id']);

			//---Find the link in the mail (translate the ID)
			$pos = strpos($mail, $encrypt . "-");

			//--- (find double or single quote)
			while($pos > 0)
			{

				if(substr($mail, $pos, 1) == "'")
				{
					$quote = "'";
					$posQuoteFirst = $pos;
				}
				if(substr($mail, $pos, 1) == '"')
				{
					$quote = '"';
					$posQuoteFirst = $pos;
				}

				//--- Search the start of the a-element
				if(substr($mail, $pos, 2) == "<a")
				{
					break;
				}

				$pos--;
			}
			//--Find the last quote
			$posQuoteLast = $posQuoteFirst + 1;

			while($posQuoteLast != false && $posQuoteLast <= strlen($mail))
			{
				//--Search the last quote
				if(substr($mail, $posQuoteLast, 1) != $quote)
				{
					$posQuoteLast++;
				}
				else
				{
					//--Stop in the while
					break;
				}
			}

			//--Find the correct encrypted link
			$encryptedLink = substr($mail, $posQuoteFirst + 1, $posQuoteLast - 1 - $posQuoteFirst);

			//--Replace the encrypted link with the correct one (so we don't add another click if we click on the overlay)
			$mail = str_replace($encryptedLink, $link['url'], $mail);

			//--Get correct label
			$strClick = $link['clicked'] == 1 ? BL::getLabel("Click") : BL::getLabel("Clicks");

			//--Calculate percentage
			$percentage = $link['clicked'] > 0 ? round($link['clicked'] / $total, 3) * 100 : 0;

			//---Add a absotule div with the information about the link
			$span = "<span style='background-color:#EBF3F9;border:1px solid #BED7E9;color:#000;font-size:12px;height:auto;margin:15px 0 0 0;padding:5px 10px;position:absolute;width:auto;'>" . $link['clicked'] . " " . $strClick . " (" . $percentage . "%)</span>";

			//--Add span to the mail
			$mail = substr($mail, 0, $pos) . $span . substr($mail, $pos);
		}

		//--Find the open-image url (at the end of the page)
		$posImage = strrpos($mail, "<img");

		//--Replace the open-image-tag
		$mail = substr($mail, 0, $posImage) . "</body></html>";

		return $mail;
	}
}
