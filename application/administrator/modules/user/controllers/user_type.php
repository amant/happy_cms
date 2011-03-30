<?php
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class User_Type extends Admin_Controller
	{
		function __construct()
		{
			parent::__construct();
			$this->load->model('user_model', 'user');
			$this->load->model('user_type_model', 'user_type');
			$this->load->library('form_validation');
			$this->load->library('ca_gridview');
			$this->load->helper('user');
		}

		function add()
		{
			// set default value in form fields
			$db           = $this->user_type->field();
			$db['status'] = 1;
			$this->ocular->set_view_data('db', $db);


			// if we have form data from last user entry set that
			if ($this->session->userdata('db'))
			{
				$db = $this->session->userdata('db');
				$this->ocular->set_view_data('db', $db);
				$this->session->unset_userdata('db');
			}

			$this->ocular->set_view_data('page_title', 'User Add');
			$this->ocular->render();
		}

		function delete($user_type_id)
		{
			if ($user_type_id > 0)
			{
				$this->user_type->delete($user_type_id);
				$this->session->set_flashdata('message', 'Successfully Deleted.');

			}
			else
			{
				$this->session->set_flashdata('error', 'Could not Delete.');
			}

			redirect('/user/user_type/display');
		}

		function display()
		{
			$grid_params = array(
				'set_query' => $this->user_type->get_user_type(),

				'set_multirow_selection' => array(
					'enable' => true,
					'primary_key' => 'user_type_id'
				),

				'set_pagination' => array(
					'enable' => true,
					'initialize' => array(
						'base_url' => site_url('user/user_type/display'),
						'per_page' => 25,
						'num_links' => 5
					),
					'order_field' => 'user_type_id',
					'order_type' => 'ASC',
					'offset' => 0
				),

				'set_column' => array(
					'title' => array(
						'title' => 'Title',
						'sortable' => false,
						'type' => array(
							'link',
							'href' => site_url('user/user_type/edit/{user_type_id}')
						)
					),
					'status' => array(
						'title' => 'Status',
						'sortable' => false,
						'type' => array(
							'image',
							'src' => base_url() . 'public/images/administrator/theme/{status}.png'
						)
					),
					'parent' => array(
						'title' => 'Parent',
						'sortable' => false,
						'type' => array(
							'text'
						)
					),
					'user_1' => array(
						'title' => 'Active',
						'sortable' => false,
						'type' => array(
							'text'
						)
					),
					'user_0' => array(
						'title' => 'Not-Active',
						'sortable' => false,
						'type' => array(
							'text'
						)
					),
					'user_trash' => array(
						'title' => 'Trash',
						'sortable' => false,
						'type' => array(
							'text'
						)
					),
					'user_verifying' => array(
						'title' => 'On Processign',
						'sortable' => false,
						'type' => array(
							'text'
						)
					),
					'user_type_id' => array(
						'title' => 'ID',
						'sortable' => false,
						'type' => array(
							'text'
						)
					)
				),

				'set_action' => array(
					'enable' => true,
					array(
						'edit' => array(
							'title' => 'Edit',
							'type' => array(
								'image',
								'src' => base_url() . 'public/images/administrator/theme/edit.png'
							),
							'href' => site_url('user/user_type/edit/{user_type_id}')
						),

						'delete' => array(
							'title' => 'Delete',
							'type' => array(
								'image',
								'src' => base_url() . 'public/images/administrator/theme/delete.png'
							),
							'href' => site_url('user/user_type/delete/{user_type_id}'),
							'html_attribute' => array(
								'onclick' => "javascript:if(confirm('Sure Want to Delete Permanently ?')) return true; else return false; "
							)
						)
					)
				),

				'set_filter' => array(
					'enable' => true,
					array(
						'user_type_id' => array(
							'title' => 'ID',
							'type' => array(
								'number'
							)
						),

						'title' => array(
							'title' => 'Title',
							'type' => array(
								'text'
							)
						)
					)
				),
				'set_export' => array(
					'enable' => false
				),
				'set_print' => array(
					'enable' => false
				),
				'set_debug' => array(
					'enable' => false
				)
			);
			$this->ca_gridview->render_grid($grid_params);
			$this->ocular->set_view_data('page_title', 'User Type');
			$this->ocular->render();
		}

		function edit($user_type_id)
		{
			$user_type = $this->user_type->user_type_by_user_type_id($user_type_id);

			if ($user_type->num_rows() > 0)
			{
				$this->ocular->set_view_data('db', $user_type->row_array());
				$this->ocular->render();
			}
			else
			{
				$this->session->set_flashdata('error', 'Could not load the record set.');
				redirect('user/user_type/display');
			}
		}

		function index()
		{
			redirect('user/index');
		}

		function insert()
		{
			// get the user data form form fields
			$this->session->set_userdata('db', $this->user_type->field());

			// define the rule for fields
			$config = array(
				array(
					'field' => 'title',
					'label' => 'Title',
					'rules' => 'trim|required'
				)
			);
			$this->form_validation->set_rules($config);

			// if validation is ok then insert the data and unset the session, otherwise don't and redirect to source page
			if ($this->form_validation->run() == false)
			{
				$this->session->set_flashdata("error_validation", $this->form_validation->error_string());
				redirect('/user/user_type/add');
			}
			else
			{
				if ($this->user_type->insert() == false)
				{
					$this->session->set_flashdata('error', 'Error Occured While Creating Database Record.');
					redirect('/user/user_type/add');
				}
				else
				{
					$this->session->set_flashdata('message', 'Successfully Created.');
					$this->session->unset_userdata('db');
					redirect('/user/user_type/display');
				}
			}
		}

		function publish()
		{
			if (count($this->input->post('chk_user_type_id')) > 0)
			{
				foreach ($this->input->post('chk_user_type_id') as $user_type_id)
				{
					$this->user_type->update_by_user_type_id($user_type_id, array(
						'status' => '1'
					));
				}
				$this->session->set_flashdata('message', 'Successfully Updated.');

			}
			else
			{
				$this->session->set_flashdata('error', 'Could not Update.');
			}

			redirect('user/user_type/display');
		}

		function unpublish()
		{
			if (count($this->input->post('chk_user_id')) > 0)
			{
				foreach ($this->input->post('chk_user_type_id') as $user_type_id)
				{
					$this->user_type->update_by_user_type_id($user_type_id, array(
						'status' => '0'
					));
				}
				$this->session->set_flashdata('message', 'Successfully Updated.');

			}
			else
			{
				$this->session->set_flashdata('error', 'Could not Update.');
			}

			redirect('user/user_type/display');
		}

		function update()
		{
			// set the rule
			$config = array(
				array(
					'field' => 'title',
					'label' => 'Title',
					'rules' => 'trim|required'
				)
			);
			$this->form_validation->set_rules($config);

			// if validation ok, then update data otherwise don't
			if ($this->form_validation->run() == false)
			{
				$this->session->set_flashdata('error_validation', $this->form_validation->error_string());
				redirect('/user/user_type/edit/' . $this->user_type->user_type_id);
			}
			else
			{
				$this->user_type->update();
				$this->session->set_flashdata('message', 'Successfully Updated.');
				redirect('/user/user_type/display');
			}
		}
	}