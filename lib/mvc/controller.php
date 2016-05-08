<?php
namespace lib\mvc;
use \lib\router;
class controller extends \lib\controller
{
	use \lib\mvc\controllers\login;
	use \lib\mvc\controllers\sessions;
	use \lib\mvc\controllers\template;
	use \lib\mvc\controllers\tools;
	use \lib\mvc\controllers\url;


	/**
	 * [__construct description]
	 */
	public function __construct()
	{
		parent::__construct();

		// if redirect to main site is enable and all thing is okay
		// then redirect to the target url
		if(
			\lib\utility\option::get('config', 'meta', 'multiDomain') &&
			\lib\utility\option::get('config', 'meta', 'redirectToMain') &&
			$mainSiteUrl = \lib\utility\option::get('config', 'meta', 'mainSite')
		)
		{
			// as soon as posible we create language detector library
			switch (Tld)
			{
				case 'ir':
					$mainSiteUrl .= "/fa";
					break;

				default:
					break;
			}
			// if we are not on debug mode
			$this->redirector($mainSiteUrl)->redirect();
		}

		if(MyAccount && SubDomain == null)
		{
			if(AccountService === Domain)
			{
				$domain = null;
			}
			else
			{
				$domain = AccountService.MainTld;
			}
			$param = $this->url('param');
			if($param)
				$param = '?'.$param;

			switch ($this->module())
			{
				case 'signin':
				case 'login':
					$this->redirector()->set_domain($domain)->set_url(MyAccount. '/login'.$param)->redirect();
					break;

				case 'signup':
				case 'register':
					$this->redirector()->set_domain($domain)->set_url(MyAccount. '/signup'.$param)->redirect();
					break;

				case 'signout':
				case 'logout':
					// if(Domain !== MainService)
						// $this->redirector()->set_domain(MainService.'.'.Tld)->set_url('logout')->redirect();
					$this->redirector()->set_domain()->set_url(MyAccount. '/logout'.$param)->redirect();
					break;

				// case 'favicon.ico':
				// 	$this->redirector()->set_domain()->set_url('static/images/favicon.png')->redirect();
				// 	break;
			}
		}
		$myrep = router::get_repository_name();

		// running template base module for homepage
		if(\lib\router::get_storage('CMS') && $myrep === 'content' && method_exists($this, 's_template_finder') && get_class($this) === 'content\home\controller')
		{
			$this->s_template_finder();
		}
	}
}
?>