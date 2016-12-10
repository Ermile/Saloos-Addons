<?php
namespace lib\mvc\models;

trait template
{
	/**
	 * this fuction check the url entered from user in database
	 * first search in posts and if not exist search in terms table
	 * @return [array] datarow of result if exist else return false
	 */
	function s_template_finder($_args = null)
	{
		// first of all search in url field if exist return row data
		$tmp_result = $this->get_posts(true, $_args);
		if($tmp_result)
		{
			return $tmp_result;
		}
		// if url not exist in posts then search in terms table and if exist return row data
		$tmp_result = $this->get_terms(true, $_args);
		if($tmp_result)
		{
			return $tmp_result;
		}

		// if url not exist in terms table then analyze the url to load similar
		// tag/(.*) || cat/(.*)
		// this url get from term type
		$tmp_result = $this->get_terms_type(true, $_args);
		if($tmp_result)
		{
			return $tmp_result;
		}
		// else retun false
		return false;
	}


	/**
	 * [get_posts description]
	 * @param  boolean $_forcheck [description]
	 * @return [type]             [description]
	 */
	public function get_posts($_forcheck = false, $_args = null)
	{
		// check shortURL
		$shortURL = \lib\db\url::checkShortURL();
		if($shortURL & is_array($shortURL))
		{
			// set datarow
			$datarow = $shortURL;
		}
		else
		{
			$url      = $this->url('path');

			if(substr($url, 0, 7) == 'static/')
			{
				return false;
			}

			$language = \lib\define::get_language();
			$preview  = \lib\utility::get('preview');
			// search in url field if exist return row data
			$post_status = "";
			if(!$preview)
			{
				$post_status = " AND post_status = 'publish' ";
			}

			$qry =
			"
				SELECT
					*
				FROM
					posts
				WHERE
				(
					post_language IS NULL OR
					post_language = '$language'
				) AND
				post_url = '$url'
				$post_status
				LIMIT 1
			";

			$datarow = \lib\db::get($qry, null, true);
			// we have more than one record
			if(isset($datarow[0]))
			{
				$datarow = false;
			}

		}

		if(isset($datarow['id']))
		{
			$post_id = $datarow['id'];
		}
		else
		{
			$datarow = false;
			$post_id  = 0;
		}

		if($datarow && $post_id)
		{


			if($_forcheck && isset($datarow['post_type']) && isset($datarow['post_slug']))
			{
				return
				[
					'table' => 'posts',
					'type' => $datarow['post_type'],
					'slug' => $datarow['post_slug']
				];
			}
			else
			{
				foreach ($datarow as $key => $value)
				{
					// if field contain json, decode it
					if(substr($value, 0, 1) == '{')
					{
						$datarow[$key] = json_decode($value, true);
						if(is_null($datarow[$key]) && preg_match("/meta$/", $key)){
							$datarow[$key] = json_decode(html_entity_decode($value), true);
						}
					}
				}

				// get meta of this post
				$meta = \lib\db\posts::get_post_meta($post_id);
				$datarow['postmeta'] = $meta;

				return $datarow;
			}
		}
		return false;
	}


	/**
	 * Gets the terms type.
	 * by url
	 *
	 * @param      boolean  $_forcheck  The forcheck
	 * @param      <type>   $_args      The arguments
	 */
	public function get_terms_type($_forcheck = false, $_args = null)
	{
		$url        = $this->url('path');
		$split_url  = preg_split("/\//", $url);
		$started_by = null;
		if(isset($split_url[0]))
		{
			$started_by = $split_url[0];
		}

		$search     = null;
		$lenght     = 10;
		$lenght_max = 50;

		if(\lib\utility::get("q") != '')
		{
			$search = \lib\utility::get("q");
		}

		$get_lenght = \lib\utility::get("lenght");
		if($get_lenght != '')
		{
			if(intval($get_lenght) < $lenght_max)
			{
				$lenght = $get_lenght;
			}
		}
		$term_type = null;
		switch ($started_by)
		{
			case 'tag':
			case 'cat':
				$term_type = $started_by;
				break;

			default:
				return false;
				break;
		}
		$datarow = \lib\db\terms::search($search,
				['term_type' => $term_type, 'end_limit' => $lenght]);

		if($_forcheck)
		{
			return
			[
				'table' => 'terms',
				'type' => $term_type,
				'slug' => $term_type,
			];
		}
		else
		{
			return $datarow;
		}
	}
}
?>
