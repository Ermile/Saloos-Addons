<?php
namespace addons\content_cp\home;

class view extends \mvc\view
{
	public function config()
	{
		// $this->data->list             = $this->cpModlueList('all');
		$this->data->bodyclass        = 'fixed unselectable';
		$this->include->css           = false;
		$this->include->js            = false;
		$this->include->fontawesome   = true;
		$this->include->datatable     = true;
		$this->include->chart         = true;
		$this->include->introjs       = true;
		$this->include->lightbox      = true;
		$this->include->editor        = true;
		$this->include->cp            = true;
		$this->include->uploader      = true;
		$this->global->js             = array();

		$this->data->feature['posts']              = true;
		$this->data->feature['pages']              = true;
		$this->data->feature['attachments']        = true;
		$this->data->feature['users']              = true;
		$this->data->feature['book']               = false;
		$this->data->feature['visitors']           = false;
		$this->data->feature['socialnetworks']     = false;
		$this->data->feature['permissions']        = true;
		$this->data->feature['options']            = [];
		$this->data->feature['options']['status']  = true;
		$this->data->feature['options']['general'] = true;
		$this->data->feature['options']['config']  = true;
		$this->data->feature['options']['sms']     = true;
		$this->data->feature['options']['social']  = true;
		$this->data->feature['options']['account'] = true;
		$this->data->feature['tools']              = true;
		$this->data->feature['tags']               = true;
		$this->data->feature['categories']         = true;

		$this->data->display['cp_posts'] = "content_cp/posts/layout.html";;


		$this->data->saloos['version']    = \lib\saloos::getLastVersion();
		$this->data->saloos['lastUpdate'] = \lib\saloos::getLastUpdate();
		$this->data->saloos['langlist']   = ['fa_IR' => 'Persian - فارسی',
											 'en_US' => 'English',
											 'ar_SU' => 'Arabic - العربية'];


		// $this->global->js             = [$this->url->myStatic.'js/highcharts/highcharts.js'];
		// $this->data->page['desc']  = 'salam';
		$this->data->page['haschild'] = true;
		$this->data->page['title']    = T_(ucfirst(\lib\router::get_url(' ')));

		$this->data->cpModule         = $this->cpModule();

		$this->data->dir['right']     = $this->global->direction == 'rtl'? 'left':  'right';
		$this->data->dir['left']      = $this->global->direction == 'rtl'? 'right': 'left';

		$mymodule = $this->module();
		switch ($mymodule)
		{
			case 'tags':
				$this->data->page['desc']     = T_('Assign keywords to your posts using tags');
				break;

			case 'categories':
				$this->data->page['desc']     = T_('Use categories to define sections of your site and group related posts');
				$this->data->page['title']    = T_('Categories');
				break;

			case 'filecategories':
				$this->data->page['desc']     = T_('Use categories to define sections of your site and group related files');
				$this->data->page['title']    = T_('File Categories');
				break;

			case 'bookcategories':
				$this->data->page['desc']     = T_('Use categories to define sections of your site and group related books');
				$this->data->page['title']    = T_('Book Categories');
				break;

			case 'books':
				$this->data->page['desc']     = T_('Use book to define important parts to use in posts');
				$this->data->page['title']    = T_('books');
				break;

			case 'posts':
				$this->data->page['desc']     = T_('Use posts to share your news in specefic category');
				break;

			case 'pages':
				$this->data->page['desc']     = T_('Use pages to share your static content');
				break;

			case 'attachments':
				$this->data->page['desc']     = T_('Upload your media');
				break;

			case 'socialnetwork':
				$this->data->page['desc']     = T_('Publish new post in social networks');
				break;

			case 'visitors':
				$this->data->page['haschild'] = false;
				if(\lib\utility\option::get('config', 'meta', 'logVisitors'))
				{
					// create for chart
					$type  = \lib\utility::get('type');
					$utype = \lib\utility::get('utype');
					$stype = \lib\utility::get('stype');
					$this->data->chart_type             = $type?  $type:  'column';
					$this->data->chart_unique_type      = $utype? $utype: 'areaspline';
					$this->data->chart_signup_type      = $stype? $stype: 'areaspline';

					// $this->data->visitors               = $this->model()->visitors();
					// $this->data->visitors_unique        = $this->model()->visitors(true);

					$this->data->visitors               = \lib\utility\visitor::chart();
					$this->data->visitors_unique        = \lib\utility\visitor::chart(true);

					// get period of signup from user
					$this->data->period = \lib\utility::get('period');
					switch ($this->data->period)
					{
						case 'year':
							$period = "%Y";
							break;

						case 'month':
							$period = "%Y-%m";
							break;

						case 'week':
							$period = "%Y ". T_('week')."%V";
							break;

						case 'day':
						default:
							$period = "%Y-%m-%d";
							break;
					}
					$this->data->signup                 = \lib\db\stat_users::signup($period);

					if($this->data->visitors <= 1)
					{
						$this->data->error = T_("Chart must be contain at least 2 column!");
					}
				}
				break;

			case 'home':
				$this->data->page['title']          = T_('Dashboard');

				$this->data->countOf['posts']       = $this->model()->countOf('posts');
				$this->data->countOf['pages']       = $this->model()->countOf('pages');
				$this->data->countOf['attachments'] = $this->model()->countOf('attachments');
				$this->data->countOf['books']       = $this->model()->countOf('books');
				$this->data->countOf['tags']        = $this->model()->countOf('tags');
				$this->data->countOf['categories']  = $this->model()->countOf('categories');
				$this->data->countOf['users']       = $this->model()->countOf('users');

				$this->data->bodyclass              .= ' unselectable';
				// check visitor is new or not
				$this->data->visitor_new            = false;
				$ref = \lib\router::urlParser('referer', 'sub');
				if($ref !== 'cp' && $ref !== null)
					$this->data->visitor_new = true;

				if(\lib\utility\option::get('config', 'meta', 'logVisitors'))
				{
					// create for chart
					$this->data->chart_type             = 'column';
					$this->data->visitors               = \lib\utility\visitor::chart();
					$this->data->visitors_toppages      = \lib\utility\visitor::top_pages(15);

					if($this->data->visitors <= 1)
					{
						$this->data->error = T_("Chart must be contain at least 2 column!");
					}
				}

				break;

			default:
				# code...
				break;
		}

		if($this->data->page['haschild'])
		{
			// Check permission and if user can do this operation
			// allow to do it, else show related message in notify center
			$myResult = $this->access('cp', $mymodule, 'add');
			$this->data->page['haschild'] = $myResult? true: false;
		}
		// $this->data->site['title']  = T_('Control Panel'). ' - ' . $this->data->site['title'];
	}

