<?php
namespace addons\content_account\login;
use \lib\utility;
use \lib\debug;

class model extends \addons\content_account\home\model
{
	private $user_id = 0;

	public function post_login()
	{
		// get parameters and set to local variables
		$mymobile   = utility::post('mobile','filter');
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

		// check for mobile exist
		$query =
		"
			SELECT
				*
			FROM
				users
			WHERE
				users.user_mobile = '$mymobile' AND
				users.user_status IN ('active', 'removed', 'awaiting')
			LIMIT 1
		";

		$tmp_result = \lib\db::get($query, null, true);

		// if exist
		if(isset($tmp_result['id']))
		{
			$this->user_id = $tmp_result['id'];

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

				$myfields =
				[
					'id',
					'user_displayname',
					'user_mobile',
					'user_meta',
					'user_status',
				];

				$this->setLoginSession($tmp_result, $myfields);
				if(utility::post('remember_me'))
				{
					$user_id = (int) $tmp_result['id'];
					if(\lib\utility::cookie('remember_me'))
					{
						$get = \lib\db\options::get([
						'user_id' 		=> $user_id,
						'option_cat'	=> 'session',
						'option_key'	=> 'rememberme',
						'option_status'	=> 'enable',
						'option_value'	=> \lib\utility::cookie('remember_me'),
						'limit'			=> 1
						]);
						if($get)
						{
							\lib\db\options::delete($get['id']);
						}
					}

					$uniq_id = urlencode(\lib\utility::hasher(time() . $user_id)) . rand(701, 1301);
					$insert = \lib\db\options::insert([
						'user_id' 		=> $user_id,
						'option_cat'	=> 'session',
						'option_key'	=> 'rememberme',
						'option_value'	=> $uniq_id,
						'date_modified'	=> date("Y-m-d H:i:s", time())
						]);
					$service_name = '.' . \lib\router::get_domain(count(\lib\router::get_domain(-1))-2);
					$tld = \lib\router::get_domain(-1);
					$service_name .= '.' . end($tld);
					setcookie("remember_me", $uniq_id, time() + (60*60*24*365), '/', $service_name);
				}
				// ======================================================
				// you can manage next event with one of these variables,
				// commit for successfull and rollback for failed
				// if query run without error means commit
				$this->commit(function()
				{
					// create code for pass with get to service home page
					$this->referer();
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