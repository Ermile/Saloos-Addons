<?php
namespace addons\content_cp\home;
class controller extends \mvc\controller
{
	function _route()
	{
		if(!$this->login())
		{
			$mydomain = AccountService? AccountService.MainTld: null;
			\lib\debug::warn(T_("first of all, you must login to system!"));
			$this->redirector(null, false)->set_domain($mydomain)->set_url('login')->redirect();
			// exit();
		}
		// Check permission and if user can do this operation
		// allow to do it, else show related message in notify center
		$this->access('cp', null, null, 'block');

		if(method_exists("parent", "_route"))
		{
			parent::_route();
			return;
		}
		// Restrict unwanted module
		if(!$this->cpModlueList())
			\lib\error::page(T_("Not found!"));

		$mymodule = $this->cpModule('table');
		$cpModule = $this->cpModule('raw');
		$mychild  = $this->child();
		$mypath   = $this->url('path','_');

		// Restrict unwanted child
		// if($mychild && !($mychild=='add' || $mychild=='edit' || $mychild=='delete' || $mychild=='list' || $mychild=='options'))
		// 	\lib\error::page(T_("Not found!"));


		if( is_file(addons.'content_cp/'.$cpModule.'/model.php') && !$this->model_name)
			$this->model_name = '\\addons\\content_cp\\'.$cpModule.'\model';

		elseif( is_file(addons.'content_cp/'.$mymodule.'/model.php')  && !$this->model_name)
			$this->model_name = '\\addons\\content_cp\\'.$mymodule.'\model';


		switch ($cpModule)
		{
			case 'home':
				break;

			case 'profile':
				//allow put on profile
				$this->display_name	= 'content_cp/templates/module_profile.html';
				$this->get(null, 'datatable')->ALL($cpModule);
				$this->put('profile')->ALL($cpModule);
				break;

			case 'options':
				//allow put on profile
				// $this->display_name	= 'content_cp/templates/module_options.html';
				$this->get(null, 'datatable')->ALL($cpModule);
				$this->put('options')->ALL($cpModule);
				break;

			// case 'permissions':
			// 	$this->display_name	= 'content_cp/templates/module_permissions.html';
			// 	$this->get(null, 'datatable')->ALL('/^[^\/]*$/');
			// 	$this->put('permissions')->ALL();
			// 	break;

			case 'logout':
				$mydomain = AccountService? AccountService.MainTld: null;
				$this->redirector(null, false)->set_domain($mydomain)->set_url('logout')->redirect();
				break;

			default:
				if( is_file(addons.'content_cp/templates/module_'.$mymodule.'.html') )
					$this->display_name	= 'content_cp/templates/module_'.$mymodule.'.html';
				else
					$this->display_name	= 'content_cp/templates/module_display.html';

				$this->get(null, 'datatable')->ALL('/^[^\/]*$/');

				// on each module except home and some special module with child like /post/add
				if($mychild)
				{
					if( is_file(addons.'content_cp/templates/child_'.$mymodule.'.html') )
						$this->display_name	= 'content_cp/templates/child_'.$mymodule.'.html';
					else
						$this->display_name	= 'content_cp/templates/child_display.html';
					//all("edit=.*")



					// $this->route_check_true = true;

					switch ($mychild)
					{
						case 'delete':
							$this->redirector()->set_url($this->cpModule('raw')); //->redirect();

							// $this->delete($mychild)->ALL('/^[^\/]*\/[^\/]*$/');
							$this->post($mychild)->ALL(["url" => [$cpModule, "/^delete=(\d+)$/"]]);
							$this->get($mychild)->ALL(["url" => [$cpModule, "/^delete=(\d+)$/"]]);		// @hasan: regular?
							// $this->display_name = null;
							// $this->redirector()->set_url($cpModule);//->redirect();
							return;
							break;

						case 'edit':
							$this->get(null, 'child')->ALL(["url" => [$cpModule, "/^edit=(\d+)$/"]]);
							$this->put($mychild)->ALL(["url" => [$cpModule, "/^edit=(\d+)$/"]]);
							break;

						case 'add':
							$this->get(null, 'child')->ALL(["url" => [$cpModule, "add"]]);
							$this->post($mychild)->ALL(["url" => [$cpModule, "add"]]);
							break;

						case 'list':
							// $this->route_check_true = false;
							$this->get($mychild)->ALL(["max" => 2]);
							$this->post($mychild)->ALL(["max" => 2]);
							break;

						case 'options':
							// $this->route_check_true = false;
							$this->get($mychild)->ALL(["max" => 2]);
							$this->post($mychild)->ALL(["max" => 2]);
							break;

						default:
							break;
					}

				}
				break;
		}


		if( is_file(addons.'content_cp/templates/static_'.$mypath.'.html') )
		{
			$this->display_name	= 'content_cp/templates/static_'.$mypath.'.html';
		}
	}


