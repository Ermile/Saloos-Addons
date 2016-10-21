<?php
namespace lib\db;

/** users account managing **/
class users
{
	/**
	 * this library work with acoount
	 * v1.2
	 */

	public static $user_id;


	/**
	 * insert new recrod in users table
	 * @param array $_args fields data
	 * @return mysql result
	 */
	public static function insert($_args){

		$set = [];
		foreach ($_args as $key => $value) {
			if($value === null)
			{
				$set[] = " `$key` = NULL ";
			}
			else
			{
				$set[] = " `$key` = '$value' ";
			}
		}
		$set = join($set, ',');
		$query =
		"
			INSERT INTO
				users
			SET
				$set
		";
		return \lib\db::query($query);
	}

	/**
	 * check signup and if can add new user
	 * @return [type] [description]
	 */
	public static function signup($_mobile, $_pass, $_perm = null, $_name = null)
	{
		// first if perm is true get default permission from db
		if($_perm === true)
		{
			// if use true fill it with default value
			$_perm     = \lib\utility\option::get('account');
			// default value not set in database
			if($_perm == '')
			{
				$_perm = null;
			}
		}
		else
		{
			$_perm = null;
		}

		$query =
		"
			SELECT
				id
			FROM
				users
			WHERE
				user_mobile = '$_mobile'
			LIMIT 1
		";

		$result = \lib\db::get($query, 'id', true);

		if($result)
		{
			// signup called and the mobile exist
			return false;
		}
		else
		{
			// signup up users
			$args =
			[
				'user_mobile'      => $_mobile,
				'user_pass'        => \lib\utility::hasher($_pass),
				'user_displayname' => $_name,
				'user_permission'  => $_perm,
				'user_createdate'  => date('Y-m-d H:i:s')
			];
			$insert_new = self::insert($args);
			$insert_id = \lib\db::insert_id();
			self::$user_id = $insert_id;
			return $insert_id;
		}
	}


	/**
	 * update mobile number of specefic user
	 * @param  [type] $_user   [description]
	 * @param  [type] $_mobile [description]
	 * @return [type]          [description]
	 */
	public static function updateMobile($_user, $_mobile)
	{
		$qry        = "UPDATE `users` SET `user_mobile` = $_mobile WHERE id = $_user;";
		$result     = \lib\db::query($qry);
		$changeDate = date('Y-m-d H:i:s');

		// save mobile number in user history
		$userDetail =
		[
			'user'   => $_user,
			'cat'    => 'history_'.$_user,
			'key'    => 'mobile',
			'value'  => $_mobile,
			'meta'   => $changeDate,
		];
		$result = \lib\utility\option::set($userDetail);

		return $result;
	}


	public static function updateDetail($_user, $_type, $_value, $_args)
	{
		$changeDate = date('Y-m-d H:i:s');
		// save mobile number in user history
		$userDetail =
		[
			'user'   => $_user,
			'cat'    => 'history_'.$_user,
			'key'    => $_type,
			'value'  => $_value,
			'meta'   => $_args,
		];
		$result = \lib\utility\option::set($userDetail);

		return $result;
	}



	public static function getDetail($_user, $_field = '*', $_cat = null, $_key = null)
	{
		$qry =
			"SELECT $_field FROM `options` WHERE user_id = $_user ";
		if($_cat)
		{
			$qry .= "AND option_cat LIKE '$_cat'";
		}

		if($_key)
		{
			$qry .= "AND option_key LIKE '$_key'";
		}
		if(is_string($_field))
		{
			$result = \lib\db::get($qry, $_field, true);
		}
		else
		{
			$result = \lib\db::get($qry, null, true);
		}
		// var_dump($result);
		// var_dump($qry);
		return $result;
	}


	/**
	 * get users data
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_field    The field
	 *
	 * @return     <type>  The user data.
	 */
	public static function get_user_data($_user_id, $_field = null)
	{
		if($_field == null)
		{
			$_field = "*";
		}
		elseif(is_array($_field))
		{
			$field = [];
			foreach ($_field as $key => $value) {
				$field[] = " users.$value ";
			}
			$_field = join($field, ",");
		}
		elseif(is_string($_field))
		{
			$_field = " users.$_field AS '$_field' ";
		}
		else
		{
			return false;
		}
		$query =
		"
			SELECT
				$_field
			FROM
				users
			WHERE
				users.id = $_user_id
			LIMIT 1
		";
		$result = \lib\db::get($query, null, true);
		return $result;
	}


