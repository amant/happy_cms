<?php
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Welcome extends Admin_Controller
	{
		function __construct()
		{
			parent::__construct();
			$this->load->model('application_model');
		}

		function index()
		{
			$this->ocular->set_view_data('page_title', 'Dashboard');
			$this->ocular->render();
		}
	}