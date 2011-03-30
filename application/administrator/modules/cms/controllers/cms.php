<?php
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Cms extends Admin_Controller
	{
		/**
		 * Constructor
		 */
		function __construct()
		{
			parent::__construct();

			$this->load->model('cms_comment_model');
			$this->load->model('cms_model');
			$this->load->model('cms_type_model');
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
		 * Add new content
		 */
		function add($save = '')
		{
			$this->_set_form_validation_rules('add');

			// if validation is ok then insert the data and unset the session, otherwise don't and redirect to source page
			if ($this->form_validation->run() === true)
			{
				if ($this->cms_model->insert() !== false)
				{
					$this->session->set_userdata('message', 'Successfully created.');

					// Empty out post data
					$_POST = array();

					if ($save !== 'apply')
					{
						redirect('cms/display', 'refresh');
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
			$db = $this->cms_model->field();

			// Initial value when post is not set
			if (count($_POST) === 0)
			{
				$db['status']   = 1;
				$db['metadata'] = 'index, follow';
				$db['created']  = gmdate("Y-m-d H:m:s");
			}

			$this->ocular->set_view_data('db', $db);
			$this->ocular->set_view_data('page_title', 'Add CMS Content');
			$this->ocular->render();
		}

		/**
		 * Delete content
		 *
		 * @param int $id
		 */
		function delete($id)
		{
			if ($id > 0)
			{
				$this->cms_model->delete($id);
				$this->session->set_flashdata('message', 'Successfully Deleted.');
			}
			else
			{
				$this->session->set_flashdata('error', 'Could not Delete.');
			}

			redirect('cms/display');
		}

		/**
		 * Delete all content data
		 */
		function delete_all()
		{
			if (count($this->input->post('chk_id')) > 0)
			{
				foreach ($this->input->post('chk_id') as $cms_content_id)
				{
					$this->cms_model->delete($cms_content_id);
				}
				$this->session->set_flashdata('message', 'Successfully Deleted.');
			}
			else
			{
				$this->session->set_flashdata('error', 'Could not Delete.');
			}

			redirect('cms/display_trash');
		}

		/**
		 * Content list
		 */
		function display()
		{
			$grid_params = array(
				'set_query' => $this->cms_model->get_cms(),

				'set_multirow_selection' => array(
					'enable' => true,
					'primary_key' => 'id'
				),

				'set_pagination' => array(
					'enable' => true,
					'initialize' => array(
						'base_url' => site_url('cms/display'),
						'per_page' => 10,
						'num_links' => 5
					),
					'order_field' => 'id',
					'order_type' => 'ASC',
					'offset' => 0
				),

				'set_column' => array(
					'title_en' => array(
						'title' => 'Title (English)',
						'sortable' => true,
						'type' => array(
							'link',
							'href' => site_url('cms/edit/{id}')
						)
					),

					'ordering' => array(
						'title' => 'Order',
						'sortable' => true,
						'type' => array(
							'textfield',
							'table_name' => 'cms_content',
							'primary_key' => 'id',
							'html_attribute' => array(
								'size' => 5
							)
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

					'cms_type' => array(
						'title' => 'Type',
						'sortable' => false,
						'type' => array(
							'text'
						)
					),

					'comment' => array(
						'title' => '# Comments',
						'sortable' => false,
						'type' => array(
							'text'
						)
					),

					'hits' => array(
						'title' => '# Hits',
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
							'href' => site_url('cms/edit/{id}')
						),

						'delete' => array(
							'title' => 'Delete',
							'type' => array(
								'image',
								'src' => base_url() . 'public/images/administrator/theme/delete.png'
							),
							'href' => site_url('cms/delete/{id}'),
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
							'title' => 'Title (English)',
							'type' => array(
								'text'
							)
						),
						'id' => array(
							'title' => 'ID',
							'type' => array(
								'number'
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
		 * Display content set for deletion
		 */
		function display_trash()
		{
			$grid_params = array(
				'set_query' => $this->cms_model->get_cms_trash(),

				'set_multirow_selection' => array(
					'enable' => true,
					'primary_key' => 'id'
				),

				'set_pagination' => array(
					'enable' => true,
					'initialize' => array(
						'base_url' => site_url('cms/display'),
						'per_page' => 10,
						'num_links' => 5
					),
					'order_field' => 'cms_type_id',
					'order_type' => 'ASC',
					'offset' => 0
				),

				'set_column' => array(
					'title_en' => array(
						'title' => 'Title',
						'sortable' => true,
						'type' => array(
							'link',
							'href' => site_url('cms/edit/{id}')
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
							'text',
							'table_name' => 'cms_content',
							'primary_key' => 'id'
						)
					),

					'cms_type' => array(
						'title' => 'Type',
						'sortable' => false,
						'type' => array(
							'text'
						)
					),

					'comment' => array(
						'title' => '# Comments',
						'sortable' => false,
						'type' => array(
							'text'
						)
					),

					'hits' => array(
						'title' => '# Hits',
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
					'enable' => false,
					array(
						'edit' => array(
							'title' => 'Edit',
							'type' => array(
								'image',
								'src' => base_url() . 'public/images/administrator/theme/edit.png'
							),
							'href' => site_url('cms/edit/{id}')
						),

						'delete' => array(
							'title' => 'Delete',
							'type' => array(
								'image',
								'src' => base_url() . 'public/images/administrator/theme/delete.png'
							),
							'href' => site_url('cms/delete/{id}'),
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
						),
						'id' => array(
							'title' => 'ID',
							'type' => array(
								'number'
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
			$this->ocular->set_view_data('page_title', 'Content Trash List');
			$this->ocular->render();
		}

		/**
		 * Edit the content
		 *
		 * @param int $cms_content_id
		 */
		function edit($cms_content_id = 0, $save = '')
		{
			// Check if record exist.
			$cms = $this->cms_model->cms_content_by_cms_content_id($cms_content_id);

			if ($cms->num_rows() === 0)
			{
				$this->session->set_userdata('error', 'Record not found it may have been delete, you can create new record.');
				redirect('cms/add', 'refresh');
			}

			// set the rule
			$this->_set_form_validation_rules('edit');

			// if validation ok, then update data otherwise don't
			if ($this->form_validation->run() === true)
			{
				$this->cms_model->update();

				$this->session->set_userdata('message', 'Successfully updated.');

				if ($save !== 'apply')
				{
					redirect('cms/display', 'refresh');
				}
			}
			else
			{
				$this->session->set_flashdata('error', $this->form_validation->error_string());
			}

			$cms = $this->cms_model->cms_content_by_cms_content_id($cms_content_id);
			$db  = $cms->row_array();

			// Reformating publish up and publish down display
			if ($db['publish_up'] === '0000-00-00 00:00:00')
			{
				$db['publish_up'] = '';
			}

			if ($db['publish_down'] === '0000-00-00 00:00:00')
			{
				$db['publish_down'] = '';
			}

			$this->ocular->set_view_data('db', $db);
			$this->ocular->set_view_data('page_title', 'Cms Content Edit');
			$this->ocular->render();
		}

		/**
		 * Index page
		 */
		function index()
		{
			redirect('cms/display', 'refresh');
		}

		/**
		 * Check if content with particular title already exist
		 *
		 * @param string $title
		 * @return bool
		 */
		function is_title_exist($title)
		{
			$query = $this->cms_model->cms_content_by_title($title);
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
		 * Publish all data
		 */
		function publish()
		{
			if (count($this->input->post('chk_id')) > 0)
			{
				foreach ($this->input->post('chk_id') as $cms_content_id)
				{
					$this->cms_model->update_by_cms_content_id($cms_content_id, array(
						'status' => '1'
					));
				}
				$this->session->set_flashdata('message', 'Successfully Updated.');
			}
			else
			{
				$this->session->set_flashdata('error', 'Could not Update.');
			}

			redirect('cms/display');
		}

		/**
		 * Unmark data for deletion
		 */
		function restore()
		{
			$this->publish();
		}

		/**
		 * Mark data for deletion
		 */
		function trash()
		{
			if (count($this->input->post('chk_id')) > 0)
			{
				foreach ($this->input->post('chk_id') as $cms_content_id)
				{
					$this->cms_model->update_by_cms_content_id($cms_content_id, array(
						'status' => 'trash'
					));
				}
				$this->session->set_flashdata('message', 'Successfully Updated.');
			}
			else
			{
				$this->session->set_flashdata('error', 'Could not Update.');
			}

			redirect('cms/display');
		}

		/**
		 * Unpublish all data
		 */
		function unpublish()
		{
			if (count($this->input->post('chk_id')) > 0)
			{
				foreach ($this->input->post('chk_id') as $cms_content_id)
				{
					$this->cms_model->update_by_cms_content_id($cms_content_id, array(
						'status' => '0'
					));
				}
				$this->session->set_flashdata('message', 'Successfully Updated.');
			}
			else
			{
				$this->session->set_flashdata('error', 'Could not Update.');
			}

			redirect('cms/display');
		}

		/**
		 * Update image
		 *
		 * @param int $id
		 */
		function update_image($id)
		{
			$query = $this->cms_model->cms_content_by_cms_content_id($id);

			if ($query->num_rows() === 1)
			{
				if ($this->cms_model->delete_image($id))
				{
					echo "Image Removed Successfully. ";
				}
				else
				{
					echo "Error found, File doesn't exist.";
				}
			}
			else
			{
				echo "Error found trying to remove image. ";
			}
			die();
		}
	}