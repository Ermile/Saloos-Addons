<?php
namespace lib\telegram;

/** telegram save library**/
class log extends tg
{
	/**
	 * this library help to save something on telegram
	 * v4.0
	 */


	/**
	 * save log of process into file
	 * @param  [type] $_data [description]
	 * @return [type]        [description]
	 */
	public static function save($_data, $_hook = false)
	{
		// if do not allow to save return null
		if(!self::$saveLog)
		{
			return null;
		}
		$fileAddr = root.'public_html/files/telegram/';
		// if dir doesn't exist, make it
		\lib\utility\file::makeDir($fileAddr, null, true);
		// set file address
		$fileAddr .= 'tg_'. self::$name. '.json';
		file_put_contents($fileAddr, json_encode($_data, JSON_UNESCAPED_UNICODE). "\r\n", FILE_APPEND);
		// add new line for debug
		$debug = "DEBUG: $_hook (". self::response('text') .") ". json_encode($_SESSION, JSON_UNESCAPED_UNICODE). "\r\n";
		file_put_contents($fileAddr, $debug, FILE_APPEND);

		// if not in hook return null
		if($_hook)
		{
			self::saveHistory(self::response('text'));
			return self::saveHook($_data);
		}
		else
		{
			return self::saveResponse($_data);
		}
	}


	/**
	 * save history of messages into session of this user
	 * @param  [type] $_text [description]
	 * @return [type]        [description]
	 */
	private static function saveHistory($_text, $_maxSize = 20)
	{
		if(!isset($_SESSION['tg']['history']))
		{
			$_SESSION['tg']['history'] = [];
		}
		// Prepend text to the beginning of an session array
		array_unshift($_SESSION['tg']['history'], $_text);
		// if count of messages is more than maxSize, remove old one
		if(count($_SESSION['tg']['history']) > $_maxSize)
		{
			// Pop the text off the end of array
			array_pop($_SESSION['tg']['history']);
		}
		// if last commit is repeated
		if(isset($_SESSION['tg']['history'][1]) &&
			$_SESSION['tg']['history'][1] === $_text || empty($_text)
		)
		{
			self::$skipText = true;
			return false;
		}
	}


	/**
	 * save data on hooking
	 * @param  [type] $_data [description]
	 * @return [type]        [description]
	 */
	private static function saveHook($_data)
	{
		// define user detail array
		$from_id = self::response('from');
		// add user_id to save dest of files
		self::$saveDest .= $from_id.'-'. self::response('from', 'username').'/';
		// if we do not have from id return false
		if(!isset($_data['message']['from']) || !$from_id)
		{
			return false;
		}

		// catch user telegram from database and if not exist insert as new user
		self::catchTelegramUser($from_id, $_data['message']['from']);

		// save user detail like contact or location if sended
		if($contact = self::response('contact', null))
		{
			self::saveUserDetail('contact', $contact);
		}
		elseif($location = self::response('location'))
		{
			self::saveUserDetail('location', $location);
		}



		// change language if needede
		if(\lib\router::get_storage('language') !== self::$language)
		{
			\lib\router::set_storage('language', self::$language );
			// use saloos php gettext function
			require_once(lib.'utility/gettext/gettext.inc');
			// gettext setup
			T_setlocale(LC_MESSAGES, \lib\router::get_storage('language'));
			// Set the text domain as 'messages'
			T_bindtextdomain('messages', root.'includes/languages');
			T_bind_textdomain_codeset('messages', 'UTF-8');
			T_textdomain('messages');
		}
		return true;
	}


	/**
	 * save telegram response
	 * @param  [type] $_data [description]
	 * @return [type]        [description]
	 */
	private static function saveResponse($_data)
	{
		// if this result is not okay return false
		if(!isset($_data['ok']))
		{
			return false;
		}
		// if result is not good return false
		if(!isset($_data['result']['total_count']) || !isset($_data['result']['photos']))
		{
			return false;
		}

		// now we are giving photos
		$count  = $_data['result']['total_count'];
		$photos = $_data['result']['photos'];
		$result = [];
		// if has more than one image
		if($count === 0)
		{
			self::createUserDetail($img['file_id']);
		}
		elseif($count > 0)
		{
			// get biggest size of first image(last profile photo)
			$img = end($photos[0]);
			// if file_id is exist
			if(isset($img['file_id']))
			{
				self::createUserDetail($img['file_id']);
			}
		}


		// if dir is not created, create it
		if(!is_dir(self::$saveDest))
		{
			\lib\utility\file::makeDir(self::$saveDest, 0775, true);
		}

		// loop on all photos
		foreach ($photos as $photoKey => $photo)
		{
			$photo = end($photo);
			if(isset($photo['file_id']) && $photo['file_id'])
			{
				$myFile = self::getFile(['file_id' => $photo['file_id']]);
				// save file
				$result[$photoKey] = self::saveFile($myFile, $photoKey, '.jpg');
			}
		}
		return $result;
	}


