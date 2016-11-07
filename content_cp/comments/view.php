<?php
namespace addons\content_cp\comments;

class view extends \addons\content_cp\home\view
{
	function view_comments($_args)
	{
		$this->data->comments = $_args->api_callback;
	}
}
?>