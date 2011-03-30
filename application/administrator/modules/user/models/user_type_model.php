<?php
	class User_Type_Model extends CI_Model
	{
		var $user_type_id = '';

		function __construct()
		{
			parent::__construct();
		}

		function insert()
		{
			$arr = $this->field();
			$this->db->insert('user_type', $arr);
			return $this->db->insert_id();
		}

		function update()
		{
			$fields = $this->field();
			
			$this->db->update('user_type', $fields, array('user_type_id' => $this->user_type_id));
		}

		function delete($user_type_id)
		{
			$this->db->delete('user_type', array(
				'user_type_id' => $user_type_id
			));
		}

		function field()
		{
			$this->user_type_id = $this->input->post('user_type_id');

			$fields = array(
				'user_type_id' => $this->input->post('user_type_id'),
				'parent_id' => $this->input->post('parent_id'),
				'title' => $this->input->post('title'),
				'status' => $this->input->post('status'),
				'modified' => gmdate("Y-m-d H:m:s")
			);
			if ($fields['status'] === false)
				$fields['status'] = '0';

			return $fields;
		}

		function user_type($parent_id = 0)
		{
			return $this->db->query("select * from user_type where parent_id = '$parent_id'");
		}

		function get_user_type()
		{
			$user      = $this->db->dbprefix('user');
			$user_type = $this->db->dbprefix('user_type');
			$sql       = "SELECT
					ut.*,
						(select title from $user_type where $user_type.user_type_id = ut.parent_id) as parent,
						(select count(user_id) from $user where $user.user_type_id = ut.user_type_id and $user.status='1') as user_1,
						(select count(user_id) from $user where $user.user_type_id = ut.user_type_id and $user.status='0') as user_0,
						(select count(user_id) from $user where $user.user_type_id = ut.user_type_id and $user.status='trash') as user_trash,
						(select count(user_id) from $user where $user.user_type_id = ut.user_type_id and $user.status='verifying') as user_verifying
				FROM
					$user_type as ut
				";
			return $this->db->query($sql);			
		}

		function user_type_by_user_type_id($user_type_id)
		{
			return $this->db->get_where('user_type', array(
				'user_type_id' => $user_type_id
			));
		}

		function update_by_user_type_id($user_type_id, $arr)
		{
			return $this->db->update('user_type', $arr, array(
				'user_type_id' => $user_type_id
			));
		}
	}