<?php
namespace lib\model;
use \lib\utility;

trait api_engine
{
	public function ae_construct()
	{
		if(!isset($this->ae_method))
		{
			$this->ae_method 		= 'post';
		}
		if(!isset($this->ae_request))
		{
			$this->ae_request 		= [];
		}
		if(!isset($this->ae_dynamic_debug))
		{
			$this->ae_dynamic_debug 		= false;
		}

		switch ($this->ae_method) {
			case 'get':
				$this->ae_request = utility::get();
				break;

			case 'array':
				$this->ae_request = utility\safe::safe($this->ae_request);
				break;

			case 'object':
				$this->ae_request = utility\safe::safe($this->ae_request);
				break;

			case 'input_json_to_array':
				$array = json_decode(file_get_contents('php://input'), true);
				$this->ae_request = utility\safe::safe($array);
				break;

			case 'input_json_to_object':
				$object = json_decode(file_get_contents('php://input'));
				$this->ae_request = utility\safe::safe($object);
				break;

			default:
				$this->ae_request = utility::post();
				break;
		}
	}

	public function ae_request()
	{
		$args = func_get_args();
		$request = $this->ae_request;
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
}
?>