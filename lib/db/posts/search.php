<?php
namespace lib\db\posts;

trait search
{

	/**
	 * search in posts
	 *
	 * @param      <type>  $_string   The string
	 * @param      array   $_options  The options
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function search($_string = null, $_options = [])
	{
		$where = []; // conditions

		if(!$_string && empty($_options))
		{
			// default return of this function 10 last record of poll
			$_options['get_last'] = true;
		}

		$default_options =
		[

			// enable|disable paignation,
			"pagenation"  => true,

			// for example in get_count mode we needless to limit and pagenation
			// default limit of record is 10
			// set the limit  = null and pagenation = false to get all record whitout limit
			"limit"           => 10,

			// for manual pagenation set the statrt_limit and end limit
			"start_limit"     => 0,

			// for manual pagenation set the statrt_limit and end limit
			"end_limit"       => 10,

			// the the last record inserted to post table
			"get_last"        => false,

			// default order by ASC you can change to DESC
			"order"           => "ASC",

			// custom sort by field
			"sort"			  => null,

			// default we check the language of user
			// and load only the post was this language or her lang is null
			"check_language"  => true,

		];
		$_options = array_merge($default_options, $_options);

		// ------------------ pagenation
		$pagenation = false;
		if($_options['pagenation'])
		{
			// page nation
			$pagenation = true;
		}

		$limit         = null;
		$public_fields = " * ";
		if($_options['limit'])
		{
			$limit = $_options['limit'];
		}
		// ------------------ get last
		$order = null;
		if($_options['get_last'])
		{
			$order = " ORDER BY posts.id DESC ";
		}
		else
		{
			$order = " ORDER BY posts.id $_options[order] ";
		}

		$start_limit = $_options['start_limit'];
		$end_limit   = $_options['end_limit'];


		if(isset($_options['post_language']))
		{
			$_options['check_language'] = false;
		}

		if($_options['check_language'] === true)
		{
			$language = \lib\define::get_language();
			$where[] = " (posts.post_language IS NULL OR posts.post_language = '$language') ";
		}

		// ------------------ remove system index
		// unset some value to not search in database as a field
		unset($_options['pagenation']);
		unset($_options['limit']);
		unset($_options['get_last']);
		unset($_options['start_limit']);
		unset($_options['end_limit']);
		unset($_options['order']);
		unset($_options['all']);
		unset($_options['check_language']);
		unset($_options['sort']);

		foreach ($_options as $key => $value)
		{
			if(is_array($value))
			{
				// for similar "posts.`field` LIKE '%valud%'"
				$where[] = " posts.`$key` $value[0] $value[1] ";
			}
			else
			{
				$where[] = " posts.`$key` = '$value' ";
			}
		}

		$where = join($where, " AND ");

		$search = null;
		if($_string != null)
		{

			$search =
			"(
				posts.post_title 	LIKE '%$_string%' OR
				posts.post_content 	LIKE '%$_string%' OR
				posts.post_url 		LIKE '%$_string%' OR
				posts.post_meta 	LIKE '%$_string%'
			)";
			if($where)
			{
				$search = " AND ". $search;
			}
		}

		if($pagenation)
		{
			$pagenation_query = "SELECT	* FROM posts WHERE $where $search ";
			list($limit_start, $limit) = \lib\db::pagnation($pagenation_query, $limit);
			$limit = " LIMIT $limit_start, $limit ";
		}
		else
		{
			// in get count mode the $limit is null
			if($limit)
			{
				$limit = " LIMIT $start_limit, $end_limit ";
			}
		}

		$query =
		"
			SELECT
				`id` 				AS `id`,
				`post_language` 	AS `post_language`,
				`post_title` 		AS `post_title`,
				`post_slug` 		AS `post_slug`,
				`post_url` 			AS `post_url`,
				`post_content` 		AS `post_content`,
				`post_meta` 		AS `post_meta`,
				`post_type` 		AS `post_type`,
				`post_comment` 		AS `post_comment`,
				`post_count` 		AS `post_count`,
				`post_order` 		AS `post_order`,
				`post_status` 		AS `post_status`,
				`post_parent` 		AS `post_parent`,
				`user_id` 			AS `user_id`,
				`post_publishdate` 	AS `post_publishdate`,
				`date_modified` 	AS `date_modified`
			FROM
				posts
			WHERE
				$where
				$search
			$order
			$limit
			-- posts::search()
		";

		$result = \lib\db::get($query);
		$result = \lib\utility\filter::meta_decode($result);
		return $result;
	}
}
?>