<div style="border:1px solid #ccc; margin-top:10px;">
	<ul>
		<?php foreach($contents->result() as $content): ?>
		<li><?php echo anchor('cms/detail/' . $content->alias, $content->title_en) ?></li>
		<?php endforeach; ?>		
	</ul>
</div>