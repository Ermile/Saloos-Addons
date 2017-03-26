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
			$_SESSION['ref'] = \lib\utility::get("ref");

			// $service_name = '.' . \lib\router::get_domain(count(\lib\router::get_domain(-1))-2);
			// $tld = \lib\router::get_domain(-1);
			// $service_name .= '.' . end($tld);
			// setcookie("ref", \lib\utility::get("ref"), time() + (60*60*24*30), '/', $service_name);
		}
	}
}
?>