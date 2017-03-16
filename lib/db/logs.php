<?php
namespace lib\db;

/** logs managing **/
class logs
{
	/**
	 * this library work with logs table
	 * v1.0
	 */


	/**
	 * insert new recrod in logs table
	 * @param array $_args fields data
	 * @return mysql result
	 */
	public static function insert($_args)
	{

		$set = [];
		foreach ($_args as $key => $value)
		{
			if($value === null || is_null($value) || $value === '' || $value == '')
			{
				$set[] = " `$key` = NULL ";
			}
			elseif(is_int($value))
			{
				$set[] = " `$key` = $value ";
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
				logs
			SET
				$set
		";
		return \lib\db::query($query);
	}


	/**
	 * update field from logs table
	 * get fields and value to update
	 * @param array $_args fields data
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function update($_args, $_id)
	{

		// ready fields and values to update syntax query [update table set field = 'value' , field = 'value' , .....]
		$query = [];
		foreach ($_args as $field => $value)
		{
			$query[] = "$field = '$value'";
		}
		$query = join($query, ",");

		// make update query
		$query = " UPDATE logs SET $query WHERE logs.id = $_id ";
		return \lib\db::query($query);
	}


	/**
	 * we can not delete a record from database
	 * we just update field status to 'deleted' or 'disable' or set this record to black list
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function delete($_id)
	{
		// get id
		$query = "
				UPDATE FROM logs
				SET logs.notification_status = 'expire'
				WHERE logs.id = $_id
				";

		return \lib\db::query($query);
	}


	/**
	 * get string query and return mysql result
	 * @param string $_query string query
	 * @return mysql result
	 */
	public static function select($_query, $_type = 'query')
	{
		return \lib\db::$_type($_query);
	}


	/**
	 * { function_description }
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_caller   The caller
	 * @param      array   $_options  The options
	 */
	public static function set($_caller, $_user_id = null, $_options = [])
	{
		$log_item_id = \lib\db\logitems::caller($_caller);

		if(!$log_item_id)
		{
			return false;
		}

		$default_options =
		[
			'data'   => null,
			'meta'   => null,
			'time'   => date("Y-m-d H:i:s"),
			'status' => 'enable',
		];

		$_options = array_merge($default_options, $_options);

		$_options['meta'] = \lib\utility\safe::safe($_options['meta']);

		if(is_array($_options['meta']) || is_object($_options['meta']))
		{
			$_options['meta'] = json_encode($_options['meta'], JSON_UNESCAPED_UNICODE);
		}

		$insert_log =
		[
			'logitem_id'     => $log_item_id,
			'user_id'        => $_user_id,
			'log_data'       => $_options['data'],
			'log_status'     => $_options['status'],
			'log_meta'       => $_options['meta'],
			'log_createdate' => $_options['time'],
		];

		return self::insert($insert_log);
	}


	/**
	 * get log
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function get($_args)
	{
		$only_one_recort = false;

		if(empty($_args) || !is_array($_args))
		{
			return false;
		}

		if(isset($_args['limit']))
		{
			if($_args['limit'] == 1)
			{
				$only_one_recort = true;
			}

			$limit = "LIMIT ". $_args['limit'];
			unset($_args['limit']);
		}
		else
		{
			$limit = null;
		}

		$where = [];
		foreach ($_args as $key => $value)
		{
			if(preg_match("/\%/", $value))
			{
				$where[] = " logs.$key LIKE '$value'";
			}
			elseif($value === null)
			{
				$where[] = " logs.$key IS NULL";
			}
			elseif(is_numeric($value))
			{
				$where[] = " logs.$key = $value ";
			}
			elseif(is_string($value))
			{
				$where[] = " logs.$key = '$value'";
			}
		}
		$where = "WHERE ". join($where, " AND ");

		$query = " SELECT * FROM logs $where $limit ";

		$result = \lib\db::get($query, null, $only_one_recort);
		if(isset($result['meta']) && substr($result['meta'], 0, 1) == '{')
		{
			$result['meta'] = json_decode($result['meta'], true);
		}
		return $result;
	}
}
?>