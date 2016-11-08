<?php
namespace addons\content_cp\comments;

use \lib\utility;
use \lib\debug;

class model extends \addons\content_cp\home\model
{
	public function get_comments($_args)
	{
		$comments = \lib\db\comments::get_all();
		return $comments;
	}

	public function post_comments()
	{
		$status     = utility::post("status");
		$comment_id = utility::post("id");

		if(($status == 'approved' || $status == 'unapproved') && $comment_id)
		{
			$args = ['comment_status' => $status];
			$result = \lib\db\comments::update($args, $comment_id);
			if($result)
			{
				debug::true(T_("Comment update to :status",['status' => $status]));
			}
			else
			{
				debug::error(T_("Error in update comment status"));
			}

		}
	}
}
?>