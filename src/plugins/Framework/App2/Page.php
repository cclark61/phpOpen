<?php
//**************************************************************************************
//**************************************************************************************
/**
 * A class to construct the page framework (App2)
 *
 * @package		phpOpenFW2
 * @author 		Christian J. Clark
 * @copyright	Copyright (c) Christian J. Clark
 * @license		https://mit-license.org
 **/
//**************************************************************************************
//**************************************************************************************

namespace phpOpenFW\Framework\App2;

//**************************************************************************************
/**
 * Page Class
 */
//**************************************************************************************
class Page
{

	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	// Member Variables
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	//************************************************************************************
	/**
	 * @var array Application injected data
	 **/
	//************************************************************************************
	protected $app_data;

	//************************************************************************************
	/**
	 * @var mixed Data from controller
	 **/
	//************************************************************************************
	protected $content_data;

	//************************************************************************************
	/**
	 * @var bool Skip Page Render
	 **/
	//************************************************************************************
	protected $skip_render;

	//************************************************************************************
	/**
	 * @var bool Skip Module Controller Execution
	 **/
	//************************************************************************************
	protected $skip_mod_controller;

	//************************************************************************************
	/**
	 * @var string Page Render Method
	 **/
	//************************************************************************************
	protected $render_method;

	//************************************************************************************
	/**
	 * @var string Modules Directory
	 **/
	//************************************************************************************
	protected $modules_dir;

	//************************************************************************************
	/**
	 * @var string Action
	 **/
	//************************************************************************************
	protected $action;

	//************************************************************************************
	/**
	 * @var string Page Path
	 **/
	//************************************************************************************
	protected $page_path;

	//************************************************************************************
	/**
	 * @var Array Add-in JavaScript Files
	 **/
	//************************************************************************************
	protected $js_files;

	//************************************************************************************
	/**
	 * @var Array Add-in CSS Files
	 **/
	//************************************************************************************
	protected $css_files;

	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	// Class Methods
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	//************************************************************************************
	//************************************************************************************
	/**
	 * Module constructor function
	 **/
	//************************************************************************************
	//************************************************************************************
	public function __construct()
	{
		//===========================================================
		// Set Class Member Defaults
		//===========================================================
		$this->app_data = [];
		$this->content_data = false;
		$this->skip_render = false;
		$this->skip_mod_controller = false;
		$this->render_method = false;
		$this->modules_dir = PHPOPENFW_APP_FILE_PATH . '/modules';
		$this->action = false;
		$this->js_files = [];
		$this->css_files = [];

		//===========================================================
		// Page Path
		//===========================================================
		$this->page_path = \phpOpenFW\Framework\Core::get_url_path();

		//===========================================================
		// Page Segments
		//===========================================================
		$this->page_segments = explode('/', $this->page_path);
		if ($this->page_segments[0] == '') { array_shift($this->page_segments); }
		$tmp_len = count($this->page_segments);
		if ($this->page_segments[$tmp_len - 1] == '') { array_pop($this->page_segments); }

		//===========================================================
		// Is Action Set?
		//===========================================================
		if (isset($_POST['action'])) { $this->action = $_POST['action']; }
		else if (isset($_GET['action'])) { $this->action = $_GET['action']; }

		//===========================================================
		// Pre-page Include Script (pre_page.inc.php)
		//===========================================================
		$pre_page_inc = "{$this->modules_dir}/pre_page.inc.php";
		if (file_exists($pre_page_inc)) { require_once($pre_page_inc); }

		//===========================================================
		// Run Module Controller
		//===========================================================
		if (!$this->skip_mod_controller) {
			$this->RunModuleController();
		}
	}

	//************************************************************************************
	//************************************************************************************
	/**
	 * Module destructor function
	 **/
	//************************************************************************************
	//************************************************************************************
	public function __destruct()
	{
		$this->render();
	}

	//************************************************************************************
	//************************************************************************************
	/**
	 * Run Module Controller
	 **/
	//************************************************************************************
	//************************************************************************************
	public function RunModuleController()
	{
        //============================================================
		// Pre-module Include Script (pre_module.inc.php)
		//============================================================
		$pre_mod_inc = "{$this->modules_dir}/pre_module.inc.php";
		if (file_exists($pre_mod_inc)) { require_once($pre_mod_inc); }

        //============================================================
		// Determine Controller
        //============================================================
		$controller = "{$this->modules_dir}/controller.php";

        //============================================================
        // Controller Exists
        //============================================================
		if (file_exists($controller)) {
			define('CONTROLLER_EXISTS', true);

			//--------------------------------------------------------
			// Execute Module Controller?
			//--------------------------------------------------------
			if (!$this->skip_mod_controller) {

	            //--------------------------------------------------------
				// Set $_GET and $_POST arrays to local variables
				// GET first, then POST to prevent GET variables 
				// over writing POST variables
	            //--------------------------------------------------------
				extract($_GET, EXTR_PREFIX_SAME, "GET_");
				extract($_POST, EXTR_PREFIX_SAME, "POST_");

	            //--------------------------------------------------------
				// Set Action if not set
	            //--------------------------------------------------------
				if (!isset($action)) { $action = $this->action; }

	            //--------------------------------------------------------
				// Include Module Controller
	            //--------------------------------------------------------			
				ob_start();
				require_once($controller);
				$this->content_data = ob_get_clean();
			}
		}
		//============================================================
		// Controller does NOT Exist
		//============================================================
		else {
			define('CONTROLLER_EXISTS', false);
		}

        //============================================================
		// Post-module Include Script (post_module.inc.php)
		//============================================================
		$post_mod_inc = "{$this->modules_dir}/post_module.inc.php";
		if (file_exists($post_mod_inc)) { require_once($post_mod_inc); }
	}

