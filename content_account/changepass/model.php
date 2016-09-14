<?php
namespace addons\content_account\changepass;
use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{
	function post_changepass()
	{
		$myid = $this->login('id');
		$newpass   = utility::post('password-new', 'hash');

		$oldpass   = utility::post('password-old');

		$tmp_result =  $this->sql()->tableUsers()->where('id', $myid)->and('user_status','active')->select();
		// if exist
		if($tmp_result->num() == 1)
		{
			$tmp_result       = $tmp_result->assoc();
			$myhashedPassword = $tmp_result['user_pass'];
			// if password is correct. go for login:)
			if (isset($myhashedPassword) && utility::hasher($oldpass, $myhashedPassword))
			{
				$newpass   = utility::post('password-new', 'hash');

				$qry      = $this->sql()->table('users')->where('id', $myid)->set('user_pass', $newpass);
				$sql      = $qry->update();

				$this->commit(function()
				{
					debug::true(T_("change password successfully"));
					$this->redirector()->set_domain()->set_url();
					// \lib\utility\sms::send($_mobile, 'changepass');
				});

				// if a query has error or any error occour in any part of codes, run roolback
				$this->rollback(function() { debug::error(T_("change password failed!")); } );

			}

			// password is incorrect:(
			else
				debug::error(T_("Password is incorrect"));
		}
		// mobile does not exits
		elseif($tmp_result->num() == 0 )
			debug::error(T_("user is incorrect"));

		// mobile exist more than 2 times!
		else
			debug::error(T_("Please forward this message to administrator"));
		// sleep(0.1);
	}
}
?>