<?php
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Cms_comment extends Admin_Controller
	{
		/**
		 * constructor
		 */
		function __construct()
		{
			parent::__construct();
			$this->load->model('cms_comment_model');
			$this->load->model('cms_model');
			$this->load->model('application_model', 'application');

			$this->load->library('form_validation');
			$this->load->library('ca_gridview');

			$this->load->helper('cms');
		}

		/**
		 * Set form rules
		 */
		function _set_form_validation_rules()
		{
			$config = array(
				array(
					'field' => 'cms_content_id',
					'label' => 'Article Id',
					'rules' => 'trim|required'
				),
				array(
					'field' => 'author',
					'label' => 'Author',
					'rules' => 'trim|required'
				),
				array(
					'field' => 'author_email',
					'label' => 'Email',
					'rules' => 'trim|required'
				)
			);

			$this->form_validation->set_rules($config);
		}

		/**
		 * Display add comment page
		 */
		function add($save = '')
		{
			$this->_set_form_validation_rules();
			// if validation is ok then insert the data and unset the session, otherwise don't and redirect to source page
			if ($this->form_validation->run() === true)
			{
				if ($this->cms_comment_model->insert() !== false)
				{
					$this->session->set_userdata('message', 'Successfully created.');

					// Empty out post data
					$_POST = array();

					if ($save !== 'apply')
					{
						redirect('cms/cms_comment/display', 'refresh');
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
			$db = $this->cms_comment_model->field();

			// Initial value when post is not set
			if (count($_POST) === 0)
			{
				$db['status']  = 1;
				$db['created'] = gmdate("Y-m-d H:m:s");
			}

			$this->ocular->set_view_data('db', $db);
			$this->ocular->set_view_data('page_title', 'Add Comment');
			$this->ocular->render();
		}

		/**
		 * Delete comment
		 *
		 * @param int $id
		 */
		function delete($id)
		{
			if ($id > 0)
			{
				$this->cms_comment_model->delete($id);
				$this->session->set_flashdata('message', 'Successfully Deleted.');
			}
			else
			{
				$this->session->set_flashdata('error', 'Could not Delete.');
			}

			redirect('cms/cms_comment/display');
		}

		/**
		 * Delete comment
		 */
		function delete_all()
		{
			if (count($this->input->post('chk_id')) > 0)
			{
				foreach ($this->input->post('chk_id') as $id)
				{
					$this->cms_comment_model->delete($id);
				}
				$this->session->set_flashdata('message', 'Successfully Deleted.');
			}
			else
			{
				$this->session->set_flashdata('error', 'Could not Delete.');
			}

			redirect('cms/cms_comment/display_trash');
		}

		/**
		 * Comment list
		 */
		function display()
		{
			$grid_params = array(
				'set_query' => $this->cms_comment_model->get_cms_comment(),

				'set_multirow_selection' => array(
					'enable' => true,
					'primary_key' => 'id'
				),

				'set_pagination' => array(
					'enable' => true,
					'initialize' => array(
						'base_url' => site_url('cms/cms_comment/display'),
						'per_page' => 25,
						'num_links' => 5
					),
					'order_field' => 'created',
					'order_type' => 'ASC',
					'offset' => 0
				),

				'set_column' => array(
					'fulltext_short' => array(
						'title' => 'Comment',
						'sortable' => true,
						'type' => array(
							'link',
							'href' => site_url('cms/cms_comment/edit/{id}')
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
					'author' => array(
						'title' => 'Author',
						'sortable' => true,
						'type' => array(
							'text'
						)
					),
					'author_email' => array(
						'title' => 'Email',
						'sortable' => true,
						'type' => array(
							'text'
						)
					),
					'cms_content' => array(
						'title' => '# Content',
						'sortable' => false,
						'type' => array(
							'text'
						)
					),
					'cms_type' => array(
						'title' => '# Category',
						'sortable' => false,
						'type' => array(
							'text'
						)
					),
					'id' => array(
						'title' => 'ID',
						'sortable' => true,
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
							'href' => site_url('cms/cms_comment/edit/{id}')
						),

						'delete' => array(
							'title' => 'Delete',
							'type' => array(
								'image',
								'src' => base_url() . 'public/images/administrator/theme/delete.png'
							),
							'href' => site_url('cms/cms_comment/delete/{id}'),
							'html_attribute' => array(
								'onclick' => "javascript:if(confirm('Sure Want to Delete Permanently ?')) return true; else return false; "
							)
						)
					)
				),

				'set_filter' => array(
					'enable' => true,
					array(
						'author' => array(
							'title' => 'Comment Author',
							'type' => array(
								'text'
							)
						),

						'author_email' => array(
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
			$this->ocular->set_view_data('page_title', 'Comment List');
			$this->ocular->render();
		}

		/**
		 * Display edit comment page
		 * @param int $id
		 */
		function edit($id = 0, $save = '')
		{
			// Check if record exist.
			$cms_comment = $this->cms_comment_model->cms_comment_by_cms_comment_id($id);

			if ($cms_comment->num_rows() === 0)
			{
				$this->session->set_userdata('error', 'Record not found it may have been delete, you can create new record.');
				redirect('cms/cms_comment/add', 'refresh');
			}

			// set the rule
			$this->_set_form_validation_rules('edit');

			// if validation ok, then update data otherwise don't
			if ($this->form_validation->run() === true)
			{
				$this->cms_comment_model->update();

				$this->session->set_userdata('message', 'Successfully updated.');

				if ($save !== 'apply')
				{
					redirect('cms/cms_comment/display', 'refresh');
				}
			}
			else
			{
				$this->session->set_flashdata('error', $this->form_validation->error_string());
			}

			$cms_comment = $this->cms_comment_model->cms_comment_by_cms_comment_id($id);
			$db          = $cms_comment->row_array();

			$this->ocular->set_view_data('db', $db);
			$this->ocular->set_view_data('page_title', 'Cms Comment Edit');
			$this->ocular->render();
		}

		/**
		 * Index page
		 */
		function index()
		{
			redirect('cms/cms_comment/display', 'refresh');
		}

		/**
		 * Publish comments
		 */
		function publish()
		{
			if (count($this->input->post('chk_id')) > 0)
			{
				foreach ($this->input->post('chk_id') as $id)
				{
					$this->cms_comment_model->update_by_cms_comment_id($id, array(
						'status' => '1'
					));
				}
				$this->session->set_flashdata('message', 'Successfully Updated.');
			}
			else
			{
				$this->session->set_flashdata('error', 'Could not Update.');
			}

			redirect('cms/cms_comment/display');
		}

		/**
		 * Unpublish comments
		 */
		function unpublish()
		{
			if (count($this->input->post('chk_id')) > 0)
			{
				foreach ($this->input->post('chk_id') as $id)
				{
					$this->cms_comment_model->update_by_cms_comment_id($id, array(
						'status' => '0'
					));
				}
				$this->session->set_flashdata('message', 'Successfully Updated.');
			}
			else
			{
				$this->session->set_flashdata('error', 'Could not Update.');
			}

			redirect('cms/cms_comment/display');
		}

		/**
		 * Update comment
		 *
		 * @param string $save
		 */
		function update($save = '')
		{
			// set the rule
			$config = array(
				array(
					'field' => 'cms_content_id',
					'label' => 'Article Id',
					'rules' => 'trim|required'
				),
				array(
					'field' => 'author',
					'label' => 'Author',
					'rules' => 'trim|required'
				),
				array(
					'field' => 'author_email',
					'label' => 'Email',
					'rules' => 'trim|required'
				)
			);

			$this->form_validation->set_rules($config);

			// if validation ok, then update data otherwise don't
			if ($this->form_validation->run() == false)
			{
				$this->session->set_flashdata('error', $this->form_validation->error_string());
				redirect('cms/cms_comment/edit/' . $_POST['id']);
			}
			else
			{
				$this->cms_comment_model->update();

				$this->session->set_flashdata('message', 'Successfully Updated.');
				if ($save === 'apply')
				{
					redirect('cms/cms_comment/edit/' . $_POST['id']);
				}
				else
				{
					redirect('cms/cms_comment/display');
				}
			}
		}
	}