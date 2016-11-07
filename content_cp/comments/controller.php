<?php
namespace addons\content_cp\comments;

class controller extends \addons\content_cp\home\controller
{
	function _route()
	{
		$this->get("comments", "comments")->ALL("comments");
	}
}
?>