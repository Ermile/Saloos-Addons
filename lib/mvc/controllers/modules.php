<?php
namespace lib\mvc\controllers;
trait modules{
	static $modules_list = array();

	static function default_modules(){

		self::add_modules('home', array(
			'title' 		=> T_('Dashboard'),
			));

		self::add_modules('posts', array(
			'desc' 			=> T_('Use posts to share your news in specefic category'),
			'icon'			=> 'file-text-o',
			'permissions'	=> ['view', 'add', 'edit', 'delete', 'admin'],
			));

		self::add_modules('tags', array(
			'permissions'	=> ['view', 'add', 'edit', 'delete'],
			'desc' 			=> T_('Assign keywords to your posts using tags'),
			'parent'		=> 'posts'
			));

		self::add_modules('categories', array(
			'desc' 			=> T_('Use categories to define sections of your site and group related posts'),
			'title' 		=> T_('Categories'),
			'permissions'	=> ['view', 'add', 'edit', 'delete'],
			'parent'		=> 'posts'
			));

		self::add_modules('pages', array(
			'desc' 			=> T_('Use pages to share your static content'),
			'icon'			=> 'files-o',
			'permissions'	=> ['view', 'add', 'edit', 'delete', 'admin'],
			));

		self::add_modules('books', array(
			'disable'		=> true,
			'desc'			=> T_('Use book to define important parts to use in posts'),
			'title' 		=> T_('Books'),
			'icon'			=> 'book',
			'permissions'	=> ['view', 'add', 'edit', 'delete', 'admin']
			));

		self::add_modules('bookcategories', array(
			'desc' 			=> T_('Use categories to define sections of your site and group related books'),
			'title' 		=> T_('Book Categories'),
			'parent'		=> 'books',
			'permissions'	=> ['view', 'add', 'edit', 'delete']
			));

		self::add_modules('socialnetworks', array(
			'disable'		=> true,
			'desc' 			=> T_('Publish new post in social networks'),
			'icon'			=> 'share-alt',
			'permissions'	=> ['view', 'add', 'edit', 'delete', 'admin']
			));

		self::add_modules('attachments', array(
			'permissions' 	=> ['view', 'add', 'edit', 'delete', 'admin'],
			'desc' 			=> T_('Upload your media'),
			'icon'			=> 'picture-o',
			));

		self::add_modules('filecategories', array(
			'desc' 			=> T_('Use categories to define sections of your site and group related files'),
			'title' 		=> T_('File Categories'),
			'parent'		=> 'files',
			'permissions'	=> ['view', 'add', 'edit', 'delete']
			));

		self::add_modules('polls', array(
			'disable' 		=> true,
			'icon'			=> 'hand-paper-o',
			'permissions'	=> ['view', 'add', 'edit', 'delete', 'admin']
			));

		self::add_modules('pollcategories', array(
			'parent' 		=> 'polls',
			'permissions'	=> ['view', 'add', 'edit', 'delete']
			));

		self::add_modules('users', array(
			'icon'			=> 'user',
			'permissions'	=> ['view', 'add', 'edit', 'delete', 'admin']
			));

		self::add_modules('permissions', array(
			'permissions'	=> ['view', 'add', 'edit', 'delete'],
			'icon' 			=> 'lock'
			));

		self::add_modules('visitors', array(
			'childless' 	=> true,
			'icon'			=> 'line-chart',
			'permissions'	=> ['view']
			));

		self::add_modules('options', array(
			'permissions' 	=> ['view', 'edit'],
			'icon'			=> 'cog',
			'submodules'	=> ['status' => true,'general' => true,'config' => true,'sms' => true,'social' => true,'account' => true]
			));

		self::add_modules('tools', array(
			'permissions'	=> ['view'],
			'icon'			=> 'wrench'
			));
		if(Tld !== 'dev')
			self::edit_modules('tools', array('disable' => true));

		self::add_modules('lock', array(
			'parent' => 'profile',
			));

		self::add_modules('logout', array(
			'parent' => 'profile',
			));

		self::add_modules('profile', array('parent' => 'global', 'childless' => true, 'permissions' => ['view']));


	}

	static function get_modules($name = null, $attr = null){
		if(!$name){
			$arr_sort = array();
			$arr_unsort = array();
			foreach (self::$modules_list as $key => $value) {
				if(array_key_exists('order', $value) && is_int($value['order']))
					$arr_sort[$key] = $value;
				else
					$arr_unsort[$key] = $value;
			}
			return array_merge($arr_sort, $arr_unsort);
		}else{
			if(!$name){
				return self::$modules_list[$name];
			}else{
				return array_key_exists($attr, self::$modules_list[$name]) ? self::$modules_list[$name][$attr] : false;
			}
		}
	}

	static function edit_modules($name, $attr){
		self::$modules_list[$name] = array_merge(self::$modules_list[$name], $attr);
	}

	static function add_modules($_name, $_attr = array()){
		$_attr['name'] = $_name;
		$_attr['title'] = (isset($_attr['title'])) ? $_attr['title'] : T_(ucfirst($_name));
		if(array_key_exists("parent", $_attr)
			&& array_key_exists($_attr['parent'], self::$modules_list)
			&& array_key_exists('disable', self::$modules_list[$_attr['parent']])
			&& self::$modules_list[$_attr['parent']]['disable'] == true
			){
			$_attr['disable'] = true;
		}
		// var_dump($_name, $_attr);
		self::$modules_list[$_name] = $_attr;
	}

	static function modules_search($attr){
		$arr = array();
		foreach (self::get_modules() as $key => $value) {
			if(array_key_exists($attr, $value)){
				$arr[$key] = $value[$attr];
			}
		}
		return $arr;
	}

	static function modules_hasnot($attr){
		$arr = array();
		foreach (self::get_modules() as $key => $value) {
			if(!array_key_exists($attr, $value)){
				$arr[$key] = $value;
			}
		}
		return $arr;
	}
}
?>