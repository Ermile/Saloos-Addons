<?php
namespace addons\attachments;
class view{
	public $display_name;
	function pushState(){
		if(!isset($this->controller->on_search_attachments))
		{
			return;
		}
		$this->controller->display_name= $this->display_name;
		unset($this->data->global->title);
	}
	function view_search_attachments($_args)
	{
		$this->display_name = 'addons/attachments/files-list.html';
		$this->data->attachments = $_args->api_callback;
	}
}
?>