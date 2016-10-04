<?php
namespace lib\db;

/** terms managing **/
class terms
{
	/**
	 * this library work with terms
	 * v1.0
	 */


	/**
	 * insert new tag in terms table
	 * @param array $_args fields data
	 * @return mysql result
	 */
	public static function insert($_args)
	{

		if(empty($_args))
		{
			return null;
		}
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
			INSERT IGNORE INTO
				terms
			SET
				$set
		";
		return \lib\db::query($query);
	}


	/**
	 * insert multi value to terms
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function insert_multi($_args)
	{
		// marge all input array to creat list of field to be insert
		$fields = [];
		foreach ($_args as $key => $value) {
			$fields = array_merge($fields, $value);
		}

		// creat multi insert query : INSERT INTO TABLE (FIELDS) VLUES (values), (values), ...
		$values = [];
		$together = [];
		foreach ($_args	 as $key => $value) {
			foreach ($fields as $field_name => $vain) {
				if(array_key_exists($field_name, $value)){
					$values[] = "'" . trim($value[$field_name]) . "'";
				}else{
					$values[] = "NULL";
				}
			}
			$together[] = join($values, ",");
			$values = [];
		}

		if(empty($fields))
		{
			return null;
		}

		$fields = join(array_keys($fields), ",");

		$values = join($together, "),(");

		// crate string query
		$query = "
				INSERT IGNORE INTO terms
				($fields)
				VALUES
				($values)
				";

		return \lib\db::query($query);
	}


	/**
	 * update field from terms table
	 * get fields and value to update
	 * @example update table set field = 'value' , field = 'value' , .....
	 * @param array $_args fields data
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function update($_args, $_id)
	{

		$query = [];
		foreach ($_args as $field => $value)
		{
			$query[] = "$field = '$value'";
		}
		$query = join($query, ",");

		// make update query
		$query = "
				UPDATE terms
				SET $query
				WHERE terms.id = $_id;
				";

		return \lib\db::query($query);

	}


	/**
	 * we can not delete a record from database
	 * we just update field status to 'deleted' or set this record to black list
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function block($_id)
	{

		// get id
		$query = "
				UPDATE FROM terms
				SET terms.term_status = 'block'
				WHERE terms.id = $_id
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
	 * get terms list used in specefic table
	 * @author Javad Evazzade
	 * @param  [type] $_usageid [description]
	 * @param  string $_return  [description]
	 * @param  string $_foreign [description]
	 * @param  string $_type    [description]
	 * @return [type]           [description]
	 */
	public static function usage($_usageid, $_return = 'term_title', $_foreign = 'posts', $_type = 'tag')
	{
		if($_return === null)
		{
			$_return = 'term_title';
		}
		if($_foreign === null)
		{
			$_foreign = 'posts';
		}
		if($_type === null)
		{
			$_type = 'tag';
		}
		$qry ="SELECT * FROM terms
		INNER JOIN termusages
			ON termusages.term_id = terms.id
			WHERE
				termusages.termusage_foreign = '$_foreign' AND
				termusages.termusage_id = $_usageid AND
				terms.term_type = '$_type'
		";
		// run query
		if($_return && $_return !== true)
		{
			$result = \lib\db::get($qry, $_return);
		}
		else
		{
			$result = \lib\db::get($qry);
		}
		// if user want count of result return count of it
		if($_return === true)
		{
			$result = count($result);
		}
		// return final result
		return $result;
	}
}
?>