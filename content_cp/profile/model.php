<?php
namespace content_cp\profile;

use \lib\utility;
use \lib\debug;

class model extends \content_cp\home\model
{
	/**
	 * Update profile data
	 * @return run update query and no return value
	 */
	function put_profile()
	{
		// Check permission and if user can do this operation
		// allow to do it, else show related message in notify center
		$this->access('cp', 'posts', 'delete', 'notify');

		$qry = $this->sql()->table('users')->where('id', $this->login('id'))
			->set('user_mobile',      utility::post('mobile'))
			->set('user_email',       utility::post('email'))
			->set('user_displayname', utility::post('displayname'));
		$qry->update();

		$this->commit(function()
		{
			debug::true(T_("Update Successfully"));
			// $this->redirector()->set_url($_module.'/edit='.$_postId);
		});

		// if a query has error or any error occour in any part of codes, run roolback
		$this->rollback(function()
		{
			debug::title(T_("Transaction error").': ');
		} );
	}
}
?>