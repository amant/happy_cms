<?php

	class CA_ColumnTree {

		const node_blank  = 'public/javascripts/ca_columntree/images/nodeblank.gif';
		const node_plus   = 'public/javascripts/ca_columntree/images/p.gif';
		const node_minus  = 'public/javascripts/ca_columntree/images/m.gif';
		const node_space  = 'public/javascripts/ca_columntree/images/s.gif';

		private $column_width = array();
		

		function render($column_tree_data)
		{
			$ci = get_instance();
			$row = '';
			
			foreach($column_tree_data['column'] as $col_hd)
			{
				$this->column_width[]['width'] = $col_hd['width'];
			}
			
			$data['column'] = $this->column_width;
			$data['column_tree_data'] = $column_tree_data;
			$data['panel_height'] = $column_tree_data['height'];
			$data['panel_width'] = $column_tree_data['width'];

			ob_start();
			$ci->load->view('ca_columntree', $data);
			$html_content = ob_get_contents();
			ob_end_clean();
			
			return $html_content;
		}


		static function create_child_node($row_data, $column_width, $row_count, $sub_row_count = 0, $recursive_depth_count=0)
		{
			if(isset($row_data['child']))
			{
				/*if($row_count == 0 && $sub_row_count == 0) {
					echo '<ul class="tree-root" id="child-node-' . $row_count . '-' . $sub_row_count . '-' . $recursive_depth_count . '" style="display:none;">';
				} else {
					echo '<ul class="tree-root" id="child-node-' . $row_count . '-' . ($sub_row_count + 1) . '-' . $recursive_depth_count . '" style="display:none;">';
				}*/
				echo '<ul class="tree-root" id="child-node-' . $row_count . '-' . ($sub_row_count + 1) . '-' . $recursive_depth_count . '" style="display:none;">';

				foreach($row_data['child'] as $child_data)
				{
					echo '<li class="tree-node">';
					echo '<div class="tree-node-inner">';

					$column_count = 0;
					foreach($child_data['data'] as $content)
					{
						if($column_count == 0)
						{
							$img = '<img src="' . base_url() . self::node_space . '" class="tree-icon-blank" />';
							$nodeblank = '';							
							$nodeplus = '<img src="' . base_url() . self::node_space . '" class="tree-pm-icon tree-icon-plus" rel="child-node-' . $row_count . '-' . ($sub_row_count + 1). '-' . ($recursive_depth_count + 1) . '" />';
							$nodeminus = '<img src="' . base_url() . self::node_space . '" class="tree-icon-minus" />';

							for($i=0; $i<=$recursive_depth_count; $i++)
							{
								$nodeblank .= $img;								
							}

							if(isset($child_data['child']))
							{
								echo '<div class="tree-col tree-col-text" style="width:' . $column_width[$column_count]['width'] . '">'. $nodeblank . $nodeplus . $content .'</div>';
							} 
							else
							{
								echo '<div class="tree-col tree-col-text" style="width:' . $column_width[$column_count]['width'] . '">'. $nodeblank . $nodeminus . $content .'</div>';
							}
							
						}
						else
						{
							echo '<div class="tree-col tree-col-text" style="width:' . $column_width[$column_count]['width'] . '">'. $content .'</div>';
						}

						$column_count++;
					}
					echo '<div class="clear"></div>';
					echo '</div>';

					CA_ColumnTree::create_child_node($child_data, $column_width, $row_count, ($sub_row_count++), ($recursive_depth_count+1));
					
					echo '</li>';
				}
				echo '</ul>';
			}
		}
		
	}
?>