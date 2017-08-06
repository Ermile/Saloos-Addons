<?php
namespace addons\content_cp\transactions;

class view extends \mvc\view
{
	public function view_list()
	{
		$list = $this->model()->transactions_list();
		$this->data->transactions_list = $list;

		if(isset($this->controller->pagnation))
		{
			$this->data->pagnation = $this->controller->pagnation_get();
		}
	}
}
?>