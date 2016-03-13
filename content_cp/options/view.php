<?php
namespace addons\content_cp\options;

class view extends \addons\content_cp\home\view
{
	public function config()
	{
		parent::config();
		$this->data->page['desc']     = T_('Edit your site general options');
		$this->data->page['haschild'] = false;
	}



	function view_datatable()
	{
		$form_general  = $this->createform('.options', 'general',  true);
		$form_social   = $this->createform('.options', 'social',   true);
		$form_twitter  = $this->createform('.options', 'twitter',  true);
		$form_facebook = $this->createform('.options', 'facebook', true);
		$form_telegram = $this->createform('.options', 'telegram', true);
		$form_sms      = $this->createform('.options', 'sms',      true);
		$form_register = $this->createform('.options', 'account',  true);


		// add sms items
		$form_sms->sms_name->child()->value('Kavenegar')->label(T_('Kavenegar'));

		// give perm list and fill it in default register type
		$myPermList  = $form_register->account_default;
		$myPermNames = $this->model()->permList();
		$myPerm      = 1;
		// get list of permissions
		foreach ($myPermNames as $key => $value)
		{
			if($myPerm == $key)
			{
				$myPermList->child()->value($key)->label(T_($value))->elname(null)->pl(null)->attr('type', null)->id('perm'.$key)->$checkStatus();
			}
			else
			{
				$myPermList->child()->value($key)->label(T_($value))->elname(null)->pl(null)->attr('type', null)->id('perm'.$key);
			}
		}

		// get the datatable of options
		$datatable     = $this->model()->draw_options();
		// $datatable['sms']['sms'] = null;

		// fill all forms used in options page


		if(isset($datatable['general']))
		{
			$this->form_fill($form_general,  $datatable['general']);
		}
		if(isset($datatable['social']))
		{
			$this->form_fill($form_social,   $datatable['social']);
		}
		if(isset($datatable['social']['twitter']['meta']))
		{
			$this->form_fill($form_twitter,  $datatable['social']['twitter']['meta']);
		}
		if(isset($datatable['social']['facebook']['meta']))
		{
			$this->form_fill($form_facebook, $datatable['social']['facebook']['meta']);
		}
		if(isset($datatable['social']['telegram']['meta']))
		{
			$this->form_fill($form_telegram, $datatable['social']['telegram']['meta']);
		}
		if(isset($datatable['sms']['sms']))
		{
			$this->form_fill($form_sms,      $datatable['sms']['sms']);
		}
		if(isset($datatable['account']['account']))
		{
			$this->form_fill($form_register, $datatable['account']['account']);
		}
	}
}
?>