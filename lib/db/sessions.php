<?php
namespace lib\db;

/** sessions managing **/
class sessions
{
	/**
	 * this library work with sessions table
	 * v1.0
	 */


	/**
	 * generate code
	 *
	 * @param      string  $_user_id  The user identifier
	 *
	 * @return     string  ( description_of_the_return_value )
	 */
	private static function generate_code($_user_id)
	{
		$code =  'SALOOS'. $_user_id. '_;)_'. time(). '(^_^)' . rand(1000, 9999);
		$code = \lib\utility::hasher($code, false);
		$code = \lib\utility\safe::safe($code);
		return $code;
	}


	/**
	 * insert sessions on database
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	private static function insert($_args)
	{
		$set = \lib\db\config::make_set($_args);
		if(!trim($set))
		{
			return false;
		}

		return \lib\db::query("INSERT INTO sessions SET $set");
	}


	/**
	 * get record is exist or no
	 *
	 * @param      <type>  $_args  The arguments
	 */
	private static function get($_args)
	{
		$where = \lib\db\config::make_where($_args);
		if(!trim($where))
		{
			return false;
		}

		$get   = \lib\db::get("SELECT * FROM sessions WHERE $where LIMIT 1", null, true);
		return $get;
	}


	/**
	 * check_code session code
	 *
	 * @param      <type>  $_code  The code
	 */
	private static function check_code($_code)
	{
		$get = self::get(['code' => $_code]);
		if(empty($get))
		{
			return false;
		}
		else
		{
			if(isset($get['status']))
			{
				switch ($get['status'])
				{
					case 'active':
						return true;
						break;

					default:
						return false;
						break;
				}
			}
		}
		return false;
	}


	/**
	 * Gets the cookie.
	 *
	 * @return     <type>  The cookie.
	 */
	public static function get_cookie()
	{
		return \lib\utility::cookie('remember_me');
	}


	/**
	 * Gets the user identifier.
	 *
	 * @return     <type>  The user identifier.
	 */
	public static function get_user_id()
	{
		$code = self::get_cookie();
		$get  = self::get(['code' => $code, 'status' => 'active']);

		if(isset($get['user_id']))
		{
			self::login($code);
			return (int) $get['user_id'];
		}
		return false;
	}


	/**
	 * Terminate the cookie.
	 *
	 * @param      <type>  $_code  The code
	 */
	private static function terminate_cookie()
	{
		unset($_COOKIE['remember_me']);
		setcookie("remember_me", null, -1, '/');
	}


	/**
	 * Sets the cookie.
	 *
	 * @param      <type>  $_code  The code
	 */
	private static function set_cookie($_code)
	{
		if(defined('service_name'))
		{
			$service_name = '.'. service_name;
		}
		else
		{
			$service_name = '.' . \lib\router::get_domain(count(\lib\router::get_domain(-1))-2);
			$tld = \lib\router::get_domain(-1);
			$service_name .= '.' . end($tld);
		}

		setcookie("remember_me", $_code, time() + (60*60*24*365), '/', $service_name);
	}

	/**
	 * inset new session in database
	 *
	 * @param      <type>  $_session  The session
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function set($_user_id)
	{
		$args =
		[
			'ip'       => ClientIP,
			'agent_id' => \lib\utility\visitor::get('agent'),
			'user_id'  => $_user_id,
			'status'   => 'active'
		];

		$exist = self::get($args);

		$args['code']      = self::generate_code($_user_id);
		$args['last_seen'] = date("Y-m-d H:i:s");

		if(!$exist)
		{
			self::insert($args);
			self::set_cookie($args['code']);
			return true;
		}
		else
		{
			if(isset($exist['status']) && $exist['status'] === 'active')
			{
				if(isset($exist['code']))
				{
					self::login($exist['code']);
					self::set_cookie($exist['code']);
				}
				return true;
			}
			else
			{
				self::insert($args);
				self::set_cookie($args['code']);
				return true;
			}
		}
	}


	/**
	 * get the session details
	 *
	 * @param      <type>  $_session  The session
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get_list($_user_id, $_raw = false)
	{
		if(!$_user_id || !is_numeric($_user_id))
		{
			return false;
		}

		if($_raw)
		{
			$query = "SELECT * FROM  sessions WHERE `user_id` = '$_user_id' ";
		}
		else
		{
			$query =
			"
				SELECT
					status,
					ip,
					last_seen,
					agent_id
				FROM
					sessions
				WHERE
					user_id = $_user_id
			";
		}

		$result = \lib\db::get($query, null, true);
		return $result;
	}


	/**
	 * the user logied by code
	 *
	 * @param      <type>  $_user_id  The user identifier
	 */
	public static function login($_code)
	{
		if($_code && is_string($_code))
		{
			\lib\db::query("UPDATE sessions SET sessions.count = sessions.count + 1 WHERE code = '$_code'");
		}
	}


	/**
	 * change status
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_status   The status
	 */
	private static function change_status($_user_id, $_status, $_change_all_code = false)
	{
		if(!$_user_id || !is_numeric($_user_id) || !$_status || !is_string($_status))
		{
			return false;
		}

		$where_code = null;

		if(!$_change_all_code)
		{
			$code = self::get_cookie();
			if($code)
			{
				$where_code = " AND code = '$code' ";
			}
		}

		\lib\db::query("UPDATE sessions SET status = '$_status' WHERE user_id = $_user_id $where_code");

	}


	/**
	 * set status of code on logout
	 *
	 * @param      <type>  $_user_id  The user identifier
	 */
	public static function logout($_user_id)
	{
		self::change_status($_user_id, 'logout');
		self::terminate_cookie();
	}


	/**
	 * set status of code on changepass
	 *
	 * @param      <type>   $_user_id  The user identifier
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function change_password($_user_id)
	{
		self::change_status($_user_id, 'changed', true);
	}

}
?>