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
			->label(T_('Site title'))
			->maxlength(20)
			->pl(T_('Site title'))
			->desc(T_("For multilanguage sites enter title in English and translate it"));

		$this->desc = $this->make('text')->name('site-desc')
			->label(T_('Site Description'))
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
			->label(T_('Site main URL'))
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
		$this->twitter();
		$this->facebook();
	}


	/**
	 * Create twitter elements
	 * @return [type] [description]
	 */
	private function twitter()
	{
		$this->t_ConsumerKey = $this->make('text')
			->name('twitter-ConsumerKey')
			->label(T_('Twitter'). ' '. T_('ConsumerKey'))
			->maxlength(20);

		$this->t_ConsumerSecret = $this->make('text')
			->name('twitter-ConsumerSecret')
			->label(T_('Twitter'). ' '. T_('ConsumerSecret'))
			->maxlength(60);

		$this->t_AccessToken = $this->make('text')
			->name('twitter-AccessToken')
			->label(T_('Twitter'). ' '. T_('AccessToken'))
			->maxlength(60);

		$this->t_AccessTokenSecret = $this->make('text')
			->name('twitter-AccessTokenSecret')
			->label(T_('Twitter'). ' '. T_('AccessTokenSecret'))
			->maxlength(60);
	}


	private function facebook()
	{
		$this->fb_app_id = $this->make('number')
			->name('fb-app_id')
			->label(T_('Facebook'). ' '. T_('app_id'))
			->maxlength(20);

		$this->fb_app_secret = $this->make('text')
			->name('fb-app_secret')
			->label(T_('Facebook'). ' '. T_('app_secret'))
			->maxlength(40);

		$this->redirect_url = $this->make('url')
			->name('fb-redirect_url')
			->label(T_('Facebook'). ' '. T_('redirect_url'))
			->maxlength(90);

		$this->required_scope = $this->make('text')
			->name('fb-required_scope')
			->label(T_('Facebook'). ' '. T_('required_scope'))
			->maxlength(60);

		$this->page_id = $this->make('text')
			->name('fb-page_id')
			->label(T_('Facebook'). ' '. T_('page_id'))
			->maxlength(20);

		$this->access_token = $this->make('text')
			->name('fb-access_token')
			->label(T_('Facebook'). ' '. T_('access_token'))
			->maxlength(300);

		$this->client_token = $this->make('text')
			->name('fb-client_token')
			->label(T_('Facebook'). ' '. T_('client_token'))
			->maxlength(60);

	}
}
?>