	//************************************************************************************
	//************************************************************************************
	/**
 	 * Render the current page
	 **/
	//************************************************************************************
	//************************************************************************************
	protected function render()
	{
		//===========================================================
		// Skip the Render Process?
		//===========================================================
		if ($this->skip_render || (defined('PHPOPENFW_SKIP_RENDER') && PHPOPENFW_SKIP_RENDER)) {
			return false;
		}

		//===========================================================
		// Add JavaScript / CSS Files to Data
		//===========================================================
		$this->app_data['js_files'] = $this->js_files;
		$this->app_data['css_files'] = $this->css_files;

		//===========================================================
		// Render Method Specified
		//===========================================================
		if ($this->render_method) {
			call_user_func_array($this->render_method, [$this, $this->app_data, $this->content_data]);
		}
		//===========================================================
		// No Render Method Given, Dump Content Data
		//===========================================================
		else {
			print $this->content_data;
		}

		//===========================================================
		// Post-page Include Script (post_page.inc.php)
		//===========================================================
		$post_page_inc = "{$this->modules_dir}/post_page.inc.php";
		if (file_exists($post_page_inc)) { require_once($post_page_inc); }
	}

	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	// Setter Methods
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	//************************************************************************************
	//************************************************************************************
	/**
	 * Skip Render Function
	 * @param bool True = Skip (Default), False = Do NOT Skip
	 **/
	//************************************************************************************
	//************************************************************************************
	public function skip_render($skip=true)
	{
		$this->skip_render = (bool)$skip;
	}

	//************************************************************************************
	//************************************************************************************
	/**
	 * Toggle the Skip Module Controller Flag
	 * @param bool True = Skip (Default), False = Do NOT Skip
	 **/
	//************************************************************************************
	//************************************************************************************
	public function skip_module($skip=true)
	{
		$this->skip_mod_controller = (bool)$skip;
	}

	//************************************************************************************
	//************************************************************************************
	/**
	 * Set Application Data
	 **/
	//************************************************************************************
	//************************************************************************************
	public function set_data($key, $val, $overwrite=true)
	{
		if ($key == '') { return false; }
		if (!isset($this->app_data[$key]) || ($overwrite)) {
			$this->app_data[$key] = $val;
			return true;
		}
		return false;
	}

	//************************************************************************************
	//************************************************************************************
	/**
	 * Unset Application Data
	 **/
	//************************************************************************************
	//************************************************************************************
	public function unset_data($key)
	{
		if ($key == '') { return false; }
		if (isset($this->app_data[$key])) {
			unset($this->app_data[$key]);
			return true;
		}
		return false;
	}

	//************************************************************************************
	//************************************************************************************
	/**
	 * Set Render Method
	 **/
	//************************************************************************************
	//************************************************************************************
	public function set_render_method($method)
	{
		if ($method == '') { return false; }
		$this->render_method = $method;
		return true;
	}

	//************************************************************************************
	//************************************************************************************
	/**
	 * Set Modules Directory
	 **/
	//************************************************************************************
	//************************************************************************************
	public function set_modules_dir($dir)
	{
		$dir = realpath($dir);
		if (!$dir) { return false; }
		$this->modules_dir = $dir;
		return true;
	}

	//************************************************************************************
	//************************************************************************************
	/**
	 * Set variables in array format for the current module
	 * @param array an array of values to be accessed later
	 **/
	//************************************************************************************
	//************************************************************************************
	public function set_mod_vars($vars, $index=false)
	{
		if (!is_array($vars)) { return false; }
		foreach ($vars as $key => $val) {
			$this->set_mod_var($key, $val, $index);
		}
		return true;
	}

	//************************************************************************************
	//************************************************************************************
	/**
	 * Set a variable in the current module's session array
	 * @param string The name of the variable
	 * @param mixed The Value of the variable
	 **/
	//************************************************************************************
	//************************************************************************************
	public function set_mod_var($key, $val, $index=false)
	{
		if ($key == '') { return false; }
		if (!$index) { $index = $this->page_path; }
		$mod_index = 'mod-' . $index;
		$_SESSION[$mod_index][$key] = $val;
		return true;
	}

