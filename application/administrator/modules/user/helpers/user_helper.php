<?php
	if (!defined('BASEPATH'))
		exit('No direct script access allowed');

	function display_user_type($user_type_id, $parent_id = 0)
	{
		$ci = get_instance();
		$query = $ci->user_type_model->user_type($parent_id);

		$space = '';
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				if ($user_type_id == $row->user_type_id)
				{
					echo '<option value="' . $row->user_type_id . '" selected="selected" >';
				}
				else
				{
					echo '<option value="' . $row->user_type_id . '" >';
				}

				for ($i = 0; $i < $row->parent_id; $i++)
					echo '-';

				echo $row->title . '</option>';

				display_user_type($user_type_id, $row->user_type_id);
			}
		}
	}

	function display_created_by($created_by)
	{
		$ci = get_instance();
		$query = $ci->application->get_user_backend();

		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				if ($created_by == $row->user_id)
				{
					echo '<option value="' . $row->user_id . '" selected="selected" >';
				}
				else
				{
					echo '<option value="' . $row->user_id . '" >';
				}

				echo '(' . $row->user_id . ') ' . $row->first_name . ' ' . $row->last_name . '</option>';
			}
		}
	}

	function display_user($user_id)
	{
		$ci = get_instance();
		$query = $ci->application->get_user_frontend();

		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				if ($user_id == $row->user_id)
				{
					echo '<option value="' . $row->user_id . '" selected="selected" >';
				}
				else
				{
					echo '<option value="' . $row->user_id . '" >';
				}

				echo '(' . $row->user_id . ') ' . $row->first_name . ' ' . $row->last_name . '</option>';
			}
		}
	}

	function display_package($package_id)
	{
		$ci = get_instance();
		$ci->load->model('package/package_model');

		$query = $ci->package_model->package_all();

		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				if ($package_id == $row->package_id)
				{
					echo '<option value="' . $row->package_id . '" selected="selected" >';
				}
				else
				{
					echo '<option value="' . $row->package_id . '" >';
				}

				echo $row->title . '</option>';
			}
		}
	}

	function display_package_status($status)
	{
		$arr = get_enum_value('user_has_package', 'status');

		foreach ($arr as $value)
		{
			if ($status == $value)
			{
				echo '<option value="' . $value . '" selected="selected" >';
			}
			else
			{
				echo '<option value="' . $value . '" >';
			}

			echo $value . '</option>';
		}
	}
?>