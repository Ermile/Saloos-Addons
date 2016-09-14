<?php
namespace lib\mvc\controllers;

trait pagenation
{
	public $pagenation = array();
	public function pagenation_config()
	{
		if(preg_match("#^([1-9][0-9]*)$#", \lib\router::get_url_property('page'), $_page))
		{
			$page = intval($_page[1]);
			\lib\router::set_storage('pagenation', $page);
			$this->pagenation['current'] = $page;
			\lib\router::remove_url_property('page');
		}
		if(preg_match("#^(\d+)$#", \lib\router::get_url_property('length'), $length))
		{
			$this->pagenation_set('length', $length);
			$this->pagenation_set('custom_length', true);
			\lib\router::remove_url_property('length');
		}
	}

	public function pagenation_set($_name, $_value)
	{
		return $this->pagenation[$_name] = $_value;
	}

	public function pagenation_make($_total_records, $_length = null)
	{
		if(!$_length && !$this->pagenation_get('custom_length'))
		{
			\lib\error::internal("PAGENAMTION LENGTH NOTFOUND");
			return;
		}
		else
		{
			$length = !$_length ? $this->pagenation_get('length') : intval($_length);
		}
		$total_pages 		= intval(ceil($_total_records / $length));
		$current 			= $this->pagenation_get('current') ? $this->pagenation_get('current') : 1;
		$next 				= $current +1;
		$prev 				= $current -1;
		if($current > $total_pages)
		{
			$this->pagenation_error();
		}
		$this->pagenation_set('total_pages', $total_pages);
		$this->pagenation_set('current', $current);
		$this->pagenation_set('next', ($next <= $total_pages) ? $next : false);
		$this->pagenation_set('prev', ($prev >= 1) ? $prev : false);
		$this->pagenation_set('count_link', 7);
		$current_url = \lib\router::get_url();
		$this->pagenation_set('current_url', $this->pagenation_get('custom_length') ? $current_url."/length=$length" : $current_url);
		$this->pagenation_set('length', $length);
	}

	public function pagenation_get($_name = null)
	{
		if($_name)
		{
			return array_key_exists($_name, $this->pagenation) ? $this->pagenation[$_name] : null;
		}
		else
		{
			return $this->pagenation;
		}
	}

	public function pagenation_error()
	{
		exit();
	}
}
?>