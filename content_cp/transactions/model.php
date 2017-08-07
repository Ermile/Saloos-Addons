<?php
namespace addons\content_cp\transactions;

use \lib\utility;
use \lib\debug;
class model extends \mvc\model
{
	public function transactions_list($_args)
	{
		$meta   = [];
		$meta['admin'] = true;

		$search = null;
		if(isset($_args->get("search")[0]))
		{
			$search = $_args->get("search")[0];
		}

		if(isset($_args->get("order")[0]))
		{
			$meta['order'] = $_args->get("order")[0];
		}

		if(isset($_args->get("sort")[0]))
		{
			$meta['sort'] = $_args->get("sort")[0];
		}

		if(isset($_args->get("mobile")[0]))
		{
			$meta['mobile'] = $_args->get("mobile")[0];
		}

		if(isset($_args->get("caller")[0]))
		{
			$meta['caller'] = $_args->get("caller")[0];
		}

		if(isset($_args->get("user_id")[0]))
		{
			$meta['user_id'] = $_args->get("user_id")[0];
		}

		if(isset($_args->get("date")[0]))
		{
			$meta['date'] = $_args->get("date")[0];
		}

		$result = \lib\db\transactions::search($search, $meta);
		return $result;
	}
}
?>
