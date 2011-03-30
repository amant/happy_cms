<?php
	class Lab extends CI_Controller
    {
		function __construct()
        {
			parent::__construct();
			//$this->output->enable_profiler(TRUE);
			//Load form helper
			$this->load->helper("form");
		}

        function password()
        {
            $this->load->library('encryption');
            $plain_text = '';
            $salt = '';
            $chiper_text = '';

            
            if($this->input->post('plain_text') != '')          // encrypt string
            {
                $plain_text = $this->input->post('plain_text');
                $salt = $this->input->post('salt');
                $chiper_text = $this->encryption->encrypt($salt, $plain_text);
            }
            elseif($this->input->post('chiper_text') != '')     // decrypt string
            {
                $chiper_text = $this->input->post('chiper_text');
                $salt = $this->input->post('salt');
                $plain_text = $this->encryption->decrypt($salt, $chiper_text);
            }
            
            $this->ocular->set_view_data('plain_text', $plain_text);
            $this->ocular->set_view_data('salt', $salt);
            $this->ocular->set_view_data('chiper_text', $chiper_text);
            $this->ocular->render();
        }


        /*function cnv_exiting_password()
        {
            $query = $this->db->get('user');
            echo "<ul>";
            foreach($query->result() as $user)
            {
                list($new_password, $salt) = encrypt_password($user->password);
                $decrypt_password = decrypt($new_password, $salt);

                echo "<li>";
                echo 'Existing Password: <b>' . $user->password . '</b><br/>';
                echo 'Salt: <b>' . $salt . '</b><br/>';
                echo 'New Password: <b>' . $new_password . '</b><br/>';
                echo 'Decrypt: <b>' . $decrypt_password . '</b><br/>';
                echo "</li>";

                $data = array(
                    'password' => $new_password,
                    'salt' => $salt
                );

                $this->db->where('user_id', $user->user_id);
                $this->db->update('user', $data);
            }
            echo "</ul>";
        }*/

        function decrypt_exiting_password()
        {
            $query = $this->db->get('user');
            echo "<ul>";
            foreach($query->result() as $user)
            {
                $decrypt_password = decrypt($user->password, $user->salt);
                echo "<li>";
                echo 'User Login: <b>' . $user->user_login . '</b><br/>';
                echo 'Existing Password: <b>' . $user->password . '</b><br/>';
                echo 'Existing Salt: <b>' . $user->salt . '</b><br/>';                
                echo 'Decrypt: <b>' . $decrypt_password . '</b><br/>';
                echo "</li>";
            }
            echo "</ul>";
        }
	}
?>