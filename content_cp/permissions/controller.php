<?php
namespace addons\content_cp\permissions;

class controller extends \addons\content_cp\home\controller
{
	function _route()
	{
		$this->route_check_true = true;
		$myChild = $this->child();
		if($myChild)
		{
			$this->display_name	= 'content_cp/permissions/display_child.html';
			switch ($myChild)
			{
				case 'add':
					$this->post($myChild)->ALL('permissions/add');
					break;

				case 'edit':
					$this->put($myChild)->ALL('/^[^\/]*\/[^\/]*$/');
					break;

				case 'delete':
					$this->post($myChild)->ALL('/^[^\/]*\/[^\/]*$/');
					$this->get($myChild)->ALL('/^[^\/]*\/[^\/]*$/');
					break;

				default:
					// $this->get()->ALL([
					// 		"max"=>3
					// 	]);
					$this->get()->ALL('/^[^\/]*\/[^\/]*$/');
					return false;
					break;
			}
		}
		else
		{

		}
	}


	public function permListFill($_fill = false)
	{
		$permResult = [];
		$permCond   = ['view', 'add', 'edit', 'delete', 'admin'];

		foreach (\lib\utility\option::contentList() as $myContent)
		{
			// for superusers allow access
			if($_fill === "su")
			{
				$permResult[$myContent]['enable'] = true;
			}
			// if request fill for using in model give data from post and fill it
			elseif($_fill)
			{
				// step1: get and fill content enable status
				$postValue = \lib\utility::post('content-'.$myContent);
				if($postValue === 'on')
				{
					$permResult[$myContent]['enable'] = true;
				}
				else
				{
					$permResult[$myContent]['enable'] = false;
				}
			}
			// else fill as null
			else
			{
				$permResult[$myContent]['enable'] = null;
			}

			// step2: fill content modules status
			foreach (\lib\utility\option::moduleList($myContent) as $myLoc =>$value)
			{
				foreach ($permCond as $cond)
				{
					// for superusers allow access
					if($_fill === "su")
					{
						$permResult[$myContent]['modules'][$myLoc][$cond] = true;
					}
					// if request fill for using in model give data from post and fill it
					elseif($_fill)
					{
						$locName = $myContent. '-'. $myLoc.'-'. $cond;
						$postValue = \lib\utility::post($locName);
						if($postValue === 'on')
						{
							$permResult[$myContent]['modules'][$myLoc][$cond] = true;
						}
						// else
						// {
							// $permResult[$myContent]['modules'][$myLoc][$cond] = null;
						// }
					}
					else
					{
						$permResult[$myContent]['modules'][$myLoc][$cond] = null;
					}
				}
			}
		}
		return $permResult;
	}
}
?>