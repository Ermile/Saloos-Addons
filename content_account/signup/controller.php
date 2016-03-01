<?php
namespace content_account\signup;

class controller extends \mvc\controller
{
	function options()
	{
		if($this->option('register'))
		{
			$this->post('signup')->ALL();
			$this->get()         ->ALL();
		}
		else
		{
			\lib\error::access(T_("Public registration is disabled!"));
		}
	}
}
?>