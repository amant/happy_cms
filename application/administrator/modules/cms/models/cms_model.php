<?php
	class Cms_Model extends CI_Model
	{
		public $id = '';
		private $upload_path = './public/images/cms/';

		/**
		 * constructor
		 */
		function __construct()
		{
			parent::__construct();
		}

		/**
		 * Return cms content by id
		 *
		 * @param int $id
		 * @return object
		 */
		function cms_content_by_cms_content_id($id)
		{
			return $this->db->get_where('cms_content', array(
				'id' => $id
			));
		}

		/**
		 * Return content by it's category type
		 *
		 * @return object
		 */
		function cms_content_by_cms_type_id()
		{
			$cms_content = $this->db->dbprefix('cms_content');
			$cms_type    = $this->db->dbprefix('cms_type');
			$sql         = "SELECT
									$cms_content.*,
									(select title_en from $cms_type where $cms_type.id = $cms_content.cms_type_id) as cms_type
								FROM
									$cms_content
								ORDER BY
									cms_type_id
							";
			return $this->db->query($sql);
		}

		/**
		 * Return content by title
		 *
		 * @param string $title
		 * @return object
		 */
		function cms_content_by_title($title)
		{
			return $this->db->get_where('cms_content', array(
				'title_en' => $title
			));
		}

		/**
		 * Delete data
		 * @param int $id
		 */
		function delete($id)
		{
			$this->db->delete('cms_content', array(
				'id' => $id
			));
		}

		/**
		 * Delete image
		 *
		 * @param int $id
		 * @return bool
		 */
		function delete_image($id)
		{
			$row = $this->cms_content_by_cms_content_id($id)->row();

			// Delete images
			$file_path = $this->upload_path;
			if ($row->image !== '')
			{
				if (file_exists($file_path . $row->image))
				{
					unlink($file_path . $row->image);
				}

				if (file_exists($file_path . 'big/' . $row->image))
				{
					unlink($file_path . 'big/' . $row->image);
				}

				if (file_exists($file_path . 'thumb/' . $row->image))
				{
					unlink($file_path . 'thumb/' . $row->image);
				}

				$data = array(
					'image' => ''
				);
				$this->update_by_cms_content_id($id, $data);
				return true;
			}
			else
			{
				return false;
			}
		}

		/**
		 * Resize the image
		 *
		 * @param string $path
		 */
		function do_resize($path)
		{
			$this->load->library('image_lib');

			$config['image_library']  = 'gd2';
			$config['source_image']   = $path;
			$config['maintain_ratio'] = TRUE;

			// create thumbnail

			$config['new_image'] = $this->upload_path . 'thumb';
			$config['width']     = 128;
			$config['height']    = 128;

			$this->image_lib->initialize($config);

			if (!$this->image_lib->resize())
			{
				$this->session->set_flashdata('error', $this->image_lib->display_errors());
			}
			// create big image

			$this->image_lib->clear();

			$config['new_image'] = $this->upload_path . 'big';
			$config['width']     = 600;
			$config['height']    = 400;
			$this->image_lib->initialize($config);

			if (!$this->image_lib->resize())
			{
				$this->session->set_flashdata('error', $this->image_lib->display_errors());
			}
		}

		/**
		 * Move uploaded image to target location
		 *
		 * @param string $image_field
		 * @return array
		 */
		function do_upload($image_field)
		{
			$config['upload_path']   = $this->upload_path;
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size']      = '2048';
			$config['max_width']     = '0';
			$config['max_height']    = '0';
			$config['remove_spaces'] = true;
			$config['encrypt_name']  = true;

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload($image_field))
			{
				$this->session->set_flashdata('error', $this->upload->display_errors());
				return false;
			}
			else
			{
				return $this->upload->data();
			}
		}

		/**
		 * Return field collector
		 * @return object
		 */
		function field()
		{
			$this->id = $this->input->post('id');

			$fields = array(
				'id' => $this->input->post('id'),
				'cms_type_id' => $this->input->post('cms_type_id'),
				'alias' =>  url_title(strtolower($this->input->post('title_en'))),
				'title_en' => $this->input->post('title_en'),
				'fulltext_en' => $this->input->post('fulltext_en'),
				'title_de' => $this->input->post('title_de'),
				'fulltext_de' => $this->input->post('fulltext_de'),
				'title_it' => $this->input->post('title_it'),
				'fulltext_it' => $this->input->post('fulltext_it'),
				'title_ru' => $this->input->post('title_ru'),
				'fulltext_ru' => $this->input->post('fulltext_ru'),
				'image' => $this->input->post('image'),
				'metakey' => $this->input->post('metakey'),
				'metadesc' => $this->input->post('metadesc'),
				'metadata' => $this->input->post('metadata'),
				'publish_up' => $this->input->post('publish_up'),
				'publish_down' => $this->input->post('publish_down'),
				'hits' => $this->input->post('hits'),
				'modified' => gmdate("Y-m-d H:m:s"),
				'modified_by' => $this->session->userdata('user_id'),
				'status' => $this->input->post('status')
			);
			if ($fields['status'] === false)
				$fields['status'] = '0';

			return $fields;
		}

		/**
		 * Return cms content data
		 * @return object
		 */
		function get_cms()
		{
			$cms         = $this->db->dbprefix('cms_content');
			$cms_type    = $this->db->dbprefix('cms_type');
			$cms_comment = $this->db->dbprefix('cms_comment');

			$sql = "SELECT
						*,
						(select title_en from $cms_type where $cms_type.id = $cms.cms_type_id) as cms_type,
						(SELECT count(id) FROM $cms_comment WHERE $cms_comment.cms_content_id = $cms.id) as comment
					FROM
						$cms
					WHERE
						status!='trash'
					";

			return $this->db->query($sql);
		}

		/**
		 * Return the ordering number of the content article
		 *
		 * @param int $cms_type_id
		 * @return int
		 */
		function get_cms_ordering($cms_type_id)
		{
			$cms   = $this->db->dbprefix('cms_content');
			$sql   = "SELECT MAX(ordering) AS ordering FROM $cms WHERE $cms.cms_type_id = '$cms_type_id'";
			$query = $this->db->query($sql);
			$row   = $query->row();
			return ($row->ordering + 1);
		}

		/**
		 * Reutrn cms trash data
		 * @return object
		 */
		function get_cms_trash()
		{
			$cms         = $this->db->dbprefix('cms_content');
			$cms_type    = $this->db->dbprefix('cms_type');
			$cms_comment = $this->db->dbprefix('cms_comment');
			$user_type   = $this->db->dbprefix('user_type');

			$sql = "SELECT
						*,
						(select title_en from $cms_type where $cms_type.id = $cms.cms_type_id) as cms_type,
						(SELECT count(id) FROM $cms_comment WHERE $cms_comment.cms_content_id = $cms.id) as comment
					FROM
						$cms
					WHERE
						status='trash'
					";

			return $this->db->query($sql);
		}

		/**
		 * Insert data
		 *
		 * @return object
		 */
		function insert()
		{
			$fields               = $this->field();
			$fields['created']    = gmdate("Y-m-d H:m:s");
			$fields['created_by'] = $this->session->userdata('user_id');
			$fields['ordering']   = $this->get_cms_ordering($fields['cms_type_id']);

			$fields['image'] = '';
			if (!$_FILES['image']['name'] == '')
			{
				$image = $this->upload_image();
				if ($image !== false)
					$fields['image'] = $image;
			}

			$this->db->insert('cms_content', $fields);
			return $this->db->insert_id();
		}

		/**
		 * Update data
		 */
		function update()
		{
			$fields = $this->field();

			$image = $this->upload_image();
			if ($image !== false)
			{
				$fields['image'] = $image;
			}
			else
			{
				unset($fields['image']);
			}

			$this->db->update('cms_content', $fields, array('id' => $this->id));
		}

		/**
		 * Update content by content id
		 * @param int $id
		 * @param array $data
		 */
		function update_by_cms_content_id($id, array $data)
		{
			$this->db->update('cms_content', $data, array(
				'id' => $id
			));
		}

		/**
		 * Upload an image
		 *
		 * @return string
		 */
		function upload_image()
		{
			$image_field = 'image';
			$filename    = false;

			if (isset($_FILES[$image_field]) && $_FILES[$image_field]['name'] != '')
			{
				$file = $this->do_upload($image_field);

				if ($file !== false)
				{
					$filename = $file['raw_name'] . $file['file_ext'];
					$this->do_resize($file['full_path']);
				}
			}
			return $filename;
		}
	}