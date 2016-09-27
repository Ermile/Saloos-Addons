<?php
namespace lib\db;

/** comments managing **/
class comments
{
	/**
	 * this library work with comments
	 * v1.0
	 */


	/**
	 * insert new recrod in comments table
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
			INSERT INTO comments
				($fields) VALUES ('$values')
			";

		return \lib\db::query($query);

	}


	/**
	 * update field from comments table
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
				UPDATE comments
				SET $query
				WHERE comments.id = $_id;
				";

		return \lib\db::query($query);
	}


	/**
	 * we can not delete a record from database
	 * we just update field status to 'deleted' or 'disable' or set this record to black list
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function delete($_id) {
		// get id
		$query = "
				UPDATE FROM comments
				SET comments.comment_status = 'deleted'
				WHERE comments.id = $_id
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




	public static function save($_content, $_args = null)
	{
		$values =
		[
			"post_id"            => null,
			"comment_author"     => null,
			"comment_email"      => null,
			"comment_url"        => null,
			// "comment_content" => null,
			"comment_meta"       => null,
			"comment_status"     => null,
			"comment_parent"     => null,
			"user_id"            => null,
			"visitor_id"         => null,
		];

		if(!$_args)
		{
			$_args = [];
		}
		// foreach args if isset use it
		foreach ($_args as $key => $value)
		{
			$value = "'". $value. "'";
			// check in normal condition exist
			if(array_key_exists($key, $values))
			{
				$values[$key] = $value;
			}
			// check for id
			$newKey = $key.'_id';
			if(array_key_exists($newKey, $values))
			{
				$values[$newKey] = $value;
			}
			// check for table prefix
			$newKey = 'comment_'. $key;
			if(array_key_exists($newKey, $values))
			{
				$values[$newKey] = $value;
			}
		}
		foreach ($values as $key => $value)
		{
			if(!$value)
			{
				unset($values[$key]);
			}
		}

		// set not null fields
		// set comment content
		$values['comment_content'] = "'". htmlspecialchars($_content). "'";
		// set comment status if not set
		if(!isset($values['comment_status']))
		{
			$values['comment_status'] = "'unapproved'";
		}
		// set time of comment
		if(isset($values['comment_meta']) && is_array($values['comment_meta']))
		{
			$values['comment_meta']['time'] = date('Y-m-d H:i:s');
		}
		else
		{
			$values['comment_meta'] = ['time' => date('Y-m-d H:i:s')];
		}
		$values['comment_meta'] = "'".json_encode($values['comment_meta'], JSON_UNESCAPED_UNICODE)."'";
		// generate query text
		$list_field  = array_keys($values);
		$list_field  = implode($list_field, ', ');
		$list_values = implode($values, ', ');
		// create query string
		$qry       = "INSERT INTO comments ( $list_field ) VALUES ( $list_values )";
		var_dump($qry);
		// run query and insert into db
		$result    = \lib\db::query($qry);
		// get insert id
		$commentId = \lib\db::insert_id();
		// return last insert id
		return $commentId;
	}


	public static function get_post_comment($_post_id)
	{
		$query =
		"
			SELECT
				*
			FROM
				comments
			WHERE
				comments.post_id = $_post_id
			LIMIT 10;
		";
		return self::select($query,"get");
	}
}
?>