	function view_datatable()
	{
		// in root page like site.com/admin/banks show datatable
		// get data from database through model
		switch ($this->module())
		{
			case 'visitors':
				break;
			case 'profile':
				$this->data->datarow = $this->model()->datarow('users', $this->login('id'));
				break;

			default:
				$this->data->datatable = $this->model()->datatable();
				break;
		}
	}


	public function view_child()
	{
		$mytable                = $this->cpModule('table');
		$mychild                = $this->child();
		// $this->global->js       = array($this->url->myStatic.'js/cp/medium-editor.min.js');
		$this->data->enum       = \lib\sql\getTable::enumValues('posts');

		switch ($mytable)
		{
			case 'posts':
				// show list of tags
				$this->data->tagList = $this->model()->sp_term_list();

				// for each type of post
				switch ($this->cpModule('raw'))
				{
					case 'pages':
						$this->data->parentList = $this->model()->sp_parent_list();
						break;

					case 'polls':
						$this->data->parentList = $this->model()->sp_parent_list(true, 'poll');
						$this->data->catList = $this->model()->sp_cats('cat_poll');

						break;

					case 'attachments':
						$this->data->maxSize = \lib\utility\upload::max_file_upload_in_bytes();
						// $this->include->uploader      = true;
						// array_push($this->global->js, $this->url->myStatic.'js/cp/uploader.js');
						$this->data->catList = $this->model()->sp_cats('filecat');
						$this->data->catListSelected = $this->model()->sp_cats('filecat', true);
						break;

					case 'books':
						$this->data->catList = $this->model()->sp_cats('bookcat');
						$this->data->catListSelected = $this->model()->sp_cats('bookcat', true);
						$this->data->parentList = $this->model()->sp_parent_list(true, 'book');

						break;

					case 'socialnetwork':
						$this->data->catList = null;

						break;

					default:
						$this->data->catList = $this->model()->sp_cats();
						break;
				}
				break;

			default:
				switch ($this->cpModule('raw'))
				{
					case 'categories':
					case 'pollcategories':
					case 'filecategories':
					case 'bookcategories':
						$this->data->parentList = $this->model()->sp_category_list($this->cpModule('type'));
						break;
				}

				$this->data->field_list = \lib\sql\getTable::get($mytable);
				$myform = $this->createform('@'.db_name.'.'.$mytable, $this->data->child);
				break;
		}

		// if module for users then fill permission list
		if($this->cpModule('raw') === 'users')
		{
			$this->draw_users();
		}


		if($mychild === 'edit')
		{
			$this->data->datarow = $this->model()->datarow($mytable, null, true);

			if(isset($this->data->datarow['post_meta']))
				$this->data->datarow['post_meta'] = json_decode($this->data->datarow['post_meta'], true);

			if($this->cpModule('raw') === 'attachments')
			{
				if(isset($this->data->datarow['meta']['slug']))
					$this->data->datarow['post_slug'] = $this->data->datarow['meta']['slug'];
			}


			if($mytable === 'posts')
			{
				// $this->data->datarow['post_content'] .= '<img src="/static/images/logo.png" />';
				// var_dump($this->data->datarow['post_content']);
				$url = $this->data->datarow['post_url'];
				$this->data->datarow['cat_url'] = substr($url, 0, strrpos( $url, '/'));
				// if defaultlang and lang of the post is not the same then add lang to url
				$defaultLang = substr(\lib\utility\option::get('config', 'meta', 'defaultLang'), 0, 2);
				if($defaultLang !== $this->data->datarow['post_language'])
				{
					$this->data->datarow['post_url'] = $this->data->datarow['post_language']. "/".$this->data->datarow['post_url'];
				}
			}
		}
	}

	function draw_users()
	{
		$mychild                = $this->child();

		$this->data->form->users->user_status->required('required');

		$checkStatus = null;
		$myPerm      = null;
		$myPermNames = \lib\utility\option::permList();
		$myPermList  = $this->data->form->users->user_permission;
		if(count($myPermNames) > 5)
		{
			$myPermList->type('select');
			$checkStatus = 'selected';
		}
		else
		{
			$myPermList->type('radio');
			$checkStatus = 'checked';
		}
		if($mychild === 'edit')
		{
			$myPerm = $this->model()->datarow('users');
			$myPerm = $myPerm['user_permission'];
			if($myPerm === "1")
			{
				$myPermList->addClass('hide');
			}
		}


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
		$myPass = $this->data->form->users->user_pass;
		if($mychild === 'add')
		{
			$myPass->attr('required', 'required')->pl(T_('Enter password within 5 to 40 character') );
		}
		else
		{
			$myPass->label(T_('New Password'))->value(null)->pl(T_('If you want to change password enter it, else leave it blank'));
		}
	}

	// function pushState()
	// {
	// 	// temporary disable push state on control panel
	// 	$this->data->display['cp'] = null;
	// }
}
?>