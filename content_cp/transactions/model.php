<?php
namespace addons\content_cp\transactions;

use \lib\utility;
use \lib\debug;
class model extends \mvc\model
{
	public function transactions_list()
	{
		$meta   = [];
		$result = \lib\db\transactions::search(null, $meta);
		return $result;
	}
}
?>
