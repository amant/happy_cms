<div class="panel">

	<div class="panel-head" style="width: <?php echo $panel_width ?>">
	<ul class="tree-root">
		<li class="tree-node">
        	<div class="tree-node-hd">
				<?php foreach($column_tree_data['column'] as $col_hd): ?>
				<div class="tree-col tree-node-hd-col" style="width:<?php echo $col_hd['width'] ?>"><?php echo $col_hd['title'] ?></div>
				<?php endforeach; ?>
                <div class="clear"></div>
            </div>
        </li>
	</ul>
	</div>


<div class="panel-inner" style="height: <?php echo $panel_height ?>; width: <?php echo $panel_width ?>">
	<ul class="tree-root">
		<?php $row_count = 0; ?>
		<?php foreach($column_tree_data['row'] as $row_data): ?>
		<li class="tree-node">
        	<div class="tree-node-inner">
				<?php
					// parent
					$column_count = 0;
					foreach($row_data['data'] as $content):

						$img = '<img src="' . base_url() . CA_ColumnTree::node_blank . '" />';
						$nodeblank = '';
						$nodeplus = '<img src="' . base_url() . CA_ColumnTree::node_space . '" class="tree-icon-plus tree-pm-icon" rel="child-node-' . $row_count . '-1-0" />';
						$nodeminus = '<img class="tree-icon-minus" src="' . base_url() . CA_ColumnTree::node_space . '" />';

						if($column_count == 0):
				?>
					<div class="tree-col tree-col-text" style="width:<?php echo $column[$column_count]['width']?>"><?php echo $nodeblank ?><?php echo (isset($row_data['child'])) ? $nodeplus : $nodeminus ;?><?php echo $content ?></div>
				<?php   else:  ?>
					<div class="tree-col tree-col-text" style="width:<?php echo $column[$column_count]['width']?>"><?php echo $content ?></div>
				<?php   endif; ?>
				<?php
					$column_count++;
					endforeach;
				?>
				<div class="clear"></div>
			</div>
			<?php CA_ColumnTree::create_child_node($row_data, $column, $row_count) ?>
		</li>
		<?php $row_count++ ?>
		<?php endforeach; ?>
	</ul>
</div>

</div>