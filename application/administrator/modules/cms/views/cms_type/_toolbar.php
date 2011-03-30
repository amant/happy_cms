<?php
	$toolbar_add = array(
		'toolbar-save' =>
			anchor('save', '<span class="icon-32-save" title="Save"></span> Save',
				array(
					'onclick'=>"javascript: submitform('" . site_url('cms/cms_type/add') . "', {validate:false}); return false;",
					'class'=>'toolbar'
				)
			),
		'toolbar-apply' =>
			anchor('apply', '<span class="icon-32-apply" title="Apply"></span> Apply',
				array(
					'onclick'=>"javascript: submitform('" . site_url('cms/cms_type/add/apply') . "', {validate:false}); return false;",
					'class'=>'toolbar'
				)
			),
		'toolbar-cancel' =>
			anchor('cms/cms_type/display', '<span class="icon-32-cancel" title="Cancel"></span> Cancel',
				array(
					'class'=>'toolbar'
				)
			),

		'toolbar-help' =>
			anchor('help', '<span class="icon-32-help" title="Help"></span> Help',
				array(
					'target'=>'_blank',
					'class'=>'toolbar'
				)
			)
		);

	if(isset($db['id'])):
	$toolbar_edit = array(
		'toolbar-save' =>
			anchor('save', '<span class="icon-32-save" title="Save"></span> Save',
				array(
					'onclick'=>"javascript: submitform('" . site_url('cms/cms_type/edit/' . $db['id']) . "', {validate:false}); return false;",
					'class'=>'toolbar'
				)
			),
		'toolbar-apply' =>
			anchor('apply', '<span class="icon-32-apply" title="Apply"></span> Apply',
				array(
					'onclick'=>"javascript: submitform('" . site_url('cms/cms_type/edit/' . $db['id'] . '/apply') . "', {validate:false}); return false;",
					'class'=>'toolbar'
				)
			),
		'toolbar-cancel' =>
			anchor('cms/cms_type/display', '<span class="icon-32-cancel" title="Cancel"></span> Cancel',
				array(
					'class'=>'toolbar'
				)
			),

		'toolbar-help' =>
			anchor('help', '<span class="icon-32-help" title="Help"></span> Help',
				array(
					'target'=>'_blank',
					'class'=>'toolbar'
				)
			)
		);
	endif;

	$toolbar_display = array(
		'toolbar-publish' =>
			anchor('publish', '<span class="icon-32-publish" title="Publish"></span> Publish',
				array(
					'onclick'=>"javascript: if(submitbutton('" . site_url('cms/cms_type/publish') . "')==0) alert('Please make a selection from the list to Publish'); return false;",
					'class'=>'toolbar'
				)
			),

		'toolbar-unpublish' =>
			anchor('unpublish', '<span class="icon-32-unpublish" title="UnPublish"></span> UnPublish',
				array(
					'onclick'=>"javascript: if(submitbutton('" . site_url('cms/cms_type/unpublish') . "')==0) alert('Please make a selection from the list to UnPublish'); return false;",
					'class'=>'toolbar'
				)
			),

		'toolbar-edit' =>
			anchor('edit', '<span class="icon-32-edit" title="Edit"></span> Edit',
				array(
					'onclick'=>"javascript: if(submitedit('" . site_url('cms/cms_type/edit') . "')==0 || submitedit('" . site_url('cms/cms_type/edit') . "')>1){alert('Please Select One Item from List to Edit');} return false;",
					'class'=>'toolbar'
				)
			),

		'toolbar-new' =>
			anchor('cms/cms_type/add', '<span class="icon-32-new" title="New"></span> New',
				array(
					'class'=>'toolbar'
				)
			),
		'toolbar-help' =>
			anchor('help', '<span class="icon-32-help" title="Help"></span> Help',
				array(
					'target'=>'_blank',
					'class'=>'toolbar'
				)
			)
		);
?>

<?php
	$toolbar = '';
	switch(get_active_function()){
		case 'add':
			$toolbar = $toolbar_add;
			break;

		case 'edit':
			$toolbar = $toolbar_edit;
			break;

		case 'display':
			$toolbar = $toolbar_display;
			break;
	}
?>

<div class="toolbar" id="toolbar">
	<table class="toolbar">
		<tr>
			<?php if($toolbar != '') ?>
			<?php foreach($toolbar as $key => $value): ?>
				<td class="button" id="<?php echo $key ?>"> <?php echo $value ?></td>
			<?php endforeach; ?>
		</tr>
	</table>
</div>

<div class="header icon-48-addedit"> Article Type - <?php echo humanize(get_active_function()); ?></div>