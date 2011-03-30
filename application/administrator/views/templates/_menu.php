<?php
	$menus = array(
			'welcome/index' => 'Dashboard',
			'cms/display' => 'Article',
		);

	$links = array(
		'welcome/index' => 'welcome/index',
		'cms/display' => 'cms/display',
		'cms/add' => 'cms/display',
		'cms/edit' => 'cms/display'
	);

	// Get active menu link
	$active_menu = '';
	foreach($links as $key => $value)
	{
		if(get_active_controller() . '/' . get_active_function() === $key)
		{
			$active_menu = $value;
			break;
		}
	}
?>
<ul>
	<?php foreach($menus as $menu_key => $menu_value): ?>
		<?php
			$class_name = '';
			if($active_menu === $menu_key):
				$class_name = 'class="active"';
			endif;
		?>

    	<li class="mainnavtrigger">
			<a href="<?php echo site_url($menu_key) ?>" <?php echo $class_name ?>>
				<span><b><?php echo $menu_value?></b></span>
			</a>
		</li>
    <?php endforeach; ?>
</ul>