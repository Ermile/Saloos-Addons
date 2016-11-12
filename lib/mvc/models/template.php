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

			$language = substr(\lib\router::get_storage('language'), 0, 2);
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
}
?>
