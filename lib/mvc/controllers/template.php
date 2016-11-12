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
			if($this->option('socialy'))
			{
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
			if(isset($social_list[$social_name]) && $this->option($social_name))
			{
				// create url of social network
				$social_url = $social_list[$social_name] . $this->option($social_name);
				// redirect to new address
				$this->redirector($social_url, false)->redirect();
				return;
			}
		}

		$myurl =
		[
			'type'  => null,
			'slug'  => null,
			'table' => null
		];

		if(!empty(db_name))
		{
			$myurl = $this->model()->s_template_finder();
		}

		// set post type, get before underscope
		$post_type        = strtok($myurl['type'], '_');
		$route_check_true = false;
		// if url does not exist show 404 error
		if(!$myurl || ($myurl['table'] != 'terms' && \lib\router::get_storage("pagenation")))
		{
			// if user entered url contain one of our site language
			$current_path = $this->url('path', '_');
			// if custom template exist show this template
			if( is_file(root.'content/template/static_'. $current_path. '.html') )
			{
				$this->display_name = 'content\template\static_'. $current_path. '.html';
				$route_check_true   = true;
			}
			elseif( is_file(root.'content/template/static/'. $current_path. '.html') )
			{
				$this->display_name = 'content\template\static\\'. $current_path. '.html';
				$route_check_true   = true;
			}
			else
			{
				// create special url for handle special type of syntax
				// for example see below example
				// ermile.com/legal			 	-> content/template/legal/home.html
				// ermile.com/legal/privacy		-> content/template/legal/privacy.html
				$my_special_url = substr($current_path, strlen($mymodule)+1);
				if(!$my_special_url)
				{
					$my_special_url = 'home';
				}
				$my_special_url = $mymodule. '/'. $my_special_url;
				if(is_file(root.'content/template/static/'. $my_special_url. '.html'))
				{
					$this->display_name = 'content/template/static/'. $my_special_url. '.html';
					$route_check_true   = true;
				}
			}
			// // elseif 404 template exist show it
			// elseif( is_file(root.'content/template/404.html') )
			// {
			// 	header("HTTP/1.1 404 NOT FOUND");
			// 	$this->display_name	= 'content\template\404.html';
			// }
			// // else show saloos default error page
			// else
			// {
			// 	\lib\error::page(T_("Does not exist!"));
			// 	return;
			// }
		}

		// elseif template type exist show it
		elseif( is_file(root.'content/template/'.$post_type.'-'.$myurl['slug'].'.html') )
		{
			$this->display_name	= 'content\template\\'.$post_type.'-'.$myurl['slug'].'.html';
			$route_check_true = true;
		}
		// elseif template type exist show it
		elseif( is_file(root.'content/template/'.$post_type.'.html') )
		{
			$this->display_name	= 'content\template\\'.$post_type.'.html';
			$route_check_true = true;
		}

		// elseif template type exist show it
		elseif( is_file(root.'content/template/'.$myurl['table'].'.html') )
		{
			$this->display_name	= 'content\template\\'.$myurl['table'].'.html';
			$route_check_true = true;
		}

		// elseif default template exist show it else use homepage!
		elseif( is_file(root.'content/template/dafault.html') )
		{
			$this->display_name	= 'content\template\dafault.html';
			$route_check_true = true;
		}
		if($route_check_true)
		{
			$this->route_check_true = $route_check_true;
			$this->get(null, $myurl['table'])->ALL("/.*/");
		}
	}
}
?>