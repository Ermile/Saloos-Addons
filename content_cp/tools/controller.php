<?php
namespace addons\content_cp\tools;

class controller extends \addons\content_cp\home\controller
{
	function _route()
	{
		// check permission to access to cp
		if(Tld !== 'dev')
		{
			parent::_permission('cp');
		}

		// // Restrict unwanted module
		// if(!$this->cpModlueList())
		// 	\lib\error::page(T_("Not found!"));
		$exist    = false;
		$mymodule = $this->cpModule('table');
		$cpModule = $this->cpModule('raw');

		// var_dump($this->child());
		$this->display_name	= 'content_cp/templates/raw.html';
		switch ($this->child())
		{
			case 'dbtables':
				$exist    = true;
				echo \lib\utility\dbTables::create();
				break;


			case 'db':

				\lib\db::$link_open    = [];
				\lib\db::$link_default = null;
				\lib\db::$db_user     = \lib\utility::post("username");
				\lib\db::$db_pass     = \lib\utility::post("password");
				\lib\db::$debug_error = false;

				$result = null;
				$exist  = true;

				if(\lib\utility::post('type') == 'upgrade')
				{

					// do upgrade
					$result = \lib\db::install(true, true);
				}
				elseif(\lib\utility::post('type') == 'backup')
				{
					$result = \lib\db::backup(true);
				}

				echo '<pre>';
				print_r($result);
				echo '</pre>';
				break;


			case 'twigtrans':
				$exist    = true;
				$mypath   = \lib\utility::get('path');
				$myupdate = \lib\utility::get('update');
				echo \lib\utility\twigTrans::extract($mypath, $myupdate);
				break;


			case 'phpinfo':
				$exist    = true;
				phpinfo();
				break;


			case 'server':
				$exist = true;
				if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && !class_exists("COM"))
				{
					ob_start();
					echo "<!DOCTYPE html><meta charset='UTF-8'/><title>Extract text form twig files</title><body style='padding:0 1%;margin:0 1%;direction:ltr;overflow:hidden'>";

					echo "<h1>". T_("First you need to enable COM on windows")."</h1>";
					echo "<a target='_blank' href='http://www.php.net/manual/en/class.com.php'>" . T_("Read More") . "</a>";
					break;
				}
				\lib\utility\tools::linfo();

				$this->display_name	= 'content_cp/templates/raw-all.html';

				break;


			case 'twitter':
				$a = \lib\utility\socialNetwork::twitter('hello! test #api');
				// var_dump($a);
				break;


			case 'mergefiles':
				$exist = true;
				echo \lib\utility\tools::mergefiles('merged-project.php');
				if(\lib\utility::get('type') === 'all')
				{
					echo \lib\utility\tools::mergefiles('merged-saloos-lib.php', core.lib);
					echo \lib\utility\tools::mergefiles('merged-saloos-cp.php', addons.'content_cp/');
					echo \lib\utility\tools::mergefiles('merged-saloos-account.php', addons.'content_account/');
					echo \lib\utility\tools::mergefiles('merged-saloos-includes.php', addons.'includes/');
				}
				break;


			case 'sitemap':
				$exist    = true;
				$site_url = \lib\router::get_storage('url_site');
				echo "<pre>";
				echo $site_url.'<br/>';
				$sitemap  = new \lib\utility\sitemap($site_url , root.'public_html/', 'sitemap' );
				// add posts
				foreach ($this->model()->sitemap('posts', 'post') as $row)
					$sitemap->addItem($row['post_url'], '0.8', 'daily', $row['post_publishdate']);

				// add pages
				foreach ($this->model()->sitemap('posts', 'page') as $row)
					$sitemap->addItem($row['post_url'], '0.6', 'weekly', $row['post_publishdate']);

				// add helps
				foreach ($this->model()->sitemap('posts', 'helps') as $row)
					$sitemap->addItem($row['post_url'], '0.3', 'monthly', $row['post_publishdate']);

				// add attachments
				foreach ($this->model()->sitemap('posts', 'attachment') as $row)
					$sitemap->addItem($row['post_url'], '0.2', 'weekly', $row['post_publishdate']);

				// add other type of post
				foreach ($this->model()->sitemap('posts', false) as $row)
					$sitemap->addItem($row['post_url'], '0.5', 'weekly', $row['post_publishdate']);

				// add cats and tags
				foreach ($this->model()->sitemap('terms') as $row)
					$sitemap->addItem($row['term_url'], '0.4', 'weekly', $row['date_modified']);

				$sitemap->createSitemapIndex();
				echo "</pre>";
				echo "<p class='alert alert-success'>". T_('Create sitemap Successfully!')."</p>";


				// echo "Create Successful";
				break;


			case 'git':
				// declare variables
				$exist    = true;
				$rep      = null;
				$result   = [];
				$location = '../../';
				$name     = \lib\utility::get('name');
				$output   = null;

				// switch by name of repository
				switch ($name)
				{
					case 'saloos':
						$location .= 'saloos';
						$rep      .= "https://github.com/Ermile/Saloos.git";
						break;

					case 'addons':
						$location .= 'saloos/saloos-addons';
						$rep      .= "https://github.com/Ermile/Saloos-Addons.git";
						break;

					default:
						$location .= $name;
						// $exist = false;
						// return;
						break;
				}
				// change location to address of requested
				chdir($location);
				// start show result
				$output   = "<pre>";
				$output  .= 'Repository address: '. getcwd(). '<br/>';
				$output  .= 'Remote address:     '. $location. '<hr/>';
				// $command  = 'git pull '.$rep.' 2>&1';
				$command  = 'git pull origin master 2>&1';


				// Print the exec output inside of a pre element
				exec($command, $result);
				if(!$result)
				{
					$output .= T_('Not Work!');
				}
				foreach ($result as $line)
				{
					$output .= $line . "\n";
				}
				$output .= "</pre>";

				echo $output;
				break;


			case 'log':
				$exist      = true;
				$output     = '<html>';
				$name       = \lib\utility::get('name');
				$page       = \lib\utility::get('p')*50000;
				if($page< 0)
				{
					$page = 0;
				}
				$lenght      = \lib\utility::get('lenght');
				if($lenght< 50000)
				{
					$lenght = 50000;
				}
				$filepath   = '';
				$fileFormat = 'sql';

				switch ($name)
				{
					case 'sql':
						$filepath = database.'log/log.sql';
						$lang     = 'sql';
						break;

					case 'sql_error':
						$filepath = database.'log/error.sql';
						$lang     = 'sql';
						break;

					default:
						$output .= 'Do you wanna something here!?';
						break;
				}
				// read file data
				$fileData = @file_get_contents($filepath, FILE_USE_INCLUDE_PATH, null, $page, $lenght);
				if($fileData)
				{
					$myURL    = Protocol."://". \lib\router::get_root_domain().'/static';
					$myCommon = Protocol."://ermile.".Tld.'/static/js/common.js';
					$output .= "<head>";
					$output .= ' <title>Log | '. $name. '</title>';
					$output .= ' <script src="'. $myCommon. '"></script>';
					$output .= ' <script src="'. $myURL. '/js/lib/highlight/highlight.min.js"></script>';
					$output .= ' <link rel="stylesheet" href="'. $myURL. '/css/lib/highlight/atom-one-dark.css">';
					$output .= ' <script>$(document).ready(function() {$("pre").each(function(i, block) {hljs.highlightBlock(block);}); });</script>';
					$output .= "</head>";
					$output .= "<pre class='$lang'>";
					$output .= $fileData;
					$output .= "</pre>";
				}
				else
				{
					$output .= 'File does not exist!';
				}

				$output .= "</body></html>";
				echo $output;
				break;


			case null:
				$mypath = $this->url('path','_');
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

		// $this->get()->ALL();
		if($exist)
		{
			$this->model()->_processor(object(array("force_json" => false, "force_stop" => true)));
		}

		return;


	}
}
?>