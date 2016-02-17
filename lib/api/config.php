<?php
namespace addons\lib\api;
class config extends \lib\api\config
{
	public function check($route)
	{
		// echo $this->api_method;
		parent::check(...func_get_args());
	}
}
?>