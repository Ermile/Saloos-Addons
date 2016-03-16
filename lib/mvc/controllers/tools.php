<?php
namespace lib\mvc\controllers;

trait tools
{
	/**
	 * call model func and return needed option in all condition
	 * @return [type] return string or array contain option value
	 */
	public function option($_key = null, $_type = 'value', $_meta = false, $_model = false)
	{
		if(!$_model)
		{
			$_model = $this->model();
		}
		$qry_options = $_model->options;

		// get list of permissions
		// $permList = $this->permList();
		$permList = $_model->permissions;
		$qry_result['permissions'] =
		[
			'value' => null,
			'meta'  => $permList
		];

		foreach ($qry_options as $key => $row)
		{
			if($row['option_key'] == 'permissions')
			{
				$myValue = $row['option_value'];
				$myMeta  = $permList;
			}
			else
			{
				$myValue  = $row['option_value'];
				$myMeta   = $row['option_meta'];
				$myStatus = $row['option_status'];
				if($myStatus === 'enable' || $myStatus === 'on' || $myStatus === 'active')
				{
					$myStatus = true;
				}
				else
				{
					$myStatus = false;
				}

				if(substr($myValue, 0,1) == '{')
				{
					$myValue = json_decode($myValue, true);
				}

				if(substr($myMeta, 0,1) == '{')
				{
					$myMeta = json_decode($myMeta, true);
				}
			}

			$qry_result[$row['option_key']] =
			[
				'value'  => $myValue,
				'meta'   => $myMeta,
				'status' => $myStatus
			];
		}

		if($_key && isset($qry_result[$_key]))
		{
			if($_type)
			{
				if(isset($qry_result[$_key][$_type]))
				{
					if($_meta)
					{
						if(isset($qry_result[$_key][$_type][$_meta]))
						{
							$qry_result = $qry_result[$_key][$_type][$_meta];
						}
						else
						{
							$qry_result = null;
						}
					}
					else
					{
						$qry_result = $qry_result[$_key][$_type];
					}
				}
				else
					$qry_result = null;
			}
			else
			{
				$qry_result = $qry_result[$_key];
			}
		}
		else
		{
			$qry_result = null;
		}
		// var_dump($qry_result);
		return $qry_result;


		// return $_model->sp_get_options(...func_get_args());
	}


	/**
	 * convert numver to en
	 * @param  [type] $string [description]
	 * @return [type]         [description]
	 */
	function convert_Num2En($string)
	{
		$persian = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
		$num = range(0, 9);
		return str_replace($persian, $num, $string);
	}
}
?>