<?php
namespace addons\includes\cls\form;

class options extends \lib\form
{
	public function __construct($function=null)
	{
		if ($function && method_exists($this, $function))
		{
			$this->$function();
		}
		else
		{
			// if(DEBUG)
			// 	var_dump('Please pass correct function name as parameter');
			return;
		}
	}

	/**
	 * Ermile CMS General Settings
	 * @return [type] [description]
	 */
	private function general()
	{
		$this->title = $this->make('text')->name('site-title')
			->label(T_('Site'). ' '. T_('title'))
			->maxlength(20)
			->pl(T_('Site title'))
			->desc(T_("For multilanguage sites enter title in English and translate it"));

		$this->desc = $this->make('text')->name('site-desc')
			->label(T_('Site'). ' '.T_('Description'))
			->maxlength(160)
			->pl(T_('Site Description'))
			->desc(T_("Explain site and porpose of it in a few words"));

		$this->email = $this->make('email')->name('site-email')
			->label(T_('Email'))
			->maxlength(50)
			->pl(T_('Email'))
			// ->desc(T_("Explain site and porpose of it in a few words"))
			;

		$this->url = $this->make('url')->name('site-url')
			->label(T_('Site'). ' '.T_('main URL'))
			->maxlength(50)
			->pl(T_('Site main Address (URL)'))
			->desc(T_("Explain site and porpose of it in a few words"));
	}


	/**
	 * Ermile Social Network Settings
	 * @return [type] [description]
	 */
	private function social()
	{
		$this->twitter = $this->make('text')
			->name('twitter')
			->label(T_('Twitter'))
			->attr('data-before','twitter.com/')
			->maxlength(30);

		$this->facebook = $this->make('text')
			->name('facebook')
			->label(T_('Facebook'))
			->attr('data-before','facebook.com/')
			->maxlength(60);

		$this->googleplus = $this->make('text')
			->name('googleplus')
			->label(T_('Google Plus'))
			->attr('data-before','plus.google.com/')
			->maxlength(60);

		$this->github = $this->make('text')
			->name('github')
			->label(T_('Github'))
			->attr('data-before','github.com/')
			->maxlength(60);

		$this->linkedin = $this->make('text')
			->name('linkedin')
			->label(T_('Linkedin'))
			->attr('data-before','linkedin.com/in/')
			->maxlength(60);

		$this->telegram = $this->make('text')
			->name('telegram')
			->label(T_('Telegram'))
			->attr('data-before','telegram.me/')
			->maxlength(60);

		$this->aparat = $this->make('text')
			->name('aparat')
			->label(T_('Aparat'))
			->attr('data-before','aparat.com/')
			->maxlength(60);
	}


	/**
	 * Create twitter elements
	 * @return [type] [description]
	 */
	private function twitter()
	{
		$this->twitter_status = $this->make('checkbox')
			->name('twitter-status')
			->class('checkbox')
			->label(T_('Status of twitter sharing'));

		$this->twitter_ConsumerKey = $this->make('text')
			->attr('data-parent', 'twitter-status')
			->name('twitter-ConsumerKey')
			->label(T_('Twitter'). ' '. T_('ConsumerKey'))
			->maxlength(20);

		$this->twitter_ConsumerSecret = $this->make('text')
			->attr('data-parent', 'twitter-status')
			->name('twitter-ConsumerSecret')
			->label(T_('Twitter'). ' '. T_('ConsumerSecret'))
			->maxlength(60);

		$this->twitter_AccessToken = $this->make('text')
			->attr('data-parent', 'twitter-status')
			->name('twitter-AccessToken')
			->label(T_('Twitter'). ' '. T_('AccessToken'))
			->maxlength(60);

		$this->twitter_AccessTokenSecret = $this->make('text')
			->attr('data-parent', 'twitter-status')
			->name('twitter-AccessTokenSecret')
			->label(T_('Twitter'). ' '. T_('AccessTokenSecret'))
			->maxlength(60);
	}


