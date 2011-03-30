<?php
	if (!defined('BASEPATH'))
		exit('No direct script access allowed');

	function display_cms_type($cms_type_id, $parent_id = 0)
	{
		$ci = get_instance();
		$query = $ci->cms_type_model->cms_type_by_parent_id($parent_id);
		$space = '';

		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				if ($cms_type_id == $row->id)
				{
					echo '<option value="' . $row->id. '" selected="selected" >';
				}
				else
				{
					echo '<option value="' . $row->id . '" >';
				}

				for ($i = 0; $i < $row->parent_id; $i++)
					echo '-';

				echo $row->title_en . '</option>';

				display_cms_type($cms_type_id, $row->id);
			}
		}
	}

	function display_cms_type_ordering($cms_type_id, $parent_id = 0)
	{
		$ci = get_instance();
		$query = $ci->cms_type_model->cms_type_by_parent_id($parent_id);
		$space = '';

		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				if ($cms_type_id == $row->cms_type_id)
				{
					echo '<option value="' . $row->ordering . '" selected="selected" >';
				}
				else
				{
					echo '<option value="' . $row->ordering . '" >';
				}

				for ($i = 0; $i < $row->parent_id; $i++)
					echo '-';

				echo '(' . $row->ordering . ') ' . $row->title . '</option>';

				display_cms_type_ordering($cms_type_id, $row->cms_type_id);
			}
		}
	}


//	function display_created_by($created_by)
//	{
//		$ci = get_instance();
//		$query = $ci->application->get_user_backend();
//
//		if ($query->num_rows() > 0)
//		{
//			foreach ($query->result() as $row)
//			{
//				if ($created_by == $row->user_id)
//				{
//					echo '<option value="' . $row->user_id . '" selected="selected" >';
//				}
//				else
//				{
//					echo '<option value="' . $row->user_id . '" >';
//				}
//
//				echo '(' . $row->user_id . ') ' . $row->first_name . ' ' . $row->last_name . '</option>';
//			}
//		}
//	}	

	function display_cms_content_by_cms_type_id($cms_content_id)
	{
		$ci = get_instance();
		$query = $ci->cms_model->cms_content_by_cms_type_id();

		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				if ($cms_content_id == $row->id)
				{
					echo '<option value="' . $row->id . '" selected="selected" >';

				}
				else
				{
					echo '<option value="' . $row->id . '" >';
				}

				echo $row->title_en . ' [ ' . $row->cms_type . ' ]</option>';
			}
		}
	}