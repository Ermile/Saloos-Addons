<?php
namespace addons\content_account\signup;
use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{
	/**
	 * signup to system
	 * @return [type] [description]
	 */
	public function post_signup()
	{
		// get parameters and set to local variables
		$mymobile   = utility::post('mobile', 'filter');
		$mypass     = utility::post('password');

		if(!$mymobile)
		{
			\lib\debug::error(T_("Is not valid mobile"));
			return ;
		}

		$passlen 	= strlen(trim($mypass));

		if($passlen < 5)
		{
			debug::error(T_("Password length must be over five characters!"));
			return ;
		}elseif($passlen > 100)
		{
			debug::error(T_("Password length must be less 50 characters!"));
			return ;
		}

		$myperm     = $this->option('account');
		if(!$myperm)
		{
			$myperm = 'NULL';
		}
		$user_id     = \lib\db\users::signup($mymobile, $mypass, $myperm);
		if($user_id)
		{
			// generate verification code
			// save in logs table
			// set SESSION verification_mobile
			$code = \lib\utility\filter::generate_verification_code($user_id, $mymobile);
			if($code)
			{
				$service_name = \lib\router::get_domain(count(\lib\router::get_domain(-1))-2);
				\lib\utility\sms::send([
					'mobile' 		=> $mymobile,
					'template' 		=> $service_name . '-' . $this->module() . '-' . \lib\define::get_language(),
					'template2' 	=> \lib\db\users::get_count(),
					'token'			=> $code,
					'type'			=> 'call'
					], 'verify');
				debug::true(T_("Register successfully"));
				$_SESSION['tmp']['verify_mobile'] = $mymobile;
				$_SESSION['tmp']['verify_mobile_time'] = time() + (5*60);
				$this->redirector()->set_url('verification');
			}
			else
			{
				debug::error(T_("Please contact to administrator!"));
			}
		}
		elseif($user_id === false)
		{
			debug::error(T_("Mobile number exist!"));
		}
		else
		{
			debug::error(T_("Please contact to administrator!"));
		}
	}
}
?>