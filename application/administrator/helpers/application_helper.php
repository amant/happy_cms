<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	/**
	 * Returns DB base dynamic configuration setting value.
	 * Value are store in *_setting table
	 *
	 * @param string $name
	 * @return string, incase value is not found false is return
	 */
	function setting($name)
	{
		$ci = get_instance();
		$query = $ci->db->get_where('setting', array('name'=>$name));

		if($query->num_rows() > 0)
		{
			return $query->row()->value;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Checks weather the user login is true or false
	 *
	 * @return boolean
	 */
	function is_login()
	{
		$ci = get_instance();
		if($ci->session->userdata('login') == 1 && $ci->session->userdata('user_id') > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * To check if the backend debug mode is on or off
	 *
	 * @return bool
	 */
	function is_debug()
	{
		return (setting('debug_admin') === 1)? true : false;
	}

	/**
	 * returns chiper string from plain text
	 *
	 * @param string $plain_text
	 * @param string $salt
	 * @return string (chiper text)
	 */
	function encrypt($plain_text, $salt)
	{
		return $plain_text;
		// TODO: implement good encryption
//		$ci = get_instance();
//		$ci->load->library('ecdc');
//		return $ci->ecdc->encrypt($plain_text, $salt);

        /*
        $ci->load->library('encryption');
		return $ci->encryption->encrypt($salt, $plain_text);*/
    }

	/**
	 * returns de-chiper string from chiper string
	 *
	 * @param unknown_type $cipher_text
	 * @param unknown_type $salt
	 * @return unknown
	 */
	function decrypt($cipher_text, $salt)
	{
		return $cipher_text;

//		$ci = get_instance();
//		$ci->load->library('ecdc');
//		echo $ci->ecdc->decrypt($cipher_text, $salt);

        /*
        $ci->load->library('encryption');
        return $ci->encryption->decrypt($salt, $cipher_text);*/
	}

	/**
	 * Display Default index YML file
	 *
	 * @param string $file_path
	 */
	function display_yml_index($file_path = '')
	{
		if($file_path === '')
		{
			$controller = get_active_controller();
			$file_path = 'modules/' . $controller . '/views/' . $controller;
		}

		$ci = get_instance();
		$ci->load->library('spyc');
		$str_header = "";
		$str_body = "";

		$db = $ci->spyc->YAMLLoad($file_path);
		if($db===false)
		{
			return false;
		}
		else
		{
			if( isset($db['module']['index']) && count($db['module']['index']) > 0)
			{
				foreach($db['module']['index'] as $fieldsets)
				{
					$str_body = "";
					$str_header = "";

					if( isset($fieldsets['fieldset']) && count($db['module']['index']) > 0)
					{
						foreach($fieldsets['fieldset'] as $fieldset)
						{
							$str_body .= '
								<div style="float: left;">
									<div class="icon">'.
										anchor($fieldset['href'],image('administrator/theme/header/'.$fieldset['icon'], $fieldset['title'], array('align'=>'top','border'=>'0')).'<span>'.$fieldset['title'].'</span>')
									.'</div>
								</div>
							';
						}
					}

					$str_header .= "<fieldset class='adminform'>
										<legend>{$fieldsets['fieldsets']}</legend>
										<div id='cpanel'> {$str_body}</div>
									</fieldset>";
					echo $str_header;
				}
			}
		}
	}

	/**
	 * Display YML help content string
	 *
	 * @param string $file_path
	 */
	function display_yml_help($file_path='')
	{
		if($file_path=='')
		{
			$controller = get_active_controller();
			$file_path = 'modules/' . $controller . '/views/' . $controller;
		}

		$ci = get_instance();
		$ci->load->library('spyc');
		$str_header = "";
		$str_body = "";

		$db = $ci->spyc->YAMLLoad($file_path);
		if($db===false)
		{
			return false;
		}
		else
		{
			if(isset($db['module']['helps']))
			{
				foreach($db['module']['helps'] as $help)
				{
					$str_body = "";

					$str_body .= '<div class="panel">
									<h3 class="title toggler-down" id="cpanel-panel">'.$help['title'].'</h3>
									<div style="border-top: medium none; border-bottom: medium none; overflow: hidden; padding-top: 0px; padding-bottom: 0px; height: 500px;" class="slider content">
									  <div style="padding: 5px;">'.$help['description'].'</div>
									</div>
								  </div>';
					echo $str_body;
				}
			}
		}
	}
	
	/**
	 * Get the translated text using google translation service.
	 *
	 * @return string
	 */
	function app_google_translate($from, $to, $text)
	{
		$ci = get_instance();
		$ci->load->library('google_translate_api');
		$ci->load->library('convert_charset');

		$text = $ci->google_translate_api->translate($from, $to, $text);
		return $text;
	}

	/**
	 * Get the enum value from enum type DB column of a table
	 *
	 * @param string $table
	 * @param string $column
	 * @return array
	 */
	function get_enum_value($table, $column)
	{
		$ci = &get_instance();
		$table = $ci->db->dbprefix($table);

		$enum_array = array();
    	$sql = 'SHOW COLUMNS FROM `' . $table . '` LIKE "' . $column . '"';

    	$query = $ci->db->query($sql);
    	$row = $query->row();

    	preg_match_all('/\'(.*?)\'/', $row->Type, $enum_array);

    	if (!empty($enum_array[1]))
    	{
    		// Shift array keys to match original enumerated index in MySQL (allows for use of index values instead of strings)
    		foreach ($enum_array[1] as $key => $value)
    			$enum_fields[$key + 1] = $value;
    		return $enum_fields;
    	}
    	else
		{
    		return array();
		}
	}

	/**
	 * Parameter string to array
	 *
	 * @param string $params
	 * @return array
	 */
	function param_to_array($params)
	{
		$param = '';
		$arr = split(',', $params);

		if(is_array($arr))
		{
			foreach($arr as $str)
			{
				list($key, $value) = split('=', $str);
				$param[trim($key)] = trim($value);
			}
		}
		return $param;
	}

	/**
	 * Get file extension of a file.
	 *
	 * @param string $filename
	 * @return string
	 */
	function get_file_extension($filename)
	{
    	$ext  = strtolower(substr($filename, (strrpos($filename, '.') ? strrpos($filename, '.') + 1 : strlen($filename)), strlen($filename)));
    	return $ext;
	}

	/**
	 * Upload a image file
	 * Define image field name, and target path
	 *
	 * @param string $image_field
	 * @param string $upload_path
	 * @return string
	 */
	function upload_image($image_field, $upload_path)
	{
        $filename = false;

        if(isset($_FILES[$image_field]) && $_FILES[$image_field]['name']!="")
		{
            $file = do_upload($image_field, $upload_path);

            if( $file !== false)
			{
                $filename = $file['raw_name'].$file['file_ext'];
                do_resize($file['full_path'], $upload_path);
            }
        }
        return $filename;
    }

	/**
	 * Uploads a file if it is valid and allowded image extension
	 *
	 * @param string $image_field
	 * @param string $upload_path
	 * @return array,	on error return false
	 */
    function do_upload($image_field, $upload_path)
    {
    	$ci = get_instance();

        //$config['upload_path'] = './public/images/user/';
        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size']    = '2048';
        $config['max_width']  = '0';
        $config['max_height']  = '0';
        $config['remove_spaces']  = true;
        $config['encrypt_name']  = true;

        $ci->load->library('upload', $config);

        if ( ! $ci->upload->do_upload($image_field))
		{
            $ci->session->set_flashdata('error', $ci->upload->display_errors());
            return false;
        }
		else
		{
            return $ci->upload->data();
        }
    }

	/**
	 * Resize image
	 *
	 * @param string $src_path
	 * @param string $trg_path
	 */
    function do_resize($src_path, $trg_path)
    {
    	$ci = get_instance();
        $ci->load->library('image_lib');

        $config['image_library'] = 'gd2';
        $config['source_image'] = $src_path;
        $config['maintain_ratio'] = TRUE;
        $config['master_dim'] = 'height';

        // create thumbnail
        //$config['new_image'] = './public/images/banner/thumb';
        $config['new_image'] = $trg_path.'/thumb';
        $config['width'] = 150;
        $config['height'] = 150;

        $ci->image_lib->initialize($config);

        if(!$ci->image_lib->resize())
		{
            $ci->session->set_flashdata('error', $ci->image_lib->display_errors());
        }

        // create big image
        $ci->image_lib->clear();

        //$config['new_image'] = './public/images/banner/big';
        $config['new_image'] = $trg_path.'/big';
        $config['width'] = 600;
        $config['height'] = 600;
        $ci->image_lib->initialize($config);

        if(!$ci->image_lib->resize())
		{
            $ci->session->set_flashdata('error', $ci->image_lib->display_errors());
        }
    }

    /**
	 * Salt & Password for user login
	 *
	 * @param unknown_type $password
	 * @param unknown_type $salt
	 */
	function encrypt_password($password, $salt='')
	{
		if (empty($salt))
		{
			$salt = md5(uniqid(rand(), true));
		}

        $new_password = encrypt($password, $salt);

		return array($new_password, $salt);
	}	

    function send_email($subscriber, $subject, $body)
	{
		$ci = get_instance();
		$ci->load->library('email');

		$config = array();

		$config['charset'] = 'UTF-8';
		$config['mailtype'] = 'html';
		$config['wordwrap'] = TRUE;

		$config['protocol'] = setting('mail_mailer');
		switch(setting('mail_mailer'))
		{
			case "mail":
				break;

			case "sendmail":
				$config['mailpath'] = setting('mail_sendmail_path');
				break;

			case "smtp":
				$config['smtp_host'] = setting('mail_smtp_host');
				if(setting('mail_smtp_auth') == '1')
                {
					$config['smtp_user'] = setting('mail_smtp_user');
					$config['smtp_pass'] = setting('mail_smtp_pass');
				}
				break;
		}
		$ci->email->initialize($config);

		$ci->email->from(setting('mail_from'), setting('mail_from_name'));
		$ci->email->to($subscriber);

		$ci->email->subject(stripslashes($subject));
		$ci->email->message(stripslashes($body));

		if($ci->email->send())
        {
			$log = "Message has been sent successfully!";
		}
        else
        {
			$log = "Sorry we can not send your mail currently!";
		}

		/*echo $ci->email->print_debugger();
        die();*/

		$ci->email->clear();

		return $log;
	}

	/**
	 * JSON header
	 */
	function json_header()
	{
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		// header('Content-type: text/json');
		header('Content-type: application/json');
	}	

	function trim_nl($string)
	{
		return str_replace("\r\n", "", $string);
	}