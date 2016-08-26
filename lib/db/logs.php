<?php
namespace lib\db;

/** logs managing **/
class logs
{
	/**
	 * this library work with logs table
	 * v1.0
	 */

	// database field 
	public $fields = [
					'logitem_id'     => true, // this field can not be null
					'user_id'        => null,
					'log_data'       => null,
					'log_meta'       => null,
					'log_status'     => ['enable','disable','expire','deliver'],
					'log_createdate' => null,
					];

	/**
	 * insert new recrod in logs table
	 * @param array $_args fields data
	 * @return mysql result
	 */
	public static function insert($_args){
		
		// check match fields and database field
		$field_value = config::check($_args, $this->fields);

		// creat field list string
		$fields = join(array_keys($field_value), ",");

		// creat value list string
		$values = join(array_values($field_value), "','");

		// make insert query
		$query = "
			INSERT INTO logs
				($fields) VALUES ('$values')
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
	public function update($_args, $_id) {
			
		// config fields and value
		$field_value = config::check($_args, $this->fields);

		// ready fields and values to update syntax query [update table set field = 'value' , field = 'value' , .....]
		$query = [];
		foreach ($field_value as $field => $value) {
			$query[] = "$field = '$value'";
		}
		$query = join($query, ",");

		// make update query
		$query = "
				UPDATE logs
				SET $query
				WHERE logs.id = $_id;
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
	public function select($_query) {
		return \lib\db::query($_query);
	}

}
?>