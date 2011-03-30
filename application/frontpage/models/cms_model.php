<?php

	class Cms_model extends CI_Model
	{
		/**
		 * Constructor
		 */
		function __construct()
		{
			parent::__construct();
		}

		function cms_type_by_id($id)
		{
			return $this->db->get_where('cms_type', array(
				'id' => $id
			));
		}

		function content_type_by_title($title)
		{
			return $this->db->get_where('cms_type', array(
				'title' => $title
			));
		}

		function content_type_list()
		{
			return $this->db->get_where('cms_type', array(
				'status' => '1'
			));
		}


		function content_by_id($id)
		{
			return $this->db->get_where('cms_content', array(
				'cms_content_id' => $id
			));
		}

		function content_by_title($title)
		{
			return $this->db->get_where('cms_content', array(
				'alias' => $title
			));
		}

		function content_by_content_type($type_id)
		{
			$this->db->order_by('ordering');
			return $this->db->get_where('cms_content', array(
				'cms_type_id' => $type_id
			));
		}

		function content_list()
		{
			return $this->db->get_where('cms_content', array(
				'status' => '1'
			));
		}

		function content_by_content_type_title($title, $limit = 30)
		{
			$content      = $this->db->dbprefix('cms_content');
			$content_type = $this->db->dbprefix('cms_type');

			$sql = "SELECT
						$content.*
					FROM
						$content,
						$content_type
					WHERE
						$content_type.title = '$title' AND
						$content.cms_type_id = $content_type.id AND
						$content.status = '1' AND
						$content_type.status = '1'
					ORDER BY
						$content.ordering
					LIMIT
						0, $limit
					";
			return $this->db->query($sql);
		}

		function content_by_content_type_title_frontpage($title, $limit = 3)
		{
			$content      = $this->db->dbprefix('cms_content');
			$content_type = $this->db->dbprefix('cms_type');

			$sql = "SELECT
						$content.*
					FROM
						$content,
						$content_type
					WHERE
						$content_type.title = '$title' AND
						$content.cms_type_id = $content_type.id AND
						$content.status = '1' AND
						$content_type.status = '1' AND
						$content.frontpage = 1
					ORDER BY
						$content.ordering
					LIMIT
						0, $limit
					";
			return $this->db->query($sql);
		}

		function content_by_id_and_title($id, $title)
		{
			return $this->db->get_where('cms_content', array(
				'cms_content_id' => $id,
				'title' => $title,
				'status' => '1'
			));
		}
	}