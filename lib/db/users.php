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
		}
		// if permission is not set then set it null
		if(!$_perm)
		{
			$_perm = 'NULL';
		}
		if(!$_name)
		{
			$_name = 'NULL';
		}

		$qry = "SELECT * FROM `users` WHERE `user_mobile` = $_mobile";
		// connect to project database
		\lib\db::connect();
		$result     = @mysqli_query(\lib\db::$link, $qry);
		$user_exist = @mysqli_affected_rows(\lib\db::$link);

		if($user_exist !== 0)
		{
			if(!is_a($result, 'mysqli_result') || $user_exist !== 1)
			{
				// no record exist
				return false;
			}

			// if has result return id
			if($result && $row = @mysqli_fetch_assoc($result))
			{
				if(isset($row['id']))
				{
					self::$user_id = $row['id'];
					return $row['id'];
				}
			}
			// mobile number exist in database
			return false;
		}

		$qry = "INSERT INTO `users`
		(
			`user_mobile`,
			`user_pass`,
			`user_displayname`,
			`user_permission`,
			`user_createdate`
		)
		VALUES
		(
			$_mobile,
			'$_pass',
			'$_name',
			$_perm,
			'".date('Y-m-d H:i:s')."'
		)";

		// execute query
		$result     = @mysqli_query(\lib\db::$link, $qry);
		$user_exist = @mysqli_affected_rows(\lib\db::$link);

		// give last insert id
		$last_id    = @mysqli_insert_id(\lib\db::$link);
		// if have last insert it return it
		if($last_id)
		{
			self::$user_id = $last_id;
			return $last_id;
		}
		return null;
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
	 * Gets the displayname.
	 *
	 * @param      <type>  $_user_id  The user identifier
	 *
	 * @return     <type>  The displayname.
	 */
	public static function get_displayname($_user_id)
	{
		$query =
		"
			SELECT
				users.user_displayname AS 'displayname'
			FROM
				users
			WHERE
				users.id = $_user_id
			LIMIT 1
		";

		return \lib\db::get($query, 'displayname', true);
	}


	public static function set_displayname($_user_id, $_displayname)
	{
		$query =
		"
			UPDATE
				users
			SET
				users.user_displayname = '$_displayname'
			WHERE
				users.id = $_user_id
		";

		$result = \lib\db::query($query);
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
			'user_id'      => $user_id,
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
}
?>