<?php
namespace lib\db;

/** logitems managing **/
class logitems
{
	/**
	 * this library work with logitems table
	 * v1.0
	 */


	/**
	 * insert new recrod in logitems table
	 * @param array $_args fields data
	 * @return mysql result
	 */
	public static function insert($_args)
	{

		$set = [];
		foreach ($_args as $key => $value)
		{
			if($value === null)
			{
				$set[] = " `$key` = NULL ";
			}
			elseif(is_numeric($value))
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
				logitems
			SET
				$set
		";

		return \lib\db::query($query);

	}


	/**
	 * update field from logitems table
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
		$query = "
				UPDATE logitems
				SET $query
				WHERE logitems.id = $_id;
				";

		return \lib\db::query($query);
	}


	/**
	 * get string query and return mysql result
	 * @param string $_query string query
	 * @return mysql result
	 */
	public static function select($_query , $_type = 'query')
	{
		return \lib\db::$_type($_query);
	}


	/**
	 * Gets the logitem id by logitem caller
	 *
	 * @param      <type>  $_caller  The logitem caller
	 *
	 * @return     <type>  The identifier.
	 */
	public static function get_id($_caller)
	{
		$log_item = self::caller($_caller);
		if($log_item && isset($log_item['id']))
		{
			return $log_item['id'];
		}
		elseif(is_numeric($log_item))
		{
			return $log_item;
		}
		return false;
	}


	/**
	 * Gets the logitem record by logitem caller
	 *
	 * @param      <type>  $_caller  The logitem caller
	 *
	 * @return     <type>  The identifier.
	 */
	public static function caller($_caller, $_options = [])
	{
		$default_args =
		[
			'all_field' => false,
		];
		$_options = array_merge($default_args, $_options);

		$field     = 'id';
		$get_field = 'id';

		if($_options['all_field'])
		{
			$field     = '*';
			$get_field = null;
		}

		$query =
		"
			SELECT
				$field
			FROM
				logitems
			WHERE
				logitems.logitem_caller = '$_caller'
			LIMIT 1
		";
		$result = \lib\db::get($query, $get_field, true);
		if(!$result || empty($result))
		{
			return self::auto_insert($_caller);
		}
		return $result;
	}


	/**
	 * auto insert record of logitems
	 *
	 * @param      <type>  $_caller  The caller
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	private static function auto_insert($_caller)
	{
		$insert_log_items =
		[
			'logitem_caller'   => $_caller,
			'logitem_title'    => $_caller,
		];

		$result = self::insert($insert_log_items);
		if($result)
		{
			return (int) \lib\db::insert_id(\lib\db::$link);
		}
		return false;
	}
}
?>