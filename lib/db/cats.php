<?php
namespace lib\db;

/** terms managing **/
class cats
{
	/**
	 * this library work with terms
	 * v1.0
	 */


	/**
	 * check cats syntax [cat, cat_(.*)]
	 * @param string $_cats
	 * @return string
	 */
	private static function check($_cats = null){

		if($_cats === null) {
			$return = 'cat';
		}

		if(preg_match("/^cat_(.*)/", $_cats)){
			$return = $_cats;
		}else{
			$return = 'cat_' . $_cats;
		}
		return $return;
	}


	/**
	 * insert new cat in terms table
	 * cat is a record of terms
	 * so we set the term_type and send $_arg to terms::insert funciton
	 * @param array $_args fields data
	 * @return mysql result
	 */
	public static function insert($_args){
		// jost cat can insert
		$_args['term_type']	= self::check($_args['term_type']);

		return terms::insert($_args);
	}


	/**
	 * update field from terms table
	 * get fields and value to update
	 * @param array $_args fields data
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function update($_args, $_id) {

		// jost cat can insert
		$_args['term_type']	= self::check($_args['term_type']);

		return terms::update($_args, $_id);
	}


	/**
	 * we can not delete a record from database
	 * we just update field status to 'deleted' or set this record to black list
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function delete($_id) {
		return terms::delete($_id);
	}


	/**
	 * get string query and return mysql result
	 * @param string $_query string query
	 * @return mysql result
	 */
	public static function select($_query, $_type = 'query') {
		return terms::select($query, $_type);
	}


	/**
	 * get cats list used in specefic post in specefic table
	 * @author Javad Evazzadeh
	 * @param  [type]  $_post_id   [description]
	 * @param  [type]  $_return    [description]
	 * @param  [type]  $_foreign   [description]
	 * @param  boolean $_seperator [description]
	 * @return [type]              [description]
	 */
	public static function usage($_post_id, $_return = null, $_foreign = null, $_seperator = false)
	{
		$result = terms::usage($_post_id, $_return, $_foreign, 'cat');
		if($_seperator && $result)
		{
			if($_seperator === true)
			{
				$_seperator = ', ';
			}
			$result = implode($result, $_seperator);
		}
		return $result;
	}


	/**
	 * Gets the multi record of cats
	 *
	 * @param      <type>  $_term_type  The term type
	 *
	 * @return     <type>  The multi.
	 */
	public static function get_multi($_term_type)
	{
		return \lib\db\terms::get_multi(['term_type' => $_term_type]);
	}
}
?>