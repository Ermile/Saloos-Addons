<?php
namespace lib\db;

/** work with posts **/
class posts
{
	/**
	 * this library work with posts
	 * v1.0
	 */


	/**
	 * get list of polls
	 * @param  [type] $_user_id set userid
	 * @param  [type] $_return  set return field value
	 * @param  string $_type    set type of post
	 * @return [type]           an array or number
	 */
	public static function get($_user_id = null, $_return = null, $_type = 'post')
	{
		// calc type if needed
		if($_type === null)
		{
			$_type = "post_type LIKE NOT NULL";
		}
		else
		{
			$_type = "post_type = '". $_type. "'";
		}
		// calc user id if exist
		if($_user_id)
		{
			$_user_id = "AND user_id = $_user_id";
		}
		else
		{
			$_user_id = null;
		}
		// generate query string
		$qry = "SELECT * FROM posts WHERE $_type $_user_id";
		// run query
		if($_return && $_return !== 'count')
		{
			$result = \lib\db::get($qry, $_return);
		}
		else
		{
			$result = \lib\db::get($qry);
		}
		// if user want count of result return count of it
		if($_return === 'count')
		{
			return count($result);
		}
		// return last insert id
		return $result;
	}


	/**
	 * save question into post table
	 * @param  [type] $_title    [description]
	 * @param  [type] $_answersList [description]
	 * @return [type]               [description]
	 */
	public static function insertOrder($_title, $_meta, $_user_id = null)
	{
		$slug    = \lib\utility\filter::slug($_title);
		$pubDate = date('Y-m-d H:i:s');
		$url     = 'order/'.date('Y-m-d').$_user_id.'/'.$slug;
		$_meta   = json_encode($_meta, JSON_UNESCAPED_UNICODE);
		// create query string
		$qry = "INSERT INTO posts
		(
			`post_language`,
			`post_title`,
			`post_slug`,
			`post_url`,
			`post_meta`,
			`post_type`,
			`post_status`,
			`post_publishdate`,
			`user_id`
		)
		VALUES
		(
			'fa',
			'$_title',
			'$slug',
			'$url',
			'$_meta',
			'order',
			'draft',
			'$pubDate',
			$_user_id
		)";
		// run query
		$result        = \lib\db::query($qry);
		// return last insert id
		$newId    = \lib\db::insert_id();
		// save answers into options table
		return $newId;
	}
}
?>