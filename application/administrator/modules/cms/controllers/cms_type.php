<?php
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Cms_Type extends Admin_Controller
	{
		/**
		 * Constructor
		 */
		function __construct()
		{
			parent::__construct();

			$this->load->model('cms_model');
			$this->load->model('cms_type_model');
			$this->load->model('cms_comment_model');
			$this->load->model('application_model', 'application');

			$this->load->library('form_validation');
			$this->load->library('ca_gridview');

			$this->load->helper('cms');
		}

		/**
		 * Set form rules
		 */
		function _set_form_validation_rules($form)
		{
			if ($form === 'add')
			{
				$config = array(
					array(
						'field' => 'title_en',
						'label' => 'Title (English)',
						'rules' => 'trim|required|callback_is_title_exist'
					)
				);
			}
			elseif ($form === 'edit')
			{
				$config = array(
					array(
						'field' => 'title_en',
						'label' => 'Title (English)',
						'rules' => 'trim|required'
					)
				);
			}
			$this->form_validation->set_rules($config);
		}

		/**
		 * Add page
		 */
		function add($save = '')
		{
			$this->_set_form_validation_rules('add');

			// if validation is ok then insert the data and unset the session, otherwise don't and redirect to source page
			if ($this->form_validation->run() === true)
			{
				if ($this->cms_type_model->insert() !== false)
				{
					$this->session->set_userdata('message', 'Successfully created.');

					// Empty out post data
					$_POST = array();

					if ($save !== 'apply')
					{
						redirect('cms/cms_type/display', 'refresh');
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
			$db = $this->cms_type_model->field();

			// Initial value when post is not set
			if (count($_POST) === 0)
			{
				$db['status']   = 1;
				$db['metadata'] = 'index, follow';
				$db['created']  = gmdate("Y-m-d H:m:s");
			}

			$this->ocular->set_view_data('db', $db);
			$this->ocular->set_view_data('page_title', 'Add CMS Type');
			$this->ocular->render();
		}

		/**
		 * Delete data
		 *
		 * @param int $cms_type_id
		 */
		function delete($cms_type_id)
		{
			if ($cms_type_id > 0)
			{
				$this->cms_type_model->delete($cms_type_id);
				$this->session->set_flashdata('message', 'Successfully Deleted.');
			}
			else
			{
				$this->session->set_flashdata('error', 'Could not Delete.');
			}

			redirect('cms/cms_type/display', 'refresh');
		}

		/**
		 * Display list
		 */
		function display()
		{
			$grid_params = array(
				'set_query' => $this->cms_type_model->get_cms_type(),

				'set_multirow_selection' => array(
					'enable' => true,
					'primary_key' => 'id'
				),

				'set_pagination' => array(
					'enable' => true,
					'initialize' => array(
						'base_url' => site_url('cms/cms_type/display'),
						'per_page' => 25,
						'num_links' => 5
					),
					'order_field' => 'parent_id',
					'order_type' => 'ASC',
					'offset' => 0
				),

				'set_column' => array(
					'title_en' => array(
						'title' => 'Title (English)',
						'sortable' => true,
						'type' => array(
							'link',
							'href' => site_url('cms/cms_type/edit/{id}')
						)
					),
					'parent' => array(
						'title' => 'Parent',
						'sortable' => false,
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

					'ordering' => array(
						'title' => 'Order',
						'sortable' => false,
						'type' => array(
							'textfield',
							'table_name' => 'cms_type',
							'primary_key' => 'id'
						)
					),

					//					'hits' => array(
					//						'title' => '# Hits',
					//						'sortable' => false,
					//						'type' => array(
					//							'text'
					//						)
					//					),
					//
					//					'status_1' => array(
					//						'title' => '# Publish',
					//						'sortable' => false,
					//						'type' => array(
					//							'text'
					//						)
					//					),
					//
					//					'status_0' => array(
					//						'title' => '# UnPublish',
					//						'sortable' => false,
					//						'type' => array(
					//							'text'
					//						)
					//					),

					//					'status_trash' => array(
					//						'title' => '# Trash',
					//						'sortable' => false,
					//						'type' => array(
					//							'text'
					//						)
					//					),

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
							'href' => site_url('cms/cms_type/edit/{id}')
						),

						'delete' => array(
							'title' => 'Delete',
							'type' => array(
								'image',
								'src' => base_url() . 'public/images/administrator/theme/delete.png'
							),
							'href' => site_url('cms/cms_type/delete/{id}'),
							'html_attribute' => array(
								'onclick' => "javascript:if(confirm('Sure Want to Delete Permanently ?')) return true; else return false; "
							)
						)
					)
				),

				'set_filter' => array(
					'enable' => true,
					array(
						'title_en' => array(
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
			$this->ocular->set_view_data('page_title', 'Content List');
			$this->ocular->render();
		}

		/**
		 * Edit page
		 *
		 * @param int $cms_type_id
		 */
		function edit($cms_type_id = 0, $save = '')
		{
			// Check if record exist.
			$cms_type = $this->cms_type_model->cms_type_by_cms_type_id($cms_type_id);

			if ($cms_type->num_rows() === 0)
			{
				$this->session->set_userdata('error', 'Record not found it may have been delete, you can create new record.');
				redirect('cms/cms_type/add', 'refresh');
			}

			// set the rule
			$this->_set_form_validation_rules('edit');

			// if validation ok, then update data otherwise don't
			if ($this->form_validation->run() === true)
			{
				$this->cms_type_model->update();

				$this->session->set_userdata('message', 'Successfully updated.');

				if ($save !== 'apply')
				{
					redirect('cms/cms_type/display', 'refresh');
				}
			}
			else
			{
				$this->session->set_flashdata('error', $this->form_validation->error_string());
			}

			$cms_type = $this->cms_type_model->cms_type_by_cms_type_id($cms_type_id);
			$db       = $cms_type->row_array();

			$this->ocular->set_view_data('db', $db);
			$this->ocular->set_view_data('page_title', 'Cms Content Edit');
			$this->ocular->render();
		}

		/**
		 * Index page
		 */
		function index()
		{
			redirect('cms/cms_type/display', 'refresh');
		}

		/**
		 * Check if title exist
		 *
		 * @param string $title
		 * @return bool
		 */
		function is_title_exist($title)
		{
			$query = $this->cms_type_model->cms_type_by_title($title);
			if ($query->num_rows > 0)
			{
				$this->form_validation->set_message('is_title_exist', 'Title ' . $title . ', already exist. Please use another title.');
				return false;
			}
			else
			{
				return true;
			}
		}

		/**
		 * Upblish data
		 */
		function publish()
		{
			if (count($this->input->post('chk_id')) > 0)
			{
				foreach ($this->input->post('chk_id') as $cms_type_id)
				{
					$this->cms_type_model->update_by_cms_type_id($cms_type_id, array(
						'status' => '1'
					));
				}
				$this->session->set_flashdata('message', 'Successfully Updated.');
			}
			else
			{
				$this->session->set_flashdata('error', 'Could not Update.');
			}

			redirect('cms/cms_type/display', 'refresh');
		}

		/**
		 * Unpublish data
		 */
		function unpublish()
		{
			if (count($this->input->post('chk_id')) > 0)
			{
				foreach ($this->input->post('chk_id') as $cms_type_id)
				{
					$this->cms_type_model->update_by_cms_type_id($cms_type_id, array(
						'status' => '0'
					));
				}
				$this->session->set_flashdata('message', 'Successfully Updated.');
			}
			else
			{
				$this->session->set_flashdata('error', 'Could not Update.');
			}

			redirect('cms/cms_type/display', 'refresh');
		}

		/**
		 * Update image
		 *
		 * @param int $id
		 */
		function update_image($id)
		{
			if ($this->cms_type_model->delete_image($id))
			{
				echo "Image Removed Successfully. ";
			}
			else
			{
				echo "Error found, File doesn't exist.";
			}
			die();
		}
	}