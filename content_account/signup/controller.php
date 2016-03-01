<?php
namespace content_account\signup;

class controller extends \mvc\controller
{
	function _route()
	{
		if($this->option('register'))
		{
			$this->post('signup')->ALL();
		}
		else
		{
			\lib\error::access(T_("Public registration is disabled!"));
		}
	}
}
?>