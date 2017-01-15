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
		$query ="SELECT *
			FROM  users
			WHERE
				users.user_mobile = '$mymobile' AND
				users.user_status IN ('active', 'removed', 'awaiting')
			LIMIT 1
		";
		$tmp_result = \lib\db::get($query, null, true);

		// $tmp_result =  $this->sql()->tableUsers()->whereUser_mobile($mymobile)
		// 	->groupOpen()
		// 	->and('user_status','active')
		// 	->or('user_status', 'removed')
		// 	->groupClose()
		// 	->select();
		// or user status == 'removed' and set the user status on old user status in user meta if password is ok
		//
		// $tmp_result =  $this->sql()->tableUsers()->select();

		// if exist
		if(isset($tmp_result['id']))
		{
			// $tmp_result       = $tmp_result->assoc();
			$myhashedPassword = $tmp_result['user_pass'];
			// if password is correct. go for login:)
			if (isset($myhashedPassword) && utility::hasher($mypass, $myhashedPassword))
			{
				// you can change the code way easily at any time!
				// $qry		= $this->sql()->tableUsers ()
				// 				->setUser_logincounter  ($tmp_result['user_logincounter'] +1)
				// 				->whereId               ($tmp_result['id']);
				// $sql		= $qry->update();

				// if the user has removed account and try to login
				// we set the user_status of this user to old status befor remove account
				if($tmp_result['user_status'] == 'removed')
				{
					$update_status = 'awaiting';
					if(isset($tmp_result['user_meta']))
					{
						$meta = json_decode($tmp_result['user_meta'], true);
						if($meta && isset($meta['old_status']))
						{
							$update_status = $meta['old_status'];
						}
					}
					\lib\db\users::update(['user_status' => $update_status], $tmp_result['id']);
				}

				$myfields = array('id',
										'user_displayname',
										'user_mobile',
										'user_meta',
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

					// set redirect to homepage
					$this->redirector()->set_domain()->set_url();

					if(\lib\utility\option::get('account', 'status'))
					{
						$_redirect_sub = \lib\utility\option::get('account', 'meta', 'redirect');
						if($_redirect_sub !== 'home')
						{
							if(\lib\utility\option::get('config', 'meta', 'fakeSub'))
							{
								$this->redirector()->set_url($_redirect_sub);
							}
							else
							{
								$this->redirector()->set_sub_domain($_redirect_sub);
							}
						}
					}
					// do not use pushstate and run link direct
					debug::msg('direct', true);
				});

				$this->rollback(function() { debug::error(T_("Login failed!")); });
			}
			// password is incorrect:(
			else
			{
				debug::error(T_("Mobile or password is incorrect"));
			}
		}
		// mobile does not exits
		elseif(is_array($tmp_result))
		{
			debug::error(T_("Mobile or password is incorrect"));
		}
		// mobile exist more than 2 times!
		else
		{
			debug::error(T_("Please forward this message to administrator"));
		}
		// sleep(0.1);
	}
}
?>