<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Welcome extends CI_Controller {

		function __construct()
		{
			parent::__construct();
			$this->load->model('cms_model');
		}

		function index()
		{
			$contents = $this->cms_model->content_list();

			$this->ocular->set_view_data('page_title', 'Welcome to Happy CMS');
			$this->ocular->set_view_data('contents', $contents);
			$this->ocular->render();
		}
	}
