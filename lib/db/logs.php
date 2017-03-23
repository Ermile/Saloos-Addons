<?php
namespace lib\db;

/** logs managing **/
class logs
{
	/**
	 * this library work with logs table
	 * v1.0
	 */

	public static $fields =
	"
			logs.id								AS 	`id`,
			logs.logitem_id 					AS 	`logitem_id`,
			logitems.logitem_type				AS 	`logitem_type`,
			logitems.logitem_caller				AS 	`logitem_caller`,
			logitems.logitem_title				AS 	`logitem_title`,
			logitems.logitem_desc				AS 	`logitem_desc`,
			logitems.logitem_meta				AS 	`logitem_meta`,
			IFNULL(logitems.count, 0) 			AS 	`count`,
			logitems.logitem_priority 			AS 	`priority`,
			logitems.date_modified 				AS 	`date_modified`,
			logs.user_id						AS 	`user_id`,
			logs.log_data						AS 	`data`,
			logs.log_meta						AS 	`meta`,
			logs.log_status						AS 	`status`,
			logs.log_createdate					AS 	`createdate`,
			logs.date_modified					AS 	`date_modified`,
			users.user_displayname				AS  `displayname`,
			users.user_mobile					AS  `mobile`,
			users.user_port						AS  `port`,
			users.user_verify					AS  `verify`,
			users.user_trust					AS  `trust`
		FROM
			logs
		LEFT JOIN logitems ON logitems.id = logs.logitem_id
		LEFT JOIN users ON logs.user_id = users.id
	";

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
			elseif(is_numeric($value))
			{
				$set[] = " `$key` = $value ";
			}
			elseif(is_string($value))
			{
				$set[] = " `$key` = '$value' ";
			}
			elseif(is_array($value) || is_object($value))
			{
				$set[] = " `$key` = '". json_encode($value, JSON_UNESCAPED_UNICODE). "' ";
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
		if(isset($result['log_meta']) && substr($result['log_meta'], 0, 1) == '{')
		{
			$result['log_meta'] = json_decode($result['log_meta'], true);
		}
		else
		{
			$result = \lib\utility\filter::meta_decode($result);
		}
		return $result;
	}



	/**
	 * Searches for the first match.
	 *
	 * @param      <type>  $_string   The string
	 * @param      array   $_options  The options
	 */
	public static function search($_string = null, $_options = [])
	{
		$where = []; // conditions

		if(!$_string && empty($_options))
		{
			// default return of this function 10 last record of poll
			$_options['get_last'] = true;
		}

		$default_options =
		[
			// just return the count record
			"get_count"   => false,
			// enable|disable paignation,
			"pagenation"  => true,
			// for example in get_count mode we needless to limit and pagenation
			// default limit of record is 15
			// set the limit  = null and pagenation = false to get all record whitout limit
			"limit"           => 15,
			// for manual pagenation set the statrt_limit and end limit
			"start_limit"     => 0,
			// for manual pagenation set the statrt_limit and end limit
			"end_limit"       => 10,
			// the the last record inserted to post table
			"get_last"        => false,
			// default order by ASC you can change to DESC
			"order"           => "ASC",
			// custom sort by field
			"sort"			  => null,
		];
		$_options = array_merge($default_options, $_options);

		$pagenation = false;
		if($_options['pagenation'])
		{
			// page nation
			$pagenation = true;
		}

		// ------------------ get count
		$only_one_value = false;
		$get_count      = false;

		if($_options['get_count'] === true)
		{
			$get_count      = true;
			$public_fields  = " COUNT(logs.id) AS 'logcount' FROM logs ";
			$limit          = null;
			$only_one_value = true;
		}
		else
		{
			$limit         = null;
			$public_fields = self::$fields;
			if($_options['limit'])
			{
				$limit = $_options['limit'];
			}
		}

		// ------------------ get last
		$order = null;
		if($_options['get_last'])
		{
			$order = " ORDER BY logs.id DESC ";
		}
		else
		{
			$order = " ORDER BY logs.id $_options[order] ";
		}

		$start_limit = $_options['start_limit'];
		$end_limit   = $_options['end_limit'];


		unset($_options['pagenation']);
		unset($_options['get_count']);
		unset($_options['limit']);
		unset($_options['start_limit']);
		unset($_options['end_limit']);
		unset($_options['get_last']);
		unset($_options['order']);
		unset($_options['sort']);

		foreach ($_options as $key => $value)
		{
			if(is_array($value))
			{
				if(isset($value[0]) && isset($value[1]) && is_string($value[0]) && is_string($value[1]))
				{
					// for similar "logs.`field` LIKE '%valud%'"
					$where[] = " logs.`$key` $value[0] $value[1] ";
				}
			}
			elseif($value === null)
			{
				$where[] = " logs.`$key` IS NULL ";
			}
			elseif(is_numeric($value))
			{
				$where[] = " logs.`$key` = $value ";
			}
			elseif(is_string($value))
			{
				$where[] = " logs.`$key` = '$value' ";
			}
		}

		$where = join($where, " AND ");
		$search = null;
		if($_string != null)
		{
			$search =
			"(
				logitems.logitem_type 		LIKE '%$_string%' OR
				logitems.logitem_caller 	LIKE '%$_string%' OR
				logitems.logitem_title 		LIKE '%$_string%' OR
				logs.log_data 				LIKE '%$_string%' OR
				logs.log_meta 				LIKE '%$_string%'
			)";
			if($where)
			{
				$search = " AND ". $search;
			}
		}

		if($where)
		{
			$where = "WHERE $where";
		}
		elseif($search)
		{
			$where = "WHERE";
		}

		if($pagenation && !$get_count)
		{
			$pagenation_query = "SELECT	$public_fields $where $search ";
			list($limit_start, $limit) = \lib\db::pagnation($pagenation_query, $limit);
			$limit = " LIMIT $limit_start, $limit ";
		}
		else
		{
			// in get count mode the $limit is null
			if($limit)
			{
				$limit = " LIMIT $start_limit, $end_limit ";
			}
		}

		$json = json_encode(func_get_args());
		$query =
		"
			SELECT SQL_CALC_FOUND_ROWS
				$public_fields
				$where
				$search
			$order
			$limit
			-- logs::search()
			-- $json
		";

		if(!$only_one_value)
		{
			$result = \lib\db::get($query);
			$result = \lib\utility\filter::meta_decode($result);
		}
		else
		{
			$result = \lib\db::get($query, 'logcount', true);
		}

		return $result;
	}

	public static function end_log($_condition = [])
	{
		$where = [];
		foreach ($_condition as $key => $value) {
			if(is_string($value))
			{
				$value = "'$value'";
			}
			$where[] = "$key = $value";
		}
		if(!empty($where))
		{
			$where = "WHERE " . join(" AND " , $where);
		}
		else
		{
			$where = "";
		}
		$query = "SELECT logitems.*, logs.* FROM logs
		INNER JOIN logitems ON logitems.id = logs.logitem_id
		$where
		ORDER BY logs.log_createdate DESC LIMIT 0,1";
		return \lib\db::get($query, null, true);
	}
}
?>