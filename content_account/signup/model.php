<?php
namespace content_account\signup;
use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{
	public function post_signup()
	{
		// get parameters and set to local variables
		$mymobile   = utility::post('mobile', 'filter');
		$mypass     = utility::post('password', 'hash');
		$myperm     = $this->option('permissions');
		// check for mobile exist
		$tmp_result = $this->sql()->table('users')
			->where('user_mobile', $mymobile)
			->select();

		// if exist
		if($tmp_result->num() == 1)
		{
			debug::error(T_("Mobile number exist!"));
		}

		// if new mobile number
		elseif($tmp_result->num() == 0 )
		{
			$qry      = $this->sql()->table('users')
							->set('user_mobile',     $mymobile)
							->set('user_pass',       $mypass)
							->set('user_permission', $myperm)
							->set('user_createdate', date('Y-m-d H:i:s'));
			$sql      = $qry->insert();


			// ======================================================
			// you can manage next event with one of these variables,
			// commit for successfull and rollback for failed
			// if query run without error means commit
			$this->commit(function($_mobile)
			{

				// \lib\utility\Sms::send($_mobile, 'signup', $_code);
				debug::true(T_("Register successfully"));

				// $this->redirector()->set_url('verification?from=signup&mobile='.$_mobile.'&referer='.$myreferer);
				$this->redirector()->set_url('login?from=signup&cp=1&mobile='.$_mobile);
			}, $mymobile);

			// if a query has error or any error occour in any part of codes, run roolback
			$this->rollback(function() { debug::error(T_("Register failed!")); } );
		}

		// if mobile exist more than 2 times!
		else
			debug::error(T_("Please forward this message to administrator"));
	}
}
?>