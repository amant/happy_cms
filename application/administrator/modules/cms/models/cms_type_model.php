<?php
	class Cms_Type_Model extends CI_Model
	{
		public $id = '';
		private $upload_path = './public/images/cms_type/';

		function __construct()
		{
			parent::__construct();
		}

		/**
		 * Return cms type by it's id
		 *
		 * @param int $cms_type_id
		 * @return object
		 */
		function cms_type_by_cms_type_id($cms_type_id)
		{
			return $this->db->get_where('cms_type', array(
				'id' => $cms_type_id
			));
		}

		/**
		 * Reuturn all the cms type by it's parent id
		 *
		 * @param int $parent_id
		 * @return object
		 */
		function cms_type_by_parent_id($parent_id)
		{
			return $this->db->get_where('cms_type', array(
				'parent_id' => $parent_id
			));
		}

		/**
		 * Return all cms type by it's title
		 * @param <type> $title
		 * @return <type>
		 */
		function cms_type_by_title($title)
		{
			return $this->db->get_where('cms_type', array(
				'title_en' => $title
			));
		}

		/**
		 * Delete db record
		 *
		 * @param int$cms_type_id
		 */
		function delete($cms_type_id)
		{
			$this->db->delete('cms_type', array(
				'id' => $cms_type_id
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
			$row = $this->cms_type_by_cms_type_id($id)->row();

			// Delete images
			$file_path = $this->upload_path;
			if($row->image !== '')
			{
				if(file_exists($file_path . $row->image))
				{
					unlink($file_path . $row->image);
				}

				if(file_exists($file_path . 'big/' . $row->image))
				{
					unlink($file_path . 'big/' . $row->image);
				}

				if(file_exists($file_path . 'thumb/' . $row->image))
				{
					unlink($file_path . 'thumb/' . $row->image);
				}

				$data = array('image' => '');
				$this->update_by_cms_type_id($id, $data);
				return true;
			}
			else
			{
				return false;
			}
		}

		/**
		 * Crop and resize the image
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
			$config['width']     = 150;
			$config['height']    = 150;

			$this->image_lib->initialize($config);

			if (!$this->image_lib->resize())
			{
				$this->session->set_flashdata('error', $this->image_lib->display_errors());
			}

			$this->image_lib->clear();

			// create big image
			$config['new_image'] = $this->upload_path . 'big';
			$config['width']     = 400;
			$config['height']    = 400;
			$this->image_lib->initialize($config);

			if (!$this->image_lib->resize())
			{
				$this->session->set_flashdata('error', $this->image_lib->display_errors());
			}
		}

		/**
		 * Move uploaded file to desired path
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
		 * Field data collector
		 *
		 * @return array
		 */
		function field()
		{
			$this->id = $this->input->post('id');

			$fields = array(
				'id' => $this->input->post('id'),
				'parent_id' => $this->input->post('parent_id'),
				'image' => $this->input->post('image'),
				'alias' => url_title(strtolower($this->input->post('title_en'))),
				'title_en' => $this->input->post('title_en'),
				'fulltext_en' => $this->input->post('fulltext_en'),
				'title_de' => $this->input->post('title_de'),
				'fulltext_de' => $this->input->post('fulltext_de'),
				'title_it' => $this->input->post('title_it'),
				'fulltext_it' => $this->input->post('fulltext_it'),
				'title_ru' => $this->input->post('title_ru'),
				'fulltext_ru' => $this->input->post('fulltext_ru'),
				'metakey' => $this->input->post('metakey'),
				'metadesc' => $this->input->post('metadesc'),
				'metadata' => $this->input->post('metadata'),
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
		 * Reutrun all the cms types
		 *
		 * @return object
		 */
		function get_cms_type()
		{
			$cms       = $this->db->dbprefix('cms_content');
			$cms_type  = $this->db->dbprefix('cms_type');

			$sql = "SELECT
					$cms_type.*,
					(select title_en from $cms_type as ct where $cms_type.parent_id = ct.id) as parent,
					(SELECT COUNT($cms.id) FROM $cms WHERE $cms.cms_type_id = $cms_type.id AND $cms.status='0' ) as status_0,
					(SELECT COUNT($cms.id) FROM $cms WHERE $cms.cms_type_id = $cms_type.id AND $cms.status='1' ) as status_1,
					(SELECT COUNT($cms.id) FROM $cms WHERE $cms.cms_type_id = $cms_type.id AND $cms.status='trash' ) as status_trash
				FROM
					$cms_type
				";

			return $this->db->query($sql);
		}

		/**
		 * Return cms type's ordering number
		 *
		 * @param int $parent_id
		 * @return int
		 */
		function get_cms_type_ordering($parent_id)
		{
			$cms_type = $this->db->dbprefix('cms_type');
			$sql      = "select max(ordering) as ordering from $cms_type where $cms_type.parent_id = '$parent_id'";
			$query    = $this->db->query($sql);
			$row      = $query->row();
			return ($row->ordering + 1);
		}

		/**
		 * Insert data
		 * @return int
		 */
		function insert()
		{
			$fields                     = $this->field();
			$fields['created']          = gmdate("Y-m-d H:m:s");
			$fields['created_by']       = $this->session->userdata('user_id');
			$fields['ordering']         = $this->get_cms_type_ordering($fields['parent_id']);

			$fields['image'] = '';
			if (!$_FILES['image']['name'] == '')
			{
				$image = $this->upload_image();
				if ($image !== false)
					$fields['image'] = $image;
			}

			$this->db->insert('cms_type', $fields);
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

			$this->db->update('cms_type', $fields, array('id' => $this->id));
		}

		/**
		 * Update cms type by id
		 *
		 * @param int $cms_type_id
		 * @param array $data
		 * @return int
		 */
		function update_by_cms_type_id($cms_type_id, array $data)
		{
			$this->db->update('cms_type', $data, array(
				'id' => $cms_type_id
			));

			return $this->db->affected_rows();
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