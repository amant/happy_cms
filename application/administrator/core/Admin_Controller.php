<?php defined('BASEPATH') OR exit('No direct script access allowed');

	class Admin_Controller extends CI_Controller
	{
		function Admin_Controller()
		{
			parent::__construct();

			// Show error and exit if the user does not have sufficient permissions
			if( ! $this->session->userdata('login') )
			{
				//$this->session->set_flashdata('error', 'Access denied, try login again.');
				redirect('login/index');
			}
			
			// Testing of query effiency
			// $this->output->enable_profiler(TRUE);
		}
	}