	// if url is outside of our list, return false else if valid module return true
	public function cpModlueList($_module = null)
	{
		// return true;
		$mylist	= [
					'home',
					'posts',
					'pages',
					'tags',
					'categories',
					'filecategories',
					'attachments',
					'books',
					'bookcategories',
					'users',
					'tools',
					'permissions',
					'options',
					'logout',
					'lock',
					'profile',
					'socialnetwork',
					'visitors',
				];
		if($_module == 'all')
		{
			return $mylist;
		}
		elseif($_module == 'permissions')
		{
			$mylist	= [
						// 'home',
						'posts',
						'categories',
						'pages',
						'tags',
						'attachments',
						// 'filecategories',
						'users',
						'tools',
						'permissions',
						'options',
						// 'profile',
					];


			// get features value from view and fix it later
			$features = [];
			if(isset($this->data->feature) && is_array($this->data->feature))
				$features = $this->data->feature;

			foreach ($features as $feature => $enable)
			{
				// if option is not true continue to next
				if(!$enable)
					continue;

				// else switch on enabled feature
				switch ($feature)
				{
					case 'book':
						$mylist = array_push($mylist, $feature);
						$mylist = array_push($mylist, 'bookcategories');
						break;

					case 'socialnetworks':
					case 'visitors':
					default:
						$mylist = array_push($mylist, $feature);
						break;
				}
			}

			return $mylist;
		}

		$_module 	= $_module? $_module: $this->module();
		if(in_array($_module, $mylist))
			return true;
		else
			return false;
	}

	public function cpModule($_resultType = null, $_module = null)
	{
		if($_module === null)
			$_module = $this->module();

		$myprefix = substr($_module, 0, -1);

		$result = ['raw' => $_module, 'table' => $_module, 'prefix' => $myprefix, 'type' => null, 'cat' => null ];
		switch ($_module)
		{
			case 'posts':
				$result['type']   = 'post';
				$result['cat']    = 'cat';
			case 'pages':
				$result['type']   = $result['type']? $result['type']: 'page';
			case 'attachments':
				$result['type']   = $result['type']? $result['type']: 'attachment';
				$result['cat']    = $result['cat']? $result['cat']: 'filecat';
			case 'books':
				$result['type']   = $result['type']? $result['type']: 'book';
				$result['cat']    = $result['cat']? $result['cat']: 'bookcat';

			case 'socialnetwork':
				$result['type']   = $result['type']? $result['type']: 'socialnetwork';

				$result['table']  = 'posts';
				$result['prefix'] = 'post';
				break;

			case 'categories':
				$result['type']   = 'cat';
			case 'filecategories':
				$result['type']   = $result['type']? $result['type']: 'filecat';
			case 'bookcategories':
				$result['type']   = $result['type']? $result['type']: 'bookcat';
			case 'tags':
				$result['type']   = $result['type']? $result['type']: 'tag';

				$result['table']  = 'terms';
				$result['prefix'] = 'term';
				break;

			case 'profile':
				$result['type']   = 'profile';
				$result['cat']    = 'profile';
				$result['table']  = 'options';
				$result['prefix'] = 'option';
				break;

			default:
				$result['type']   = $myprefix;
				break;
		}

		if(array_key_exists($_resultType, $result))
		{
			return $result[$_resultType];
		}
		else
			return $result;
	}


	/**
	 * define perm modules for permission level
	 * @return [array] return the permissions in this content
	 */
	function permModules()
	{
		$mylist	= [
					'posts'       => null,
					'categories'  => ['admin'],
					'pages'       => null,
					'tags'        => ['admin'],
					'attachments' => ['admin'],
					'users'       => null,
					'tools'       => ['admin'],
					'permissions' => ['admin'],
					'options'     => ['admin', 'add', 'delete']
				];

		// get features value from view and fix it later
		$features = [];
		if(isset($this->data->feature) && is_array($this->data->feature))
			$features = $this->data->feature;

		foreach ($features as $feature => $enable)
		{
			// if option is not true continue to next
			if(!$enable)
				continue;

			// else switch on enabled feature
			switch ($feature)
			{
				case 'book':
					array_push($mylist, $feature);
					array_push($mylist, 'bookcategories');
					break;

				case 'socialnetworks':
				case 'visitors':
				default:
					array_push($mylist, $feature);
					break;
			}
		}
		// var_dump($mylist);
		// $mylist = $this->model()->permModuleFill($_content, $mylist);
		// var_dump($mylist);

		return $mylist;
	}
}
?>