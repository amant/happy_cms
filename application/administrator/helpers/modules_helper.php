<?php
	if (!defined('BASEPATH'))
		exit('No direct script access allowed');
	/**
	 * Modular Extensions - PHP4
	 *
	 * Adapted from the CodeIgniter Core Classes
	 * @copyright	Copyright (c) 2006, EllisLab, Inc.
	 * @license		http://codeigniter.com/user_guide/license.html
	 *
	 * Description:
	 * This helper provides functions to load and instantiate controllers
	 * and module controllers.
	 *
	 * Module controllers have access to the currently loaded core classes
	 * in the same manner as a normal controller.
	 *
	 * Install this file as application/helpers/modules_helper.php
	 *
	 * @version: 4.2.06 (c) Wiredesignz 2008-07-19
	 **/

	/** Load a module controller **/
	function &modules_load($module, $base = 'controllers/')
	{
		/* the module registry */
		static $registry = array();

		(is_array($module)) AND list($module, $params) = each($module) OR $params = NULL;

		/* get the controller class name */
		$class = modules_decode($module);

		/* get existing object from registry */
		if (isset($registry[$class]))
			return $registry[$class];

		/* find the module controller */
		list($path, $module, $home) = modules_find($module, $module, $base);

		if ($path === FALSE)
			return $path;

		/* set the module directory */
		modules_path($home);

		/* load the module controller class */
		modules_load_file($module, $path);

		$module = ucfirst($module);

		/* create the new module */
		$registry[$class] = new $module($params);

		return $registry[$class];
	}

	/** Return current controller instance **/
	function &modules_instance($instance = NULL)
	{
		static $ci_instance;

		(is_object($instance)) AND $ci_instance = $instance;

		return $ci_instance;
	}

	/** Remove the path from filename **/
	function modules_decode($file)
	{
		if (($pos = strrpos($file, '/')) !== FALSE)
			$file = substr($file, $pos + 1);

		return strtolower($file);
	}

	/** Set the module directory **/
	function modules_path($path = NULL)
	{
		static $home;

		(isset($home)) OR $home = router::path();

		($path) AND $home = $path;

		return $home;
	}

	/** Load a module file **/
	function modules_load_file($file, $path, $type = 'other', $result = TRUE)
	{
		$file = str_replace(EXT, '', $file);

		if ($type === 'other')
		{
			if (class_exists($file))
			{
				log_message('debug', "File already loaded: " . $path . $file . EXT);

				return $result;
			}

			include_once $path . $file . EXT;
		}
		else
		{
			include $path . $file . EXT;

			if (!isset($$type) OR !is_array($$type))
			{
				show_error($path . $file . EXT . " does not contain a valid {$type} array");
			}

			$result = $$type;
		}

		log_message('debug', "File loaded: " . $path . $file . EXT);

		return $result;
	}

	/** Find a file
	 *
	 * Scans for files located anywhere within application/modules directory.
	 * Also scans application directories for config, controllers, models and views.
	 * Generates fatal error on file not found.
	 *
	 **/
	function modules_find($file, $path = '', $base = 'controllers/', $subpath = '')
	{
		if (($pos = strrpos($file, '/')) !== FALSE)
		{
			$path = substr($file, 0, $pos);

			$file = substr($file, $pos + 1);
		}

		($path) AND $path .= '/';

		/* scan module directory first */
		$paths2scan = array(
			MODBASE . $path . $base
		);

		/* then scan application directories for these types */
		if (in_array($base, array(
			'controllers/',
			'models/',
			'views/'
		)))
		{
			$paths2scan = array_merge($paths2scan, array(
				APPPATH . $base,
				APPPATH . $base . $path
			));
		}

		/* and also scan sub-directories */
		if ($subpath)
		{
			$subpath .= '/';

			$paths2scan = array_merge($paths2scan, array(
				MODBASE . $path . $base . $subpath,
				APPPATH . $base . $subpath
			));
		}

		/* then scan further sub-directories for language */
		if (in_array($base, array(
			'language/'
		)))
			$paths2scan = array_merge($paths2scan, array(
				MODBASE . $subpath . $base . $path
			));


		$file_ext = strpos($file, '.') ? $file : $file . EXT;

		foreach ($paths2scan as $path2)
		{
			/* echo '<p>',$path2,$file_ext,'</p>'; /* debug paths */

			foreach (array(
				$file_ext,
				ucfirst($file_ext)
			) as $name)
			{
				if (is_file($path2 . $name))
					return array(
						$path2,
						$file,
						substr($path, 0, -1)
					);
			}
		}

		/* file not found */

		/* don't die just yet, we handle these types back in the caller */
		if (in_array($base, array(
			'config/',
			'controllers/',
			'helpers/',
			'language/',
			'libraries/',
			'plugins/'
		)))
			return array(
				FALSE,
				$file,
				FALSE
			);


		/* ok, die now */
		show_error("Unable to locate the requested file: " . $path2 . $file_ext);
	}

//	class Modules
//	{
//		/**
//		 * Run a module and method
//		 *
//		 * Prevents running the module constructor and redirects to the
//		 * module index method appropriately.
//		 *
//		 * Output from module is buffered and returned.
//		 **/
//		function run($module, $data = '', $method = '')
//		{
//			if ($class =& modules_load($module))
//			{
//				/* protect the constructor */
//				($method == '' OR $method == $module) AND $method = 'index';
//
//				ob_start();
//
//				$output = $class->method($method, $data);
//
//				$buffer = ob_get_contents();
//
//				ob_end_clean();
//
//				return ($output) ? $output : $buffer;
//			}
//		}
//	}