<?php
	// TODO: simplify legacy code with update() and use $this->db->update() instead of manual sql.
	
	class Cms_comment_Model extends CI_Model
	{
		public $id = '';

		function __construct()
		{
			parent::__construct();
		}

		/**
		 * Return comment by comment id
		 *
		 * @param int $id
		 * @return object
		 */
		function cms_comment_by_cms_comment_id($id)
		{
			return $this->db->get_where('cms_comment', array(
				'id' => $id
			));
		}

		/**
		 * Reutrn comment by title
		 *
		 * @param string $title
		 * @return object
		 */
		function cms_comment_by_title($title)
		{
			return $this->db->get_where('cms_comment', array(
				'title' => $title
			));
		}

		/**
		 * Delete db
		 *
		 * @param int $id
		 */
		function delete($id)
		{
			$this->db->delete('cms_comment', array(
				'id' => $id
			));
		}

		/**
		 * Collects field db
		 *
		 * @return array
		 */
		function field()
		{
			$this->id = $this->input->post('id');

			$fields = array(
				'id' => $this->input->post('id'),
				'cms_content_id' => $this->input->post('cms_content_id'),				
				'fulltext' => $this->input->post('fulltext'),
				'author' => $this->input->post('author'),
				'author_email' => $this->input->post('author_email'),
				'author_url' => $this->input->post('author_url'),
				'author_ip' => $this->input->post('author_ip'),
				'modified' => gmdate("Y-m-d H:m:s"),
				'modified_by' => $this->session->userdata('user_id'),
				'hits' => $this->input->post('hits'),
				'status' => $this->input->post('status')
			);

			if ($fields['status'] === false)
			{
				$fields['status'] = '0';
			}

			return $fields;
		}

		/**
		 * Return comments data
		 *
		 * @return object
		 */
		function get_cms_comment()
		{
			$cms_content = $this->db->dbprefix('cms_content');
			$cms_comment = $this->db->dbprefix('cms_comment');
			$cms_type    = $this->db->dbprefix('cms_type');

			$sql = "
        		SELECT
					$cms_comment.*,
					CONCAT(SUBSTRING($cms_comment.fulltext, 1, 200), '...') as fulltext_short,
					(SELECT
						CONCAT(SUBSTRING(title_en, 1, 100), '...') as title_en
					FROM
						$cms_content
					WHERE
						$cms_content.id = $cms_comment.cms_content_id
					) AS cms_content,
					(SELECT
						$cms_type.title_en
					FROM
						$cms_type, $cms_content
					WHERE
						$cms_type.id = $cms_content.cms_type_id AND
						$cms_content.id = $cms_comment.cms_content_id
					) as cms_type
					FROM
				$cms_comment
			";
			return $this->db->query($sql);
		}

		/**
		 * Insert data to db
		 * @return int
		 */
		function insert()
		{
			$fields               = $this->field();
			$fields['created']    = gmdate("Y-m-d H:m:s");
			$fields['created_by'] = $this->session->userdata('user_id');

			$this->db->insert('cms_comment', $fields);
			return $this->db->insert_id();
		}

		/**
		 * Update data to db
		 */
		function update()
		{			
			$fields = $this->field();
			
			$this->db->update('cms_comment', $fields, array('id' => $this->id));
		}

		/**
		 * Update comment by id
		 * @param int $id
		 * @param array $data
		 */
		function update_by_cms_comment_id($id, array $data)
		{
			$this->db->update('cms_comment', $data, array(
				'id' => $id
			));
		}
	}