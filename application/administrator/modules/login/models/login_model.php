<?php
	class Login_Model extends CI_Model
	{
		function __construct()
		{
			parent::__construct();
		}

		/**
		 * Validates username and password against db
		 * 
		 * @param string $username
		 * @param string $password
		 * @return bool
		 */
		function validate($username, $password)
		{
			$query = $this->db->get_where('user', array(
				'email' => $username,
				'password' => $password
			));

			if ($query->num_rows() === 1)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		/**
		 * Returns user data by using username
		 *
		 * @param string $user_login
		 * @return object
		 */
		function user_by_user_login($user_login)
		{
			return $this->db->get_where('user', array(
				'email' => $user_login
			));
		}

		/**
		 * Updates user db
		 *
		 * @param int $id
		 * @param array $data
		 * @return int
		 */
		function update_user_login($id, array $data)
		{
			$this->db->where('user', $id);
			$this->db->update('user', $data);
			return $this->db->affected_rows();
		}
	}