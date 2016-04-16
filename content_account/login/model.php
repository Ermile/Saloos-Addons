<?php
namespace addons\content_account\login;
use \lib\utility;
use \lib\debug;

class model extends \addons\content_account\home\model
{
	public function post_login()
	{
		// get parameters and set to local variables
		$mymobile   = utility::post('mobile','filter');
		$mypass     = utility::post('password');

		// check for mobile exist
		$tmp_result =  $this->sql()->tableUsers()->whereUser_mobile($mymobile)->and('user_status','active')->select();
		// $tmp_result =  $this->sql()->tableUsers()->select();

		// if exist
		if($tmp_result->num() == 1)
		{
			$tmp_result       = $tmp_result->assoc();
			$myhashedPassword = $tmp_result['user_pass'];
			// if password is correct. go for login:)
			if (isset($myhashedPassword) && utility::hasher($mypass, $myhashedPassword))
			{
				// you can change the code way easily at any time!
				// $qry		= $this->sql()->tableUsers ()
				// 				->setUser_logincounter  ($tmp_result['user_logincounter'] +1)
				// 				->whereId               ($tmp_result['id']);
				// $sql		= $qry->update();


				$myfields = array('id',
										'user_displayname',
										'user_mobile',
										'user_status',
										);
				$this->setLoginSession($tmp_result, $myfields);

				// ======================================================
				// you can manage next event with one of these variables,
				// commit for successfull and rollback for failed
				// if query run without error means commit
				$this->commit(function()
				{
					// $this->logger('login');
					// create code for pass with get to service home page
					debug::true(T_("Login Successfully"));
					\lib\utility\session::save();

					$referer  = \lib\router::urlParser('referer', 'host');
					/**

					 * temporary: after fix permissions below line must be delete
					 */
					if($referer == 'archiver.dev' || $referer == 'irancamera.ir')
					{
						$this->redirector()
							->set_domain()
							->set_sub_domain('files')
							->set_url();
					}
					elseif(\lib\router::get_storage('CMS'))
					{
						$this->redirector()->set_domain()->set_sub_domain(\lib\router::get_storage('CMS') )->set_url();
					}
					else
						$this->redirector()->set_domain()->set_url();
				});

				$this->rollback(function() { debug::error(T_("Login failed!")); });
			}
				// password is incorrect:(
			else
				debug::error(T_("Mobile or password is incorrect"));
		}
		// mobile does not exits
		elseif($tmp_result->num() == 0 )
			debug::error(T_("Mobile or password is incorrect"));

		// mobile exist more than 2 times!
		else
			debug::error(T_("Please forward this message to administrator"));
		// sleep(0.1);
	}
}
?>