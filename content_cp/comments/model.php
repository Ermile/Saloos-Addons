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
}
?>