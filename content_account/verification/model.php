<?php
namespace addons\content_account\verification;
use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{
	public function post_verification()
	{
		$this->put_verification();
	}

	public function put_verification()
	{
		// get parameters and set to local variables
		$mycode     = utility::post('code');
		$mymobile   = utility::post('mobile','filter');
		$myuserid   = $this->sql()->table('users')->field('id')->where('user_mobile', $mymobile)->select()->assoc('id');

		// check for mobile exist
		$tmp_result = $this->sql()->table('logs')
						  ->where        ('user_id'       , $myuserid)
						  ->and          ('log_data'      , $mycode)
						  ->and          ('log_status'    , 'enable')
						  ->select();

		if($tmp_result->num())
		{
			// mobile and code exist update the record and verify
			$qry = $this->sql()->table('logs')
							  ->set          ('log_status',   'expire')
							  ->where        ('user_id'       , $myuserid)
							  ->and          ('log_data'      , $mycode)
							  ->and          ('log_status'    , 'enable');
			$sql		= $qry->update();
			$sql_users  = $this->sql()->table('users')->where('id', $myuserid)->set('user_status', 'active')->update();



			// ======================================================
			// you can manage next event with one of these variables,
			// commit for successfull and rollback for failed
			//
			// if query run without error means commit
			$this->commit(function($_mobile, $_userid)
			{
				$myfrom   = utility\cookie::read('from');
				if($myfrom == 'signup')
				{
					// login user to system
					$this->model()->setLogin($_userid);
					//Send SMS
					\lib\utility\Sms::send($_mobile, 'verification');
					debug::true(T_("verify successfully."));
				}
				else
				{
					// login user to system
					$this->model()->setLogin($_userid, false);
					$this->redirector()->set_url('changepass');

					$myreferer = utility\cookie::write('mobile', $_mobile, 60*5);
					$myreferer = utility\cookie::write('from', 'verification', 60*5);
					debug::true(T_("verify successfully.").' '.T_("please Input your new password"));
				}
			}, $mymobile, $myuserid);

			// if a query has error or any error occour in any part of codes, run roolback
			$this->rollback(function() { debug::error(T_("verify failed!")); } );
		}

		// mobile does not exits
		elseif($tmp_result->num() == 0 )
			debug::error(T_("this data is incorrect"));

		// mobile exist more than 2 times!
		else
			debug::error(T_("please forward this message to administrator"));
	}
}
?>