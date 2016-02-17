<?php
namespace content_cp\options;

use \lib\utility;
use \lib\debug;

class model extends \content_cp\home\model
{
	/**
	 * Update options data
	 * @return run update query and no return value
	 */
	function put_options()
	{
		$myFields = [
			'title'       => 'site-title',
			'desc'        => 'site-desc',
			'email'       => 'site-email',
			'url'         => 'site-url',
			'redirect'    => 'site-redirect',
			'register'    => 'site-reg',
			'permissions' => 'site-role'
		];

		foreach ($myFields as $field => $postName)
		{
			$qry = $this->sql()->table('options')
				->where('option_cat', 'options')
				->and('option_key', $field)
				->and('post_id', '#NULL')
				->and('user_id', '#NULL');

			$fieldExist = $qry->select()->num();
			// if exist more than 2 times remove all the properties
			if($fieldExist > 1)
			{
				debug::true(T_("We find a problem and solve it!"));
				$qry->delete();
				$fieldExist = 0;
			}

			$value = utility::post($postName);
			if(!$value)
				$value = '#""';

			$qry = $qry
				->set('option_cat', 'options')
				->set('option_status', 'enable')
				->set('option_key', $field)
				->set('option_value', $value);

			// if exist update field
			if($fieldExist == 1)
			{
				$qry->update();
			}
			// else if not exist insert this field to table
			else
			{
				$qry->insert('IGNORE');
			}
		}

		// exit();

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



	public function draw_options()
	{
		// $uid         = $this->login('id');
		$qry_result  = [];
		$qry_options = $this->sql()->table('options')
					->where('user_id', 'IS', 'NULL')
					->and('post_id', 'IS', "NULL")
					->and('option_cat', 'options')

					->groupOpen('g_status')
					->and('option_status', '=', "'enable'")
					->or('option_status', 'IS', "NULL")
					->or('option_status', "")
					->groupClose('g_status')
					->select()
					->allassoc();

		// get list of permissions
		$permList = $this->permList();
		$qry_result['permissions'] =
		[
			'value' => null,
			'meta'  => $permList
		];

		foreach ($qry_options as $key => $row)
		{
			if($row['option_key'] == 'permissions')
			{
				$myValue = $row['option_value'];
				$myMeta  = $permList;
			}
			else
			{
				$myValue = $row['option_value'];
				$myMeta  = $row['option_meta'];

				if(substr($myValue, 0,1) == '{')
				{
					$myValue = json_decode($myValue, true);
				}

				if(substr($myMeta, 0,1) == '{')
				{
					$myMeta = json_decode($myMeta, true);
				}
			}

			$qry_result[$row['option_key']] =
			[
				'value' => $myValue,
				'meta'  => $myMeta
			];
		}
		// var_dump($qry_result);
		return $qry_result;
	}
}
?>