<?php
namespace addons\content_cp\transactions;

class view extends \mvc\view
{
	public function view_list($_args)
	{
		$list = $this->model()->transactions_list($_args);

		$this->data->transactions_list = $list;

		$field =
		[
			'id',
			'title',
			'transactionitem_id',
			'user_id',
			'type',
			'unit',
			'plus',
			'minus',
			'budgetbefore',
			'budget',
			'status',
			'meta',
			'desc',
			'related_user_id',
			'parent_id',
			'finished',
			'date',
			'mobile',
			'displayname',
			'caller',
		];

		$this->order_url($_args, $field);

		if(isset($this->controller->pagnation))
		{
			$this->data->pagnation = $this->controller->pagnation_get();
		}

		if(isset($_args->get("search")[0]))
		{
			$this->data->get_search = $_args->get("search")[0];
		}
	}


	/**
	 * MAKE ORDER URL
	 *
	 * @param      <type>  $_args    The arguments
	 * @param      <type>  $_fields  The fields
	 */
	public function order_url($_args, $_fields)
	{
		$order_url = [];
		foreach ($_fields as $key => $value)
		{

			if(isset($_args->get("sort")[0]))
			{
				if($_args->get("sort")[0] == $value)
				{
					if(mb_strtolower($_args->get("order")[0]) == mb_strtolower('ASC'))
					{
						$order_url[$value] = "sort=$value/order=desc";
					}
					else
					{
						$order_url[$value] = "sort=$value/order=asc";
					}
				}
				else
				{

					$order_url[$value] = "sort=$value/order=asc";
				}
			}
			else
			{
				$order_url[$value] = "sort=$value/order=asc";
			}
		}

		$this->data->order_url = $order_url;
	}
}
?>