	/**
	 * save telegram file
	 * @param  [type] $_response [description]
	 * @param  [type] $_prefix   [description]
	 * @param  [type] $_ext      [description]
	 * @return [type]            [description]
	 */
	public static function saveFile($_response, $_prefix = null, $_ext = null)
	{
		if(!isset($_response['ok']) || !isset($_response['result']) || !isset($_response['result']['file_path']))
		{
			return false;
		}
		$file_id   = $_response['result']['file_id'];
		$file_path = $_response['result']['file_path'];
		$dest      = self::$saveDest;
		$exist     = glob($dest.'/*'.$file_id.$_ext);
		// if file exist then don't need to get it from server, return
		if(count($exist))
		{
			return null;
		}
		// add prefix if exits
		if($_prefix)
		{
			$dest .= $_prefix .'-';
		}
		// add file_id
		$dest      .= $file_id;
		if($_ext)
		{
			$dest = $dest. $_ext;
		}
		// save file source
		$source    = "https://api.telegram.org/file/bot";
		$source    .= self::$api_key. "/". $file_path;

		return copy($source, $dest);
	}


	/**
	 * generate user details
	 * @return [type] [description]
	 */
	public static function createUserDetail($_photo = null, $_createArray = true, $_sendMsg = true)
	{
		// create detail of caption
		$user_details = "Your Id: ". self::response('from');
		$user_details .= "\nName: ". self::response('from', 'first_name');
		$user_details .= ' '. self::response('from', 'last_name');
		$user_details .= "\nUsername: @". self::response('from', 'username');
		if($_createArray)
		{
			// create array of message
			if($_photo)
			{
				$user_details =
				[
					'caption' => $user_details,
					'method'  => 'sendPhoto',
					'photo'   => $_photo,
				];
			}
			else
			{
				$user_details =
				[
					'text' => $user_details,
				];
			}
			$user_details['reply_to_message_id'] = self::response('message_id');
			if($_sendMsg)
			{
				$user_details = self::sendResponse($user_details);
			}
		}
		return $user_details;
	}

	/**
	 * save once telegram user details
	 * @param  [type] $_telegram_id [description]
	 * @param  [type] $_fromDetail  [description]
	 * @return [type]               [description]
	 */
	private static function catchTelegramUser($_telegram_id, $_fromDetail = null)
	{
		// if user_id is not set try to give user_id from database
		// search in db to find user_id
		$qry = "SELECT `user_id`
			FROM options
			WHERE
				`option_cat` LIKE 'telegram\_%' AND
				`option_key` LIKE 'user\_%' AND
				`option_value` = $_telegram_id
		";
		$my_user_id = \lib\db::get($qry, 'user_id', true);
		if(is_numeric($my_user_id))
		{
			self::$user_id = $my_user_id;
		}

		// if user does not exist in db, signup it
		if(!self::$user_id)
		{
			// calc full_name of user
			$fullName = trim(self::response('from','first_name'). ' '. self::response('from','last_name'));

			$mobile = '1100'. $_telegram_id;
			\lib\db\users::signup($mobile, 'telegram', true, $fullName);
			self::$user_id = \lib\db\users::$user_id;

			// save telegram user detail like name and username into options
			$userDetail =
			[
				'cat'    => 'telegram_'.self::$user_id,
				'key'    => 'user_'.self::response('from', 'username'),
				'value'  => $_telegram_id,
				'meta'   => $_fromDetail,
			];
			if(isset(self::$user_id))
			{
				$userDetail['user']   = self::$user_id;
				$userDetail['status'] = 'enable';
			}
			else
			{
				$userDetail['status'] = 'disable';
			}
			// save in options table
			\lib\utility\option::set($userDetail, true);
		}

		// save session id database only one time
		// if exist use old one else insert new one to database
		\lib\utility\session::save_once(self::$user_id, 'telegram_'.$_telegram_id);

		if(self::$user_id)
		{
			return true;
		}
		return false;
	}


	private static function saveUserDetail($_type = 'contact', $_args)
	{
		switch ($_type)
		{
			case 'contact':
				$contact         = $_args;
				$from            = self::response('from', null);
				$mobile          = null;
				// set like contact
				$from['user_id'] = $from['id'];
				// remove from values to being like contact array
				unset($from['id']);
				unset($from['username']);
				// if mobile isset, use it
				if(isset($contact['phone_number']))
				{
					$mobile = $contact['phone_number'];
					unset($contact['phone_number']);
				}
				// if user send contact detail save as normal user
				if($mobile)
				{
					// if this is for current user
					if($from == $contact)
					{
						\lib\db\users::updateMobile(self::$user_id, $mobile);
						// if user send contact detail then save all of his/her profile photos
						self::sendResponse(['method' => 'getUserProfilePhotos']);
						self::sendResponse(['text' => T_('Your phone number registered successfully;)')]);

					}
					// else ask real contact detail
					else
					{
						// set fake value for this contact
						self::$hook['message']['contact']['fake'] = true;
						self::sendResponse(['text' => T_('We dont need another users contact:?)')]);
						// do nothing!
					}
				}
				else
				{
					self::$hook['message']['contact']['fake'] = true;
					self::$hook['message']['contact']['phone_number'] = false;
					self::sendResponse(['text' => T_('We need mobile number!')]);
				}
				break;

			case 'location':
				break;

			default:
				break;
		}

		\lib\db\users::updateDetail(self::$user_id, $_type, 'telegram', $_args);
	}
}
?>