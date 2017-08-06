<?php
namespace addons\content_cp\transactions;

class controller extends \mvc\controller
{
	public function _route()
	{

		\lib\permission::access('cp:transaction', 'block');

		$this->get(false, 'list')->ALL();
	}
}
?>