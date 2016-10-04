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
				posts
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
	public static function update($_args, $_id) {

		// ready fields and values to update syntax query [update table set field = 'value' , field = 'value' , .....]
		$query = [];
		foreach ($_args as $field => $value) {
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
	public static function select($_query , $_type = 'query') {
		return \lib\db::$_type($_query);
	}

}
?>