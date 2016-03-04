<?php
namespace ilib;

class main extends \lib\main{
	function check_controller($_controller_name){
		$default_controller = parent::check_controller($_controller_name);
		if(!$default_controller){
			$controller_name = '\addons'. $_controller_name;
			if(!class_exists($controller_name))
				return NULL;
			else
				return $controller_name;
		}
		return $default_controller;
	}
}
?>