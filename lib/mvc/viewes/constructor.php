<?php
namespace lib\mvc\viewes;

trait constructor
{
	/**
	 * [mvc_construct description]
	 * @return [type] [description]
	 */
	public function mvc_construct()
	{
		array_push($this->twig_include_path, addons);

		// define default value for url
		$this->url->full             = $this->url('full');       // full url except get parameter with http[s]
		$this->url->path             = $this->url('path');       // full path except parameter and domain name
		$this->url->breadcrumb       = $this->url('breadcrumb'); // full path in array for using in breadcrumb
		$this->url->domain           = $this->url('domain');     // domain name like 'ermile'
		$this->url->base             = $this->url('base');
		$this->url->tld              = $this->url('tld');        // domain ltd like 'com'
		$this->url->raw              = Service;                  // domain name except subdomain like 'ermile.com'
		$this->url->root             = $this->url('root');
		$this->url->static           = $this->url->root. 'static/';
		$this->url->protocol         = Protocol;
		$this->url->account          = $this->url('account');
		$this->url->MainStatic       = $this->url('MainService'). '/'.'static/';
		$this->url->MainSite         = $this->url('MainSite');
		$this->url->MainProtocol     = $this->url('MainProtocol');
		$this->url->SubDomain        = SubDomain? SubDomain.'.': null;

		// return all parameters and clean it
		$this->url->param            = \lib\utility::get(null, true);
		$this->url->all              = $this->url->full.$this->url->param;

		$this->data->site['title']       = T_("Saloos");
		$this->data->site['desc']        = T_("Another Project with Saloos");
		$this->data->site['slogan']      = T_("Saloos is an artichokes for PHP programming!!");
		$this->data->site['langlist']    = \lib\utility\option::languages();
		$this->data->site['currentlang'] = substr(\lib\router::get_storage('language'), 0, 2);
		$this->data->site['defaultLang'] = substr(\lib\router::get_storage('defaultLanguage'), 0, 2);

		// if allow to use social then get social network account list
		if(\lib\utility\option::get('social', 'status'))
		{
			$this->data->social = \lib\utility\option::get('social', 'meta');
		}

		$this->data->page['title']   = null;
		$this->data->page['desc']    = null;
		$this->data->page['special'] = null;

		$this->data->bodyclass       = null;
		$this->data->module          = $this->module();
		$this->data->child           = $this->child();
		$this->data->login           = $this->login('all');
		$this->data->perm            = $this->access(null, 'all');
		$this->data->permContent     = $this->access('all');

		// define default value for global
		$this->global->title         = null;
		$this->global->login         = $this->login();

		$this->global->lang          = \lib\router::get_storage('language');
		$this->global->direction     = \lib\router::get_storage('direction');
		$this->global->id            = $this->url('path','_');

		// add special pages to display array to use without name
		$this->data->display['main']       = "content/main/layout.html";
		$this->data->display['home']       = "content/home/display.html";
		$this->data->display['account']    = "content_account/home/layout.html";
		$this->data->display['cp']         = "content_cp/home/layout.html";
		$this->data->display['pagination'] = "content_cp/templates/inc_pagination.html";
		// add special pages to template array to use without name
		$this->data->template['header']    = 'content/template/header.html';
		$this->data->template['sidebar']   = 'content/template/sidebar.html';
		$this->data->template['footer']    = 'content/template/footer.html';

		// define default value for include
		$this->include->newline      = PHP_EOL;
		$this->include->css_main     = false;
		$this->include->css_ermile   = true;
		$this->include->js_main      = true;
		$this->include->css          = true;
		$this->include->js           = true;
		$this->include->fontawesome  = null;
		$this->include->datatable    = null;
		$this->include->telinput     = null;
		$this->include->lightbox     = null;
		$this->include->editor       = null;
		if(isset($this->controller->pagnation))
		{
			$this->data->pagnation = $this->controller->pagnation_get();
		}

		if(method_exists($this, '_construct'))
		{
			$this->_construct();
		}

		if(isset($this->url->MainStatic) && $this->url->MainStatic)
			$this->url->myStatic = $this->url->MainStatic;
		elseif(isset($this->url->MainStatic))
			$this->url->myStatic = $this->url->static;

		if(method_exists($this, 'options')){
			$this->options();
		}

		if(\lib\utility\option::get('config', 'meta', 'saveAsCookie'))
		{
			$mygetlist = \lib\utility::get(null, 'raw');
			if($mygetlist)
			{
				foreach ($mygetlist as $name => $value)
				{
					if($name === 'ssid')
						$_SESSION['ssid'] = $value;

					elseif( !($name === 'dev' || $name === 'lang') )
						\lib\utility\cookie::write($name, $value);
				}

				// remove get parameter from url
				header('Location: '. $this->url('full'));
			}
		}

		// check main  ********************************************* CHECK FOR ONLY IN FIRST PAGE IN RIGHT PLACE
		// in all page like ajax request must be run
		if(AccountService === MainService)
		{
			$this->model()->checkMainAccount();
			$this->controller()->checkSession();
		}
	}
}
?>