<?php
namespace lib\db;

/** work with polls **/
class chart_polls
{
	/**
	 * this library work with acoount
	 * v1.0
	 */


	/**
	 * [answeredCount description]
	 * @param  [type] $_period [description]
	 * @return [type]          [description]
	 */
	public static function answeredCount($_period)
	{
		if(!$_period)
		{
			$_period = "%Y-%m";
		}
		$qry ="SELECT
				DATE_FORMAT(date_modified, '$_period') as date,
				count(id) as total
			FROM options
			WHERE
				date_modified IS NOT Null AND
			 	date_modified != 0 AND
				option_cat LIKE 'polls\_%' AND
				option_key LIKE 'answer\_%' AND
				option_status = 'enable'
			GROUP BY date
		";

		$result = \lib\db::get($qry);
		return $result;
	}
}
?>