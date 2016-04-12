<?php
namespace addons\content_account\recovery;
use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{
	public function post_recovery()
	{
		// get parameters and set to local variables
		$mymobile   = utility::post('mobile','filter');
		// check for mobile exist
		$tmp_result = $this->sql()->table('users')->where('user_mobile', $mymobile)->select();

		if($tmp_result->num() == 1)
		{
			$myuserid  = $tmp_result->assoc('id');
			$mylogitem = $this->sql()->table('logitems')->field('id')->where('logitem_title', 'account/recovery')->select()->assoc('id');
			if(!isset($mylogitem))
				return;
			$mycode    = utility::randomCode();

			$qry       = $this->sql()->table('logs')
							 ->set          ('logitem_id'    , $mylogitem)
							 ->set          ('user_id'       , $myuserid)
							 ->set          ('log_data'      , $mycode)
							 ->set          ('log_status'    , 'enable')
							 ->set          ('log_createdate', date('Y-m-d H:i:s'));

			// var_dump($qry->insertString());
			// return;
			$sql      = $qry->insert();


			// ======================================================
			// you can manage next event with one of these variables,
			// commit for successfull and rollback for failed
			//
			// if query run without error means commit
			$this->commit(function($_mobile, $_code)
			{
				$myreferer = utility\cookie::read('referer');
				//Send SMS
				\lib\utility\Sms::send($_mobile, 'recovery', $_code);
				debug::true(T_("we send a verification code for you"));
				$myreferer = utility\cookie::write('mobile', $_mobile, 60*5);
				$myreferer = utility\cookie::write('from', 'recovery', 60*5);


				$this->redirector()->set_url('verification?from=recovery&mobile='.$_mobile.'&referer='.$myreferer );
			}, $mymobile, $mycode);

			// if a query has error or any error occour in any part of codes, run roolback
			$this->rollback(function() { debug::error(T_("recovery failed!")); } );
		}

		// mobile does not exits
		elseif($tmp_result->num() == 0 )
			debug::error(T_("Mobile number is incorrect"));

		// mobile exist more than 2 times!
		else
			debug::error(T_("please forward this message to administrator"));
	}
}
?>