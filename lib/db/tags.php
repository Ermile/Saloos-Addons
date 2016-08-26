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
	public static function insert($_args){
		// jost tag can insert
		$_args['term_type']	= 'tag';

		return terms::insert($_args);
	}


	/**
	 * update field from terms table
	 * get fields and value to update
	 * @param array $_args fields data
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public function update($_args, $_id) {

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
	public function delete($_id) {
		return terms::delete($_id);
	}


	/**
	 * get string query and return mysql result
	 * @param string $_query string query
	 * @return mysql result
	 */
	public function select($_query, $_type = 'query') {
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