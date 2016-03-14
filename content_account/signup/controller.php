<?php
namespace addons\content_account\signup;

class controller extends \addons\content_account\home\controller
{
	function _route()
	{
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