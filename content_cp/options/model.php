<?php
namespace addons\content_cp\options;

use \lib\utility;
use \lib\debug;

class model extends \addons\content_cp\home\model
{
	/**
	 * Update options data
	 * @return run update query and no return value
	 */
	function put_options()
	{
		foreach ($this->getOptions() as $group => $record)
		{
			foreach ($record as $field => $value)
			{
				// var_dump($field);
				$meta   = null;
				$status = 'enable';
				$qry    = $this->sql()->table('options')
					->where('option_cat', 'option_'.$group)
					->and('option_key',   $field)
					->and('post_id',      '#NULL')
					->and('user_id',      '#NULL');
				$fieldExist = $qry->select()->num();
				// if exist more than 2 times remove all the properties
				if($fieldExist > 1)
				{
					debug::true(T_("We find a problem and solve it!"));
					$qry->delete();
					$fieldExist = 0;
				}

				// for array seperate it intro value and meta and encode it
				if(is_array($value))
				{
					// set meta values
					if(isset($value['meta']))
					{
						$meta   = json_encode($value['meta'], JSON_FORCE_OBJECT | JSON_HEX_QUOT | JSON_HEX_APOS );
						// $meta   = $value['meta'];
					}
					// set status if exist
					if(array_key_exists('status', $value))
					{
						$status = $value['status']? 'enable': 'disable';
					}
					// set value
					if(array_key_exists('value', $value))
					{
						$value  = $value['value'];
					}
					else
					{
						$value = null;
					}
				}
				// if value is empty set it empty
				if(!$value)
					$value = '#""';

				$qry = $qry
					->set('option_cat',    'option_'.$group)
					->set('option_status', $status)
					->set('option_key',    $field)
					->set('option_value',  $value);

				// if meta is not empty then add it to insert query
				if(isset($meta) && $meta !== '""')
				{
					$qry = $qry->set('option_meta', $meta);
				}

				// if exist update field
				if($fieldExist == 1)
				{
					// var_dump($qry->updateString());
					$qry->update();
				}
				// else if not exist insert this field to table
				else
				{
					$qry->insert('IGNORE');
				}
			}
		}


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


	/**
	 * get post variables and fill it in array
	 * @return [array] contain list of all data entered
	 */
	private function getOptions()
	{
		$myOptions =
		[
			'general' =>
			[
				'title'       => utility::post('site-title'),
				'desc'        => utility::post('site-desc'),
				'email'       => utility::post('site-email'),
				'url'         => utility::post('site-url)'),
			],
			'social'  =>
			[
				'twitter'     =>
				[
					'value' => utility::post('twitter'),
					'meta'  =>
					[
						'ConsumerKey'       => utility::post('twitter-ConsumerKey'),
						'ConsumerSecret'    => utility::post('twitter-ConsumerSecret'),
						'AccessToken'       => utility::post('twitter-AccessToken'),
						'AccessTokenSecret' => utility::post('twitter-AccessTokenSecret')
					]
				],
				'facebook'    =>
				[
					'value' => utility::post('facebook'),
					'meta'  =>
					[
						'app_id'         => utility::post('fb-app_id'),
						'app_secret'     => utility::post('fb-app_secret'),
						'redirect_url'   => utility::post('fb-redirect_url'),
						'required_scope' => utility::post('fb-required_scope'),
						'page_id'        => utility::post('fb-page_id'),
						'access_token'   => utility::post('fb-access_token'),
						'client_token'   => utility::post('fb-client_token')
					]
				],
				'googleplus'  =>
				[
					'value' => utility::post('googleplus'),
					'meta'  => ''
				],
				'github'      =>
				[
					'value' => utility::post('github'),
					'meta'  => ''
				],
				'linkedin'    =>
				[
					'value' => utility::post('linkedin'),
					'meta'  => ''
				],
				'telegram'    =>
				[
					'value' => utility::post('telegram'),
					'meta'  =>
					[
						'key' => utility::post('tg-key'),
					]
				],
				'aparat'      =>
				[
					'value' => utility::post('aparat'),
					'meta'  => ''
				],
				'sms' =>
				[
					'status' => utility::post('sms-status'),
					'value'  => utility::post('sms-name'),
					'meta'   =>
					[
						'apikey'       => utility::post('sms-apikey'),
						'debug'        => utility::post('sms-debug'),
						'line1'        => utility::post('sms-line1'),
						'line2'        => utility::post('sms-line2'),
						'iran'         => utility::post('sms-iran'),
						'header'       => utility::post('sms-header'),
						'footer'       => utility::post('sms-footer'),
						'one'          => utility::post('sms-one'),
						'signup'       => utility::post('sms-signup'),
						'verification' => utility::post('sms-verification'),
						'recovery'     => utility::post('sms-recovery'),
						'changepass'   => utility::post('sms-changepass'),
					]
				],
			],
			'account' =>
			[
				'register'    =>
				[
					'status' => utility::post('register-status'),
					'value'  => utility::post('register-default'),
					'meta'   =>
					[
						'redirect' => utility::post('register-redirect'),
					]
				]
			],
		];

		return $myOptions;
	}


	/**
	 * draw options fields
	 * @param  [type] $_key [description]
	 * @return [type]       [description]
	 */
	public function draw_options($_key = null)
	{
		// get options
		$qry = $this->sql()->table('options')
			->where('option_cat', 'like', "'option%'")
			->and('post_id',      '#NULL')
			->and('user_id',      '#NULL');

		// change options to special array to fill it
		$result    = [];
		$datatable = $qry->select()->allassoc();
		foreach ($datatable as $datarow)
		{
			$cat    = $datarow['option_cat'];
			$cat    = substr($cat, 7);
			$key    = $datarow['option_key'];
			$status = $datarow['option_status'];
			$value  = $datarow['option_value'];
			$meta   = $datarow['option_meta'];
			$meta   = json_decode($meta, true);

			if(isset($meta))
			{
				$result[$cat][$key]['status'] = $status;
				$result[$cat][$key]['value']  = $value;
				$result[$cat][$key]['meta']   = $meta;
			}
			elseif(isset($value) && $value === '-status-')
			{
				$result[$cat][$key] = $status;
			}
			else
			{
				$result[$cat][$key] = $value;
			}
		}

		return $result;
	}
}
?>