<?php
namespace lib\model;
use \lib\utility;

class api_engine
{
	public function __construct($_options = array())
	{
		$options = [
			'request' 		=> [],
			'dynamic_debug'	=> false
		];
		$options = array_merge($options, $_options);
		if(!isset($options['method']))
		{
			if(isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] == 'application/json')
			{
				$this->method 		= 'input_json_to_array';
			}
			else
			{
				$this->method 		= 'post';
			}
		}
		$this->request 		= $options['request'];
		$this->dynamic_debug 	= $options['dynamic_debug'];

		if($this->dynamic_debug)
		{
			$this->debug = new \lib\debug();
		}
		else
		{
			$this->debug = '\lib\debug';
		}
		switch ($this->method) {
			case 'get':
				$this->request = utility::get();
				break;

			case 'array':
				$this->request = utility\safe::safe($this->request);
				break;

			case 'object':
				$this->request = utility\safe::safe($this->request);
				break;

			case 'input_json_to_array':
				$array = json_decode(file_get_contents('php://input'), true);
				$this->request = utility\safe::safe($array);
				break;

			case 'input_json_to_object':
				$object = json_decode(file_get_contents('php://input'));
				$this->request = utility\safe::safe($object);
				break;

			default:
				$this->request = utility::post();
				break;
		}
	}

	public function request()
	{
		$args = func_get_args();
		$request = $this->request;
		if(empty($args))
		{
			return $request;
		}

		foreach ($args as $key => $value) {
			if(is_object($request))
			{
				if(!isset($request->$value))
				{
					return null;
				}
				$request = $request->$value;
			}
			elseif(is_array($request))
			{
				if(!isset($request[$value]))
				{
					return null;
				}
				$request = $request[$value];
			}
			else
			{
				return null;
			}
		}
		return $request;
	}

	public function debug()
	{
		$args = func_get_args();
		if($this->dynamic_debug)
		{
			return call_user_func_array([$this->debug, $args[0]], array_splice($args, 1));
		}
		else
		{
			return call_user_func_array(['\lib\debug', $args[0]], array_splice($args, 1));
		}
	}

	public function return()
	{
		if($this->dynamic_debug)
		{
			return $this->debug->compile();
		}
		else
		{
			return \lib\debug::compile();
		}
	}
}
?>