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
			foreach ($this->permModulesList($myContent) as $myLoc =>$value)
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

	/**
	 * return the modules of each part of system
	 * first check if function declare then return the permissions module of this content
	 * @param  [string] $_content content name
	 * @return [array]  return the permission modules list
	 */
	public function permModulesList($_content)
	{
		$myList      = [];
		$contentName = preg_replace("/content(_[^\/]*)?\//", "content" . $_content, get_class(\lib\main::$controller));
		// $contentName = '\addons\content_'. $_content. '\home\controller';
		if(method_exists($contentName, 'permModules'))
		{
			// if module exist call it
			$contentInstance = new $contentName;
			$myList          = $contentInstance->permModules();
			if(!is_array($myList))
			{
				$myList = [];
			}

			// recheck return value from permission modules list func
			foreach ($myList as $permLoc => $permValue)
			{
				if(is_array($permValue))
				{
					$permCond = ['view', 'add', 'edit', 'delete', 'admin'];
					$myList[$permLoc] = null;
					foreach ($permCond as $value)
					{
						if(in_array($value, $permValue))
						{
							// $myList[$permLoc][$value] = 'show';
							$myList[$permLoc][$value] = 'hide';
						}
						else
						{
							// $myList[$permLoc][$value] = 'hide';
						}
					}
				}
				else
				{
					$myList[$permLoc] = null;
				}
			}
		}
		// var_dump($myList);
		// $myList = array_flip($myList);


		return $myList;
	}
}
?>