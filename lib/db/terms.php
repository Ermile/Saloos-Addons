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
	public static function insert($_args){

		// creat field list string
		$fields = join(array_keys($_args), ",");

		// creat value list string
		$values = join(array_values($_args), "','");

		// make insert query
		$query = "
			INSERT IGNORE INTO terms
				($fields) VALUES ('$values')
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
	public static function update($_args, $_id) {

		$query = [];
		foreach ($_args as $field => $value) {
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
	public static function block($_id) {

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
	public static function select($_query, $_type = 'query') {
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