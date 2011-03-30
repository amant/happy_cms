<?php
	class User_Model extends CI_Model
	{
		public $user_id = '';

		function __construct()
		{
			parent::__construct();
			$this->CI = get_instance();
		}

		function insert()
		{
			$fields                     = $this->field();
			$fields['created']          = gmdate("Y-m-d H:m:s");

			$this->db->insert('user', $fields);
			return $this->db->insert_id();
		}

		function update()
		{
			$fields = $this->field();
			
			$this->db->update('user', $fields, array('user_id' => $this->user_id));
		}

		function delete($user_id)
		{
			$this->db->delete('user', array(
				'user_id' => $user_id
			));
		}

		function field()
		{
			$this->user_id = $this->input->post('user_id');

			$fields = array(
				'user_id' => $this->input->post('user_id'),
				'user_type_id' => $this->input->post('user_type_id'),
				'password' => $this->input->post('password'),
				'email' => $this->input->post('email'),
				'first_name' => $this->input->post('first_name'),
				'middle_name' => $this->input->post('middle_name'),
				'last_name' => $this->input->post('last_name'),
				'modified' => gmdate("Y-m-d H:m:s"),
				'status' => $this->input->post('status')
			);

			$fields['password'] = encrypt($fields['password'], $fields['email']);

			if ($fields['status'] === false)
				$fields['status'] = '0';

			return $fields;
		}

		function user()
		{
			$this->db->order_by('user_email');
			return $this->db->get('user');
		}

		function user_by_user_id($user_id)
		{
			return $this->db->get_where('user', array(
				"user_id" => $user_id
			));
		}

		function user_by_user_email($email)
		{
			$query = $this->db->get_where('user', array(
				'email' => $email
			));
			return $query;
		}

		function user_by_user_login($user_login)
		{
			$query = $this->db->get_where('user', array(
				'user_login' => $user_login
			));
			return $query;
		}

		function get_users()
		{
			$user      = $this->db->dbprefix('user');
			$user_type = $this->db->dbprefix('user_type');

			$sql = "SELECT
					$user.*, $user_type.title as user_type
				FROM
					$user,
					$user_type
				WHERE
					$user.user_type_id = $user_type.user_type_id and
					$user.status != 'trash'
				";

			return $this->db->query($sql);			
		}

		function get_users_trash()
		{
			return $this->db->get_where('user', array(
				'status' => 'trash'
			));
		}

		function update_by_user_id($user_id, $arr)
		{
			$this->db->update('user', $arr, array(
				'user_id' => $user_id
			));
		}
	}