	/**
	 * Sets the user data.
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_field    The field
	 * @param      <type>  $_value    The value
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function set_user_data($_user_id, $_field, $_value)
	{
		$query =
		"
			UPDATE
				users
			SET
				users.$_field = '$_value'
			WHERE
				users.id = $_user_id
		";
		$result = \lib\db::query($query);
		return $result;
	}

	/**
	 * Gets the displayname.
	 *
	 * @param      <type>  $_user_id  The user identifier
	 *
	 * @return     <type>  The displayname.
	 */
	public static function get_displayname($_user_id)
	{
		if(isset($_SESSION['user']['displayname']))
		{
			return $_SESSION['user']['displayname'];
		}
		$result = self::get_user_data($_user_id, "user_displayname");
		$_SESSION['user']['displayname'] = $result["user_displayname"];
		return $result;
	}


	/**
	 * Sets the displayname.
	 *
	 * @param      <type>   $_user_id      The user identifier
	 * @param      <type>   $_displayname  The displayname
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function set_displayname($_user_id, $_displayname)
	{
		// check new display name vs old display name
		if(isset($_SESSION['user']['displayname']) && $_SESSION['user']['displayname'] == $_displayname )
		{
			return true;
		}
		$result = self::set_user_data($_user_id, "user_displayname", $_displayname);
		if($result)
		{
			$_SESSION['user']['displayname'] = $_displayname;
		}
		return $result;
	}


	/**
	 * Gets the email.
	 *
	 * @param      <type>  $_user_id  The user identifier
	 *
	 * @return     <type>  The email.
	 */
	public static function get_email($_user_id)
	{
		if(isset($_SESSION['user']['email']))
		{
			return $_SESSION['user']['email'];
		}
		$result = self::get_user_data($_user_id, "user_email");
		$_SESSION['user']['email'] = $result["user_email"];
		return isset($result["user_email"]) ? $result["user_email"]: null;
	}


	/**
	 * Sets the email.
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_email    The email
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function set_email($_user_id, $_email)
	{
		if(isset($_SESSION['user']['email']) && $_SESSION['user']['email'] == $_email )
		{
			return true;
		}
		$result = self::set_user_data($_user_id, "user_email", $_email);
		if($result)
		{
			$_SESSION['user']['email'] = $_email;
		}
		return $result;
	}


	/**
	 * Sets the user language.
	 *
	 * @param      <type>  $_language  The language
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function set_language($_language, $_options = [])
	{
		$default_options =
		[
			"update_on_duplicate" => true,
			"user_id"             => self::$user_id
		];

		$_options = array_merge($default_options, $_options);

		// set user id
		if($_options['user_id'] == null)
		{
			return false;
		}

		$arg =
		[
			'user_id'      => $_options['user_id'],
			'option_cat'   => 'user_detail_'. $_options['user_id'],
			'option_key'   => 'language',
			'option_value' => $_language
		];
		$result = \lib\db\options::insert($arg);
		if(!$result && $_options['update_on_duplicate'])
		{
			$result = \lib\db\options::update_on_error($arg);
		}
		return $result;
	}


	/**
	 * Gets the user language.
	 *
	 * @return     <type>  The language.
	 */
	public static function get_language($_user_id = null)
	{
		if($_user_id === null)
		{
			$user_id = self::$user_id;
		}
		else
		{
			$user_id = $_user_id;
		}

		$query =
		"
			SELECT
				option_value AS 'language'
			FROM
				options
			WHERE
				post_id IS NULL AND
				user_id = $user_id AND
				option_cat = 'user_detail_$user_id' AND
				option_key = 'language'
			LIMIT 1
		";
		return \lib\db::get($query, 'language', true);
	}


	/**
	 * try the sarshomar
	 * generate mobile and password and register the Guset Session
	 */
	public static function signup_inspection()
	{
		$displayname = "Guest Session";
		$mobile      = \lib\utility\filter::temp_mobile();
		$password    = \lib\utility\filter::temp_password();
		$user_id     = self::signup($mobile, $password, true, $displayname);
		return $user_id;
	}



	/**
	 * set login session
	 *
	 * @param      <type>  $_user_id  The user identifier
	 */
	public static function set_login_session($_datarow = null, $_fields = null, $_user_id = null)
	{
		if($_user_id)
		{
			$user_data = self::get_user_data($_user_id);
			if(is_array($user_data))
			{
				$_fields = array_keys($user_data);
				$_datarow = $user_data;
			}
			else
			{
				return false;
			}
		}
		$_SESSION['user']       = [];
		$_SESSION['permission'] = [];
		foreach ($_fields as $value)
		{
			if(substr($value, 0, 5) === 'user_')
			{
				$key = substr($value, 5);
				if($key == 'meta')
				{
					$_SESSION['user'][$key] = json_decode($_datarow[$value], true);
				}
				else
				{
					$_SESSION['user'][$key] = $_datarow[$value];
				}
			}
			else
			{
				$_SESSION['user'][$value] = $_datarow[$value];
			}
		}

	}
}
?>