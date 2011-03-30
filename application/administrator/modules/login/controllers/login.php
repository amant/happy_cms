<?php
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Login extends CI_Controller
	{
		/**
		 * Constructor
		 */
		function __construct()
		{
			parent::__construct();
			$this->load->model('login_model', 'login');
			$this->load->library('email');
			$this->load->library('form_validation');
		}

		/**
		 * Return password to user email address incase they forgot their password
		 */
		function forgot_password()
		{
			$this->ocular->set_view_data('page_title', 'Forgot Password');
			$this->ocular->render('application_login');
		}

		/**
		 * Index Page
		 */
		function index()
		{
			$this->ocular->set_view_data('page_title', 'Administration Login');
			$this->ocular->render('application_login');
		}

		/**
		 * Logout
		 */
		function logout()
		{
			$this->session->unset_userdata('login');
			$this->session->unset_userdata('user_id');
			$this->session->unset_userdata('user_type_id');
			$this->session->unset_userdata('user_login');

			$this->session->set_flashdata('message', "<b>You have Successfully Logout</b>");
			redirect('login/index');
		}

		/**
		 * Return password to user email address incase they forgot their password
		 */
		function sendpassword()
		{
			$this->load->helper('string');

			$config = array(
				array(
					'field' => 'email',
					'label' => 'Email',
					'rules' => 'trim|required|valid_email'
				)
			);
			$this->form_validation->set_rules($config);

			// if validation ok, then update data otherwise don't
			if ($this->form_validation->run() == false)
			{
				$this->session->set_flashdata('error', $this->form_validation->error_string());
				redirect('login/forgot_password');
			}
			else
			{
				$query = $this->login->user_by_user_login($this->input->post('email'));
				if ($query->num_rows() > 0)
				{
					$row = $query->row();

					// reset password and send email

					/*$new_pass = random_string('alnum');
					list($password, $salt) = encrypt_password($new_pass);
					$this->login->update_user_login($row->user_id, array('password'=>$password, 'salt'=>$salt));

					$this->send_email($this->input->post('email'), array(
					'user_login'=>$row->user_login,
					'password'=>$new_pass
					));*/

					// decrypt password and send email
					$password          = $row->password;
					$salt              = $row->email;
					$dechiper_password = decrypt($password, $salt);

					$subject = 'Forgot Password';
					$message = "Here is your Password <br/>\n";
					$message .= "username:  " . $row->user_login . "<br/>\n";
					$message .= "pass:  $dechiper_password <br/>\n";

					send_email($this->input->post('email'), $subject, $message);
				}

				$this->session->set_flashdata('message', "<b>Login Information has been send to Email Address " . $this->input->post('email') . ", Please check your email.</b>");
				redirect('login/index');
			}
		}

		/**
		 * Validate
		 */
		function validate()
		{
			// set the rule
			$config = array(
				array(
					'field' => 'user_login',
					'label' => 'User name',
					'rules' => 'trim|required'
				),
				array(
					'field' => 'password',
					'label' => 'Password',
					'rules' => 'trim|required'
				)
			);

			$this->form_validation->set_rules($config);

			// if validation ok, then update data otherwise don't
			if ($this->form_validation->run() == false)
			{
				$this->session->set_flashdata('error', $this->form_validation->error_string());
				redirect('login/index');
			}
			else
			{
				$encrypt_password = encrypt($this->input->post('password'), $this->input->post('user_login'));

				if ($this->login->validate($this->input->post('user_login'), $encrypt_password) !== false)
				{
					// save the user information in session
					$query = $this->login->user_by_user_login($this->input->post('user_login'));
					$row   = $query->row();

					$this->session->set_userdata('login', true);
					$this->session->set_userdata('user_id', $row->user_id);
					$this->session->set_userdata('user_type_id', $row->user_type_id);

					$this->session->set_flashdata('message', "<b>Welcome To Administrator Panel, $row->first_name $row->last_name  </b>");
					redirect('welcome/index');
				}
				else
				{
					$this->session->set_flashdata('error', 'Access Denied!!! Invalid Login');
					redirect('login/index');
				}
			}
		}
	}