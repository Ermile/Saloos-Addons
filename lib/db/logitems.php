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

		// creat field list string
		$fields = join(array_keys($_args), ",");

		// creat value list string
		$values = join(array_values($_args), "','");

		// make insert query
		$query = "
			INSERT INTO logitems
				($fields) VALUES ('$values')
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
	public function update($_args, $_id) {

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
	public function select($_query) {
		return \lib\db::query($_query);
	}

}
?>