<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Cms extends CI_Controller {

		function __construct()
		{
			parent::__construct();
			$this->load->model('cms_model');
		}

		function detail($title_alias)
		{
			$query = $this->cms_model->content_by_title($title_alias);

			if($query->num_rows() === 1)
			{
				$db = $query->row_array();				
				$this->ocular->set_view_data('db', $db);
				$this->ocular->set_view_data('page_title', $title_alias);
				$this->ocular->render();
			}
			else
			{
				show_404();
			}
		}

		function index()
		{			
			redirect('welcome');				
		}		
	}