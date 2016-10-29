<?php
namespace lib\mvc\controllers;

trait ref
{
	/**
	 * Saves a reference.
	 *
	 */
	public function save_ref()
	{
		if(\lib\utility::get("ref") && !$this->login())
		{
			$_SESSION['user']['ref'] = \lib\utility::get("ref");
		}
	}
}
?>