<?php
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class User extends Admin_Controller
	{
		function __construct()
		{
			parent::__construct();
			$this->load->model('user_model');
			$this->load->model('user_type_model');
			$this->load->library('form_validation');
			$this->load->library('ca_gridview');
			$this->load->helper('user');
		}

		/**
		 * Set form rules
		 */
		function _set_form_validation_rules($form)
		{
			$config = array();

			if ($form === 'add')
			{
				$config = array(
					array(
						'field' => 'email',
						'label' => 'Email',
						'rules' => 'trim|required|valid_email|callback_is_email_exist'
					),
					array(
						'field' => 'password',
						'label' => 'Password',
						'rules' => 'trim|required'
					),
					array(
						'field' => 'first_name',
						'label' => 'First Name',
						'rules' => 'trim|required'
					)
				);
			}
			elseif ($form === 'edit')
			{
				$config = array(
					array(
						'field' => 'email',
						'label' => 'Email',
						'rules' => 'trim|required|valid_email'
					),
					array(
						'field' => 'password',
						'label' => 'Password',
						'rules' => 'trim|required'
					),
					array(
						'field' => 'first_name',
						'label' => 'First Name',
						'rules' => 'trim|required'
					)
				);
			}
			$this->form_validation->set_rules($config);
		}

		function add($save = '')
		{
			$this->_set_form_validation_rules('add');

			// if validation is ok then insert the data and unset the session, otherwise don't and redirect to source page
			if ($this->form_validation->run() === true)
			{
				if ($this->user_model->insert() !== false)
				{
					$this->session->set_userdata('message', 'Successfully created.');

					// Empty out post data
					$_POST = array();

					if ($save !== 'apply')
					{
						redirect('user/display', 'refresh');
					}
				}
				else
				{
					$this->session->set_userdata('error', 'Error occured while creating db record. Please try again.');
				}
			}
			else
			{
				$this->session->set_userdata('error', $this->form_validation->error_string());
			}

			// set default value in form fields
			$db = $this->user_model->field();

			// Initial value when post is not set
			if (count($_POST) === 0)
			{
				$db['status']  = 1;
				$db['created'] = gmdate("Y-m-d H:m:s");
			}

			$this->ocular->set_view_data('db', $db);
			$this->ocular->set_view_data('page_title', 'User Add');
			$this->ocular->render();
		}

		function delete($user_id)
		{
			if ($user_id > 0)
			{
				$this->user_model->delete($user_id);
				$this->session->set_flashdata('message', 'Successfully Deleted.');
			}
			else
			{
				$this->session->set_flashdata('error', 'Could not Delete.');
			}

			redirect('/user/display');
		}

		function delete_all()
		{
			if (count($this->input->post('chk_user_id')) > 0)
			{
				foreach ($this->input->post('chk_user_id') as $user_id)
				{
					$this->user_model->delete($user_id);
				}
				$this->session->set_flashdata('message', 'Successfully Deleted.');
			}
			else
			{
				$this->session->set_flashdata('error', 'Could not Delete.');
			}

			redirect('user/display_trash');
		}

		function display()
		{
			$grid_params = array(
				'set_query' => $this->user_model->get_users(),
				'set_multirow_selection' => array(
					'enable' => true,
					'primary_key' => 'user_id'
				),
				'set_pagination' => array(
					'enable' => true,
					'initialize' => array(
						'base_url' => site_url('user/display'),
						'per_page' => 25,
						'num_links' => 5
					),
					'order_field' => 'user_id',
					'order_type' => 'ASC',
					'offset' => 0
				),
				'set_column' => array(
					'first_name' => array(
						'title' => 'First Name',
						'sortable' => true,
						'type' => array(
							'link',
							'href' => site_url('user/edit/{user_id}')
						)
					),
					'last_name' => array(
						'title' => 'Last Name',
						'sortable' => true,
						'type' => array(
							'text'
						)
					),
					'email' => array(
						'title' => 'Email',
						'sortable' => true,
						'type' => array(
							'text'
						)
					),
					'status' => array(
						'title' => 'Status',
						'sortable' => true,
						'type' => array(
							'image',
							'src' => base_url() . 'public/images/administrator/theme/{status}.png'
						)
					),
					'user_type' => array(
						'title' => 'User Type',
						'sortable' => false,
						'type' => array(
							'text'
						)
					),
					'user_id' => array(
						'title' => 'ID',
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
							'href' => site_url('user/edit/{user_id}')
						),

						'delete' => array(
							'title' => 'Delete',
							'type' => array(
								'image',
								'src' => base_url() . 'public/images/administrator/theme/delete.png'
							),
							'href' => site_url('user/delete/{user_id}'),
							'html_attribute' => array(
								'onclick' => "javascript:if(confirm('Sure Want to Delete Permanently ?')) return true; else return false; "
							)
						)
					)
				),
				'set_filter' => array(
					'enable' => true,
					array(
						'user_id' => array(
							'title' => 'ID',
							'type' => array(
								'number'
							)
						),
						'first_name' => array(
							'title' => 'First Name',
							'type' => array(
								'text'
							)
						),
						'last_name' => array(
							'title' => 'Last Name',
							'type' => array(
								'text'
							)
						),
						'email' => array(
							'title' => 'Email',
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
			$this->ocular->set_view_data('page_title', 'User List');
			$this->ocular->render();
		}

		function display_trash()
		{
			$grid_params = array(
				'set_query' => $this->user_model->get_users_trash(),

				'set_multirow_selection' => array(
					'enable' => true,
					'primary_key' => 'user_id'
				),

				'set_pagination' => array(
					'enable' => true,
					'initialize' => array(
						'base_url' => site_url('user/display'),
						'per_page' => 25,
						'num_links' => 5
					),
					'order_field' => 'user_id',
					'order_type' => 'ASC',
					'offset' => 0
				),

				'set_column' => array(
					'user_id' => array(
						'title' => 'ID',
						'type' => array(
							'text'
						)
					),
					'first_name' => array(
						'title' => 'First Name',
						'sortable' => true,
						'type' => array(
							'link',
							'href' => site_url('user/edit/{user_id}')
						)
					),
					'last_name' => array(
						'title' => 'Last Name',
						'sortable' => true,
						'type' => array(
							'text'
						)
					),
					'email' => array(
						'title' => 'Email',
						'sortable' => true,
						'type' => array(
							'text'
						)
					),
					'status' => array(
						'title' => 'Status',
						'sortable' => true,
						'type' => array(
							'image',
							'src' => base_url() . 'public/images/administrator/theme/{status}.png'
						)
					)
				),
				'set_action' => array(
					'enable' => false
				),
				'set_filter' => array(
					'enable' => true,
					array(
						'user_id' => array(
							'title' => 'ID',
							'type' => array(
								'number'
							)
						),
						'first_name' => array(
							'title' => 'First Name',
							'type' => array(
								'text'
							)
						),
						'last_name' => array(
							'title' => 'Last Name',
							'type' => array(
								'text'
							)
						),
						'email' => array(
							'title' => 'Email',
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
			$this->ocular->set_view_data('page_title', 'User Trash List');
			$this->ocular->render();
		}

		function edit($user_id = 0, $save = '')
		{
			// Check if record exist.
			$user = $this->user_model->user_by_user_id($user_id);

			if ($user->num_rows() === 0)
			{
				$this->session->set_userdata('error', 'Record not found it may have been delete, you can create new record.');
				redirect('user/add', 'refresh');
			}

			// set the rule
			$this->_set_form_validation_rules('edit');

			// if validation ok, then update data otherwise don't
			if ($this->form_validation->run() === true)
			{
				$this->user_model->update();

				$this->session->set_userdata('message', 'Successfully updated.');

				if ($save !== 'apply')
				{
					redirect('user/display', 'refresh');
				}
			}
			else
			{
				$this->session->set_userdata('error', $this->form_validation->error_string());
			}

			$user = $this->user_model->user_by_user_id($user_id);
			$db   = $user->row_array();

			$this->ocular->set_view_data('db', $db);
			$this->ocular->set_view_data('page_title', 'User Edit');
			$this->ocular->render();
		}

		function index()
		{
			$this->ocular->set_view_data('page_title', 'User');
			$this->ocular->render();
		}

		function is_email_exist($email)
		{
			$query = $this->user_model->user_by_user_email($email);
			if ($query->num_rows > 0)
			{
				$this->form_validation->set_message('is_email_exist', 'Email ' . $user_email . ', already exist. Please use another email address.');
				return false;
			}
			else
			{
				return true;
			}
		}

		function is_user_login_exist($user_login)
		{
			$query = $this->user_model->user_by_user_login($user_login);
			if ($query->num_rows > 0)
			{
				$this->form_validation->set_message('is_user_login_exist', 'User login name ' . $user_login . ', already exist. Please use another user name');
				return false;
			}
			else
			{
				return true;
			}
		}

		function publish()
		{
			if (count($this->input->post('chk_user_id')) > 0)
			{
				foreach ($this->input->post('chk_user_id') as $user_id)
				{
					$this->user_model->update_by_user_id($user_id, array(
						'status' => '1'
					));
				}
				$this->session->set_flashdata('message', 'Successfully Updated.');
			}
			else
			{
				$this->session->set_flashdata('error', 'Could not Update.');
			}

			redirect('user/display');
		}

		function restore()
		{
			$this->publish();
		}

		function trash()
		{
			if (count($this->input->post('chk_user_id')) > 0)
			{
				foreach ($this->input->post('chk_user_id') as $user_id)
				{
					$this->user_model->update_by_user_id($user_id, array(
						'status' => 'trash'
					));
				}
				$this->session->set_flashdata('message', 'Successfully Updated.');
			}
			else
			{
				$this->session->set_flashdata('error', 'Could not Update.');
			}

			redirect('user/display');
		}

		function unpublish()
		{
			if (count($this->input->post('chk_user_id')) > 0)
			{
				foreach ($this->input->post('chk_user_id') as $user_id)
				{
					$this->user_model->update_by_user_id($user_id, array(
						'status' => '0'
					));
				}
				$this->session->set_flashdata('message', 'Successfully Updated.');
			}
			else
			{
				$this->session->set_flashdata('error', 'Could not Update.');
			}

			redirect('user/display');
		}
	}