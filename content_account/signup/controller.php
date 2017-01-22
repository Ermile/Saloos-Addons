<?php
namespace addons\content_account\signup;

class controller extends \addons\content_account\home\controller
{
	function _route()
	{
		if($this->login())
		{
			\lib\debug::true(T_("you are logined to system!"));
			$this->referer();
		}
		$canAccess = $this->option('account', 'meta', 'register');
		if($canAccess)
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