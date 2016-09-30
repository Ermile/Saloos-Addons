<?php
namespace addons\attachments;
class model
{
	public function get_search_attachments($_args)
	{
		$this->controller->on_search_attachments = true;
		$where = '';
		$search = $_args->get_search(0);
		$image = $_args->get_image(0);
		$video = $_args->get_video(0);
		$audio = $_args->get_audio(0);
		$other = $_args->get_other(0);
		if($search)
		{
			$where .= "(post_title LIKE '%$search%' OR post_content LIKE '%$search%')";
		}

		$_type = ['image', 'audio', 'video'];
		$type = array();
		if($image)
		{
			array_push($type, 'image');
		}
		if($video)
		{
			array_push($type, 'video');
		}
		if($audio)
		{
			array_push($type, 'audio');
		}
		if($other)
		{
			array_push($type, 'other');
		}
		if(count($type) > 0 && count($type) < 4)
		{
			$where .= empty($where) ? '' : " AND ";
			if($other)
			{
				if(count($type) == 1)
				{
					$_type = join("\"' ,'\"", $_type);
					$where .= "json_extract(post_meta, '$.type') NOT IN ('\"$_type\"')";
				}
				else
				{
					$_type = join("\"' ,'\"", array_diff($_type, $type));
					$type = count($type) > 1 ? "\"" . join("\"' ,'\"", $type) . "\"" : $type[0];
					$where .= "(json_extract(post_meta, '$.type') IN ('$type')";
					$where .= " OR json_extract(post_meta, '$.type') NOT IN ('\"$_type\"'))";

				}
			}
			else
			{
				$type = count($type) > 1 ? "\"" . join("\"' ,'\"", $type) . "\"" : $type[0];
				$where .= "json_extract(post_meta, '$.type') in ('$type')";
			}
		}
		$where .= empty($where) ? '' : " AND ";
		$where .= "post_type = 'attachment'";
		$query = "SELECT * FROM posts WHERE $where";
		$result = \lib\db::get($query);
		$decode_result = \lib\utility\filter::decode_meta($result);
		return $decode_result;
	}
}
?>