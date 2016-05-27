<?php
namespace lib\mvc\controllers;

trait template
{
	/**
	 * [s_template_finder description]
	 * @return [type] [description]
	 */
	function s_template_finder()
	{
		// if lang exist in module or subdomain remove it and continue
		$currentLang = substr(\lib\router::get_storage('language'), 0, 2);
		$defaultLang = substr(\lib\router::get_storage('defaultLanguage'), 0, 2);

		if($currentLang === SubDomain && $currentLang !== $defaultLang)
		{
			\lib\router::set_sub_domain(null);
		}
		// elseif($currentLang === $this->module() && $currentLang !== $defaultLang)
		// 	\lib\router::remove_url($currentLang);



		// continue find best template for this condition
		$mymodule    = $this->module();
		if($mymodule == 'home')
		{
			// if home template exist show it
			if( is_file(root.'content/template/home.html') )
				$this->display_name	= 'content\template\home.html';
			$this->get()->ALL();
			return 0;
		}


		elseif($mymodule == 'search')
		{
			if( is_file(root.'content/template/search.html') )
				$this->display_name	= 'content\template\search.html';

			$this->get()->ALL();
			return;
		}


		elseif($mymodule == 'feed')
		{
			$site_title    = $this->view()->data->site['title'];
			$site_desc     = $this->view()->data->site['desc'];
			$site_protocol = $this->url('MainProtocol'). '://';
			$site_url      = $this->url('MainSite');

			$rss = new \lib\utility\RSS($site_protocol, $site_url, $site_title, $site_desc);
			// add posts
			foreach ($this->model()->get_feeds() as $row)
				$rss->addItem($row['link'], $row['title'], $row['desc'], $row['date']);

			$rss->create();

			// \lib\utility\RSS::create();
			// exit();
			return;
		}
		else
		{
			// save name of current module as name of social
			$social_name = $mymodule;
			// declare list of shortkey for socials
			$social_list =
			[
				'@'        => 'twitter',
				'~'        => 'github',
				'+'        => 'googleplus',
				'f'        => 'facebook',
				'fb'       => 'facebook',
				'in'       => 'linkedin',
				'tg'       => 'telegram',
			];

			// if name of current module is exist then save complete name of it
			if(isset($social_list[$mymodule]))
			{
				$social_name = $social_list[$mymodule];
			}

			// declare address of social networks
			$social_list =
			[
				'twitter'    => 'https://twitter.com/',
				'github'     => 'https://github.com/',
				'googleplus' => 'https://plus.google.com/',
				'facebook'   => 'https://www.facebook.com/',
				'linkedin'   => 'https://linkedin.com/in/',
				'telegram'   => 'http://telegram.me/',
				'aparat'     => 'http://www.aparat.com/',
			];

			// if social name exist in social adresses then redirect to it
			if(isset($social_list[$social_name]))
			{
				// create url of social network
				$social_url = $social_list[$social_name] . $this->option($social_name);
				// redirect to new address
				$this->redirector($social_url, false)->redirect();
				return;
			}
		}

		$myurl = null;
		if(!empty(db_name))
		{
			$url   = self::checkShortURL();
			$myurl = $this->model()->s_template_finder($url);
		}
		else
		{
			$myurl = null;
		}

		// if url does not exist show 404 error
		if(!$myurl)
		{
			// var_dump($mymodule);
			// var_dump(\lib\router::get_storage('language'));
			// if user entered url contain one of our site language

			$currentPath = $this->url('path', '_');
			// if custom template exist show this template
			if( is_file(root.'content/template/static_'. $currentPath. '.html') )
			{
				$this->display_name	= 'content\template\static_'. $currentPath. '.html';
			}
			// elseif 404 template exist show it
			elseif( is_file(root.'content/template/404.html') )
			{
				header("HTTP/1.1 404 NOT FOUND");
				$this->display_name	= 'content\template\404.html';
			}
			// else show saloos default error page
			else
			{
				\lib\error::page(T_("Does not exist!"));
				return;
			}
		}

		// elseif template type exist show it
		elseif( is_file(root.'content/template/'.$myurl['type'].'-'.$myurl['slug'].'.html') )
			$this->display_name	= 'content\template\\'.$myurl['type'].'-'.$myurl['slug'].'.html';

		// elseif template type exist show it
		elseif( is_file(root.'content/template/'.$myurl['type'].'.html') )
			$this->display_name	= 'content\template\\'.$myurl['type'].'.html';

		// elseif template type exist show it
		elseif( is_file(root.'content/template/'.$myurl['table'].'.html') )
			$this->display_name	= 'content\template\\'.$myurl['table'].'.html';

		// elseif default template exist show it else use homepage!
		elseif( is_file(root.'content/template/dafault.html') )
			$this->display_name	= 'content\template\dafault.html';

		$this->route_check_true = true;
		$this->get(null, $myurl['table'])->ALL("/.*/");
	}

	/**
	 * [find_url_from_shortURL description]
	 * @param  [type] $_shortURL [description]
	 * @return [type]            [description]
	 */
	private function checkShortURL($_shortURL = null)
	{
		// set this shorturl, real url:)
		if(!\lib\utility\option::get('config', 'meta', 'shortURL'))
		{
			return null;
		}
		if(!$_shortURL)
		{
			$_shortURL = $this->url('path');
		}
		$table = null;
		$field = null;
		$urlPrefix = substr($_shortURL, 0, 3);
		switch ($urlPrefix)
		{
			case 'sp_':
				// if this is url of one post
				$table = 'post';
				break;

			case 'st_':
				// else if this is url of one term
				$table = 'term';
				break;
		}
		// if prefix is not correct return false
		if(!$table)
		{
			return null;
		}
		// remove prefix from url
		$_shortURL = substr($_shortURL, 3);
		$id        = \lib\utility\shortURL::decode($_shortURL);
		$field     = $table.'_url';
		$table     .= 's';

		$qry       = "SELECT $field as url FROM $table WHERE id = $id";
		$result    = \lib\db::get($qry, 'url', true);
		if(!is_string($result))
		{
			return false;
		}

		if(!\lib\utility\option::get('config', 'meta', 'forceShortURL'))
		{
			// redirect to url of this post
			$this->redirector()->set_url($result)->redirect();
		}
		// if not force simulate this url
		return $result;
	}
}
?>