<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Modular Extensions - PHP4
 *
 * Debug Helper
 *
 * Install this file as application/helpers/debug_helper.php
 *
 * Load or autoload as required.
 *
 * @version: 4.2.06 (c) Wiredesignz 2008-07-19
 */

	/**
	 * Debug
	 *
	 * Lists object_vars for an object
	 **/
	function debug($_this)
	{
	    if (is_object($_this))
	    {
	        echo '<pre>[',get_class($_this),' Object] => ',
	        print_r(array_keys(get_object_vars($_this)), TRUE),'</pre>';
	    }
	}

	/**
	 * Debug_in
	 *
	 * Dumps an object or array
	 **/
	function debug_in($_this)
	{
	    if (is_object($_this))
	    {
	        echo '<pre>[',get_class($_this),' Object] => Array</pre>'."\n";
	        foreach (get_object_vars($_this) as $key)
			{
				debug($key);
	        }
	    }

		if (is_array($_this))
	    {
	        echo '<pre>',print_r($_this, TRUE),'</pre>';
	    }
	}