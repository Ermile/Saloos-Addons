<?php
namespace lib\db;

/** options managing **/
class options
{
	/**
	 * this library work with options table
	 * v1.0
	 */


	/**
	 * insert new recrod in options table
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
			INSERT INTO options
				($fields) VALUES ('$values')
			";

		return \lib\db::query($query);

	}


	/**
	 * update field from options table
	 * get fields and value to update
	 * @param array $_args fields data
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public function update($_args, $_id) {

		// ready fields and values to update syntax query [update table set field = 'value' , field = 'value' , .....]
		$query = [];
		foreach ($_args as $field => $value) {
			$query[] = "$field = '$value'";
		}
		$query = join($query, ",");

		// make update query
		$query = "
				UPDATE options
				SET $query
				WHERE options.id = $_id;
				";

		return \lib\db::query($query);
	}


	/**
	 * we can not delete a record from database
	 * we just update field status to 'deleted' or 'disable' or set this record to black list
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public function delete($_id) {
		// get id
		$query = "
				UPDATE FROM options
				SET options.option_status = 'disable'
				WHERE options.id = $_id
				";

		return \lib\db::query($query);
	}


	/**
	 * get string query and return mysql result
	 * @param string $_query string query
	 * @return mysql result
	 */
	public function select($_query, $_type = 'query') {
		return \lib\db::$_type($_query);
	}

}
?>