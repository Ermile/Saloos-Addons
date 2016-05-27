<?php
namespace lib\db;

/** get users stats **/
class stat_users
{
	/**
	 * this library generate stats from users
	 * v1.1
	 */


	/**
	 * generate signup stats by custom period
	 * @param  string $_period [description]
	 * @return [type]          [description]
	 */
	public static function signup($_period)
	{
		if(!$_period)
		{
			$_period = "%Y-%m";
		}
		$qry ="SELECT
			DATE_FORMAT(user_createdate, '$_period') as date,
			count(id) as total
		FROM users
		GROUP BY
			date
		";

		$result = \lib\db::get($qry);
		return $result;
	}
}
?>