<?php
namespace lib\db;

/** terms managing **/
class tags
{
	/**
	 * this library work with terms
	 * v1.0
	 */


	/**
	 * insert new tag in terms table
	 * tag is a record of terms
	 * so we set the term_type and send $_arg to terms::insert funciton
	 * @param array $_args fields data
	 * @return mysql result
	 */
	public static function insert($_args)
	{
		// jost tag can insert
		$_args['term_type']	= 'tag';

		return terms::insert($_args);
	}


	/**
	 * insert mulit tag
	 *
	 * @param      <type>  $_tags  strign of tags
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function insert_multi($_tags)
	{
		//split tags
		$tags = preg_split("/\,/", $_tags);
		// remove empty tags
		$tags = array_filter($tags);

		if(empty($tags))
		{
			return null;
		}

		$result = [];
		foreach ($tags as $key => $value) {
			$result[] =
			[
				'term_type'  => 'tag',
				'term_title' => $value,
				'term_url'   => $value,
				'term_slug'  => \lib\utility\filter::slug($value)
			];
		}
		return \lib\db\terms::insert_multi($result);
	}


	/**
	 * update field from terms table
	 * get fields and value to update
	 * @param array $_args fields data
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function update($_args, $_id)
	{

		// jost tag can insert
		$_args['term_type']	= 'tag';

		return terms::update($_args, $_id);
	}


	/**
	 * we can not delete a record from database
	 * we just update field status to 'deleted' or set this record to black list
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function delete($_id)
	{
		return terms::delete($_id);
	}


	/**
	 * get id
	 *
	 * @param      <type>  $_tags  The tags
	 */
	public static function get_multi_id($_tags)
	{
		//split tags
		$tags = preg_split("/\,/", $_tags);
		// remove empty tags
		$tags = array_filter($tags);

		if(empty($tags))
		{
			return null;
		}

		$condition = [];
		foreach ($tags as $key => $value)
		{
			$condition[] = " term_title = '" . $value . "' ";
		}

		$condition = join($condition, " OR ");

		$query = "
			SELECT
				id
			FROM
				terms
			WHERE
				term_type = 'tag' AND
				($condition)
			";

		$result = \lib\db::get($query, "id");
		return $result;
	}


	/**
	 * get string query and return mysql result
	 * @param string $_query string query
	 * @return mysql result
	 */
	public static function select($_query, $_type = 'query')
	{
		return terms::select($_query, $_type);
	}


	/**
	 * get tags list used in specefic post in specefic table
	 * @author Javad Evazzadeh
	 * @param  [type]  $_post_id   [description]
	 * @param  [type]  $_return    [description]
	 * @param  [type]  $_foreign   [description]
	 * @param  boolean $_seperator [description]
	 * @return [type]              [description]
	 */
	public static function usage($_post_id, $_return = null, $_foreign = null, $_seperator = false)
	{
		$result = terms::usage($_post_id, $_return, $_foreign, 'tag');
		if($_seperator)
		{
			if(is_array($result) && $result)
			{
				if($_seperator === true)
				{
					$_seperator = ', ';
				}
				$result = implode($result, $_seperator);
			}
			else
			{
				$result = "";
			}
		}
		return $result;
	}
}
?>