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
		if(trim($mypass) == '')
		{
			debug::error(T_("Please set password!"));
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
				\lib\utility\sms::send([
					'mobile' 	=> $mymobile,
					'template' 	=> 'Verify-fa',
					'token'		=> $code,
					'type'		=> 'call'
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