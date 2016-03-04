<?php
namespace addons\content_cp\tools;

class controller extends \addons\content_cp\home\controller
{
	function _route()
	{
		// if(!$this->login() && Tld !== 'dev')
		// {
		// 	$mydomain = AccountService? AccountService.MainTld: null;
		// 	\lib\debug::warn(T_("first of all, you must login to system!"));
		// 	$this->redirector(null, false)->set_domain($mydomain)->set_url('login')->redirect();
		// 	exit();
		// }

		// // Restrict unwanted module
		// if(!$this->cpModlueList())
		// 	\lib\error::page(T_("Not found!"));

		$mymodule = $this->cpModule('table');
		$cpModule = $this->cpModule('raw');

		// var_dump($this->child());
		$this->display_name	= 'content_cp/templates/raw.html';
		switch ($this->child())
		{
			case 'dbtables':
				\lib\utility\dbTables::create();
				exit();
				break;

			case 'twigtrans':
				\lib\utility\twigTrans::extract(\lib\utility::get('path'));
				exit();
				break;

			case 'server':
				if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && !class_exists("COM"))
				{
					ob_start();
					echo "<!DOCTYPE html><meta charset='UTF-8'/><title>Extract text form twig files</title><body style='padding:0 1%;margin:0 1%;direction:ltr;overflow:hidden'>";

					echo "<h1>". T_("First you need to enable COM on windows")."</h1>";
					echo "<a target='_blank' href='http://www.php.net/manual/en/class.com.php'>" . T_("Read More") . "</a>";
					break;
				}
				\lib\utility\Linfo::show();
				exit();

				$this->display_name	= 'content_cp/templates/raw-all.html';

				break;

			case 'twitter':
				$a = \lib\utility\SocialNetwork::twitter('hello! test #api');
				var_dump($a);
				break;

			case 'sitemap':
				$site_url = \lib\router::get_storage('url_site');
				$sitemap = new \lib\utility\Sitemap($site_url , root.'public_html/', 'sitemap' );
				// echo "<pre>";

				// add posts
				foreach ($this->model()->sitemap('posts', 'post') as $row)
					$sitemap->addItem($row['post_url'], '0.8', 'daily', $row['post_publishdate']);

				// add pages
				foreach ($this->model()->sitemap('posts', 'page') as $row)
					$sitemap->addItem($row['post_url'], '0.6', 'weekly', $row['post_publishdate']);

				// add attachments
				foreach ($this->model()->sitemap('posts', 'attachment') as $row)
					$sitemap->addItem($row['post_url'], '0.2', 'weekly', $row['post_publishdate']);

				// add books
				foreach ($this->model()->sitemap('posts', 'book') as $row)
					$sitemap->addItem($row['post_url'], '0.6', 'yearly', $row['post_publishdate']);

				// add cats and tags
				foreach ($this->model()->sitemap('terms') as $row)
					$sitemap->addItem($row['term_url'], '0.4', 'weekly', $row['date_modified']);

				$sitemap->createSitemapIndex();
				echo "<p class='alert alert-success'>Create sitemap Successfully!</p>";


				// echo "Create Successful";
				break;

			case 'git':
				echo shell_exec("/".Domain." git pull");
				// exec('git pull');
				break;

			case null:
				$mypath   = $this->url('path','_');
				if( is_file(addons.'content_cp/templates/static_'.$mypath.'.html') )
				{
					$this->display_name	= 'content_cp/templates/static_'.$mypath.'.html';
				}
				// $this->display_name	= 'content_cp/templates/static_'.$mypath.'.html';
				break;

			default:
				$this->display_name	= 'content_cp/templates/static_tools.html';

				return;
				break;
		}

		$this->get()->ALL();
		return;


	}
}
?>