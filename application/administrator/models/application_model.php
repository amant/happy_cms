<?php
	class Application_Model extends CI_Model
	{
		function __construct()
		{
			 parent::__construct();
		}
		
//		function get_module_id($title)
//		{
//			return $this->db->get_where('module',array('title'=>$title, 'status'=>'1'));
//		}
//
//		function get_controller_id($controller)
//		{
//			return $this->db->get_where('module_controller',array('controller'=>$controller, 'status'=>'1'));
//		}
//
//		function get_module_controller($module_controller_id, $user_type_id)
//		{
//			return $this->db->get_where('module_access',array('module_controller_id'=>$module_controller_id, 'user_type_id'=>$user_type_id));
//		}
//
//
//		function get_user_backend($parent_id = 3)
//		{
//			$user = $this->db->dbprefix('user');
//			$user_type = $this->db->dbprefix('user_type');
//			$sql = "SELECT $user.* FROM $user, $user_type WHERE $user.user_type_id = $user_type.user_type_id AND $user_type.parent_id = '$parent_id'";
//			return $this->db->query($sql);
//		}
//
//		function get_user_frontend($parent_id = 4)
//		{
//			$user = $this->db->dbprefix('user');
//			$user_type = $this->db->dbprefix('user_type');
//			$sql = "SELECT $user.* FROM $user, $user_type WHERE $user.user_type_id = $user_type.user_type_id AND $user_type.parent_id = '$parent_id'";
//			return $this->db->query($sql);
//		}
//
//		function get_user_type_frontend($parent_id = 4)
//		{
//			$user = $this->db->dbprefix('user');
//			$user_type = $this->db->dbprefix('user_type');
//			//$sql = "SELECT $user_type.* FROM $user, $user_type WHERE $user.user_type_id = $user_type.user_type_id AND $user_type.parent_id = '$parent_id'";
//
//			$sql = "SELECT $user_type.* FROM $user_type WHERE $user_type.parent_id = '$parent_id'";
//			return $this->db->query($sql);
//		}
//
//		function get_user_root($parent_id = 0)
//		{
//			return get_where('user_type', array('parent_id'=>$parent_id));
//		}
//
//		function get_user_type_backend($parent_id = 3)
//		{
//			/*$user = $this->db->dbprefix('user');
//			$user_type = $this->db->dbprefix('user_type');
//
//			$sql = "SELECT $user_type.* FROM $user, $user_type WHERE $user.user_type_id = $user_type.user_type_id AND $user_type.parent_id = '$parent_id'";
//			echo $sql;
//			die();
//
//			return $this->db->query($sql);*/
//
//			return $this->db->get_where('user_type', array('parent_id' => $parent_id));
//		}
//
//
//		function test()
//		{
//			return 1;
//		}
//
//		function bmenu_by_parent_id($parent_id=0)
//		{
//			$this->db->order_by('ordering');
//            return $this->db->get_where('bmenu', array('parent_id'=>$parent_id, 'status'=>'1'));
//        }
//
//        function is_bmenu_accessable_by_user_type($bmenu_id, $user_type_id)
//		{
//        	$query = $this->db->get_where('bmenu_access',array('bmenu_id'=>$bmenu_id, 'user_type_id'=>$user_type_id));
//        	if($query->num_rows > 0)
//        		return true;
//        	else
//        		return false;
//        }

	}
?>