	/**
	 * Create facebook elements
	 * @return [type] [description]
	 */
	private function facebook()
	{
		$this->fb_status = $this->make('checkbox')
			->name('fb-status')
			->class('checkbox')
			->label(T_('Status of facebook sharing'));

		$this->fb_app_id = $this->make('number')
			->attr('data-parent', 'fb-status')
			->name('fb-app_id')
			->label(T_('Facebook'). ' '. T_('app_id'))
			->maxlength(20);

		$this->fb_app_secret = $this->make('text')
			->attr('data-parent', 'fb-status')
			->name('fb-app_secret')
			->label(T_('Facebook'). ' '. T_('app_secret'))
			->maxlength(40);

		$this->fb_redirect_url = $this->make('url')
			->attr('data-parent', 'fb-status')
			->name('fb-redirect_url')
			->label(T_('Facebook'). ' '. T_('redirect_url'))
			->maxlength(90);

		$this->fb_required_scope = $this->make('text')
			->attr('data-parent', 'fb-status')
			->name('fb-required_scope')
			->label(T_('Facebook'). ' '. T_('required_scope'))
			->maxlength(60);

		$this->fb_page_id = $this->make('text')
			->attr('data-parent', 'fb-status')
			->name('fb-page_id')
			->label(T_('Facebook'). ' '. T_('page_id'))
			->maxlength(20);

		$this->fb_access_token = $this->make('text')
			->attr('data-parent', 'fb-status')
			->name('fb-access_token')
			->label(T_('Facebook'). ' '. T_('access_token'))
			->maxlength(300);

		$this->fb_client_token = $this->make('text')
			->attr('data-parent', 'fb-status')
			->name('fb-client_token')
			->label(T_('Facebook'). ' '. T_('client_token'))
			->maxlength(60);
	}


	/**
	 * Create telegram elements
	 * @return [type] [description]
	 */
	private function telegram()
	{
		$this->tg_status = $this->make('checkbox')
			->name('tg-status')
			->class('checkbox')
			->label(T_('Status of telegram sharing'));

		$this->tg_key = $this->make('text')
			->attr('data-parent', 'tg-status')
			->name('tg-key')
			->label(T_('Telegram'). ' '. T_('Key'))
			->maxlength(200);
	}