	//************************************************************************************
	//************************************************************************************
	/**
	 * Destroy the current module's session array
	 **/
	//************************************************************************************
	//************************************************************************************
	public function clear_mod_vars($index=false)
	{
		if (!$index) { $index = $this->page_path; }
		$mod_index = 'mod-' . $index;
		if (isset($_SESSION[$mod_index])) {
			unset($_SESSION[$mod_index]);
			return true;
		}
		return false;
	}
	
	//************************************************************************************
	//************************************************************************************
	/**
	 * Destroy a current module variable
	 **/
	//************************************************************************************
	//************************************************************************************
	public function clear_mod_var($key, $index=false)
	{
		if ($key == '') { return false; }
		if (!$index) { $index = $this->page_path; }
		$mod_index = 'mod-' . $index;
		if (isset($_SESSION[$mod_index][$key])) { 
			unset($_SESSION[$mod_index][$key]);
			return true;
		}
		return false;
	}

	//************************************************************************************
	//************************************************************************************
	/**
	 * Add a Javascript File to be included
	 * @param string Javascript File
	 **/
	//************************************************************************************
	//************************************************************************************
	public function add_js_file($file)
	{
		if ($file) { $this->js_files[] = $file; }
	}

	//************************************************************************************
	//************************************************************************************
	/**
	 * Add a CSS File to be included
	 * @param array CSS link attributes
	 **/
	//************************************************************************************
	//************************************************************************************
	public function add_css_file($file_attrs)
	{
		if (is_array($file_attrs)) {
			if (!isset($file_attrs['rel'])) { $file_attrs['rel'] = 'stylesheet'; }
			if (!isset($file_attrs['type'])) { $file_attrs['type'] = 'text/css'; }
			if (!isset($file_attrs['media'])) { $file_attrs['media'] = 'all'; }
			$this->css_files[] = $file_attrs;
		}
		else {
			settype($file_attrs, 'string');
			$css_file = $file_attrs;
			$file_attrs = array();
			$file_attrs['href'] = $css_file;
			$file_attrs['rel'] = 'stylesheet';
			$file_attrs['type'] = 'text/css';
			$file_attrs['media'] = 'all';
			$this->css_files[] = $file_attrs;
		}
	}

	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	// Getter Methods
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	//************************************************************************************
	//************************************************************************************
	/**
	 * Get Previously Set Application Data
	 **/
	//************************************************************************************
	//************************************************************************************
	public function get_data($key=false)
	{
		if ($key == '') {
			return $this->app_data;
		}
		if (isset($this->app_data[$key])) {
			return $this->app_data[$key];
		}
		return null;
	}

	//************************************************************************************
	//************************************************************************************
	/**
	 * Get Content Generated by the Module
	 **/
	//************************************************************************************
	//************************************************************************************
	public function get_content()
	{
		return $this->content_data;
	}

	//************************************************************************************
	//************************************************************************************
	/**
	 * Get Page URL Path
	 **/
	//************************************************************************************
	//************************************************************************************
	public function get_url_path()
	{
		return $this->page_path;
	}

	//************************************************************************************
	//************************************************************************************
	/**
	 * Get Page URL Segments
	 **/
	//************************************************************************************
	//************************************************************************************
	public function get_url_segments()
	{
		return $this->page_segments;
	}

	//************************************************************************************
	//************************************************************************************
	/**
	 * Get Modules Directory
	 **/
	//************************************************************************************
	//************************************************************************************
	public function get_modules_dir()
	{
		return $this->modules_dir;
	}

	//************************************************************************************
	//************************************************************************************
	/**
	 * Returns an array of values that was set with set_mod_vars($vars) otherwise returns NULL
	 * @return array an array of values set for this module or NULL
	 **/
	//************************************************************************************
	//************************************************************************************
	public function get_mod_vars($index=false)
	{
		if ($key == '') { return false; }
		if (!$index) { $index = $this->page_path; }
		$mod_index = 'mod-' . $index;
		if (isset($_SESSION[$mod_index]) && !empty($_SESSION[$mod_index])) {
			return $_SESSION[$mod_index];
		}
		return null;
	}

	//************************************************************************************
	//************************************************************************************
	/**
	 * Returns a variable's value from the current module's session array
	 * @return mixed A variable's value from the current module's session array
	 **/
	//************************************************************************************
	//************************************************************************************
	public function get_mod_var($key, $index=false)
	{
		if ($key == '') { return false; }
		if (!$index) { $index = $this->page_path; }
		$mod_index = 'mod-' . $index;
		if (isset($_SESSION[$mod_index][$key]) && !empty($_SESSION[$mod_index])) {
			return $_SESSION[$mod_index][$key];
		}
		return null;
	}

}