	/**
	 * Create sms elements
	 * @return [type] [description]
	 */
	private function sms()
	{
		$this->sms_status = $this->make('checkbox')
			->name('sms-status')
			->class('checkbox')
			->label(T_('Status of sms service'));

		$this->sms_debug = $this->make('checkbox')
			->attr('data-parent', 'sms-status')
			->name('sms-debug')
			->class('checkbox')
			->label(T_('Simulate SMS (Debugging)'));

		$this->sms_seperator1 = $this->make('seperator')
			->label(T_('SMS api detail'));

		$this->sms_name = $this->make('select')
			->attr('data-parent', 'sms-status')
			->name('sms-name')
			->label(T_('SMS service'))
			->pl(T_('SMS service'));

		$this->sms_apikey = $this->make('text')
			->attr('data-parent', 'sms-status')
			->name('sms-apikey')
			->label(T_('SMS'). ' '. T_('apikey'))
			->maxlength(100);

		$this->sms_line1 = $this->make('number')
			->attr('data-parent', 'sms-status')
			->name('sms-line1')
			->label(T_('SMS'). ' '. T_('line number'). ' 1')
			->maxlength(20);

		$this->sms_line2 = $this->make('number')
			->attr('data-parent', 'sms-status')
			->name('sms-line2')
			->label(T_('SMS'). ' '. T_('line number'). ' 2')
			->maxlength(20);

		$this->sms_iran = $this->make('checkbox')
			->attr('data-parent', 'sms-status')
			->name('sms-iran')
			->class('checkbox')
			->label(T_('Regional restriction'));

		$this->sms_seperator2 = $this->make('seperator')
			->label(T_('Message detail'));

		$this->sms_header = $this->make('text')
			->attr('data-parent', 'sms-status')
			->name('sms-header')
			->label(T_('Message header'))
			->maxlength(20);

		$this->sms_footer = $this->make('text')
			->attr('data-parent', 'sms-status')
			->name('sms-footer')
			->label(T_('Message footer'))
			->maxlength(20);

		$this->sms_one = $this->make('checkbox')
			->attr('data-parent', 'sms-status')
			->name('sms-one')
			->class('checkbox')
			->label(T_('Force one message'));

		$this->sms_seperator3 = $this->make('seperator')
			->label(T_('Send message in custom situation'));

		$this->sms_signup = $this->make('checkbox')
			->attr('data-parent', 'sms-status')
			->name('sms-signup')
			->class('checkbox')
			->label(T_('Send message for'). ' '. T_('signup'));

		$this->sms_verification = $this->make('checkbox')
			->attr('data-parent', 'sms-status')
			->name('sms-verification')
			->class('checkbox')
			->label(T_('Send message for'). ' '. T_('verification'));

		$this->sms_recovery = $this->make('checkbox')
			->attr('data-parent', 'sms-status')
			->name('sms-recovery')
			->class('checkbox')
			->label(T_('Send message for'). ' '. T_('recovery'));

		$this->sms_changepass = $this->make('checkbox')
			->attr('data-parent', 'sms-status')
			->name('sms-changepass')
			->class('checkbox')
			->label(T_('Send message for'). ' '. T_('changepass'));

		$this->sms_verification = $this->make('checkbox')
			->attr('data-parent', 'sms-status')
			->name('sms-verification')
			->class('checkbox')
			->label(T_('Send message for'). ' '. T_('verification'));
	}


	/**
	 * Create account elements
	 * @return [type] [description]
	 */
	private function account()
	{
		$this->account_status = $this->make('checkbox')
			->name('account-status')
			->class('checkbox')
			->label(T_('Account Status'));

		$this->account_redirect = $this->make('checkbox')
			->attr('data-parent', 'account-status')
			->name('account-redirect')
			->class('checkbox')
			->label(T_('Redirect to main address'));

		$this->account_seperator1 = $this->make('seperator')
			->attr('data-parent', 'account-status')
			->label(T_('Signup Settings'));

		$this->account_default = $this->make('select')
			->attr('data-parent', 'account-status')
			->name('account-default')
			->label(T_('Default permission'))
			->pl(T_('Default permission'));

		$this->account_seperator2 = $this->make('seperator')
			->attr('data-parent', 'account-status')
			->label(T_('Increase account security'));

		$this->account_passphrase = $this->make('checkbox')
			->attr('data-parent', 'account-status')
			->name('account-passphrase')
			->class('checkbox')
			->label(T_('Access with pass phrase'));

		$this->account_passkey = $this->make('text')
			->attr('data-parent', 'account-status account-passphrase' )
			->name('account-passkey')
			->label(T_('Pass phrase key'))
			->maxlength(20);

		$this->account_passvalue = $this->make('text')
			->attr('data-parent', 'account-status account-passphrase' )
			->name('account-passvalue')
			->label(T_('Pass phrase value'))
			->maxlength(20);

		$this->account_seperator3 = $this->make('seperator')
			->attr('data-parent', 'account-status')
			->label(T_('Status of account service'));

		$this->account_register = $this->make('checkbox')
			->attr('data-parent', 'account-status')
			->name('account-register')
			->class('checkbox')
			->label(T_('Allow registration'));

		$this->account_recovery = $this->make('checkbox')
			->attr('data-parent', 'account-status')
			->name('account-recovery')
			->class('checkbox')
			// ->attr('data-relation', 'account-passphrase' )
			->label(T_('Allow recovery account'));
	}
}
?>