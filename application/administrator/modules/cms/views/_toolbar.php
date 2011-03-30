<?php
	$toolbar_add = array(
		'toolbar-save' =>
			anchor('save', '<span class="icon-32-save" title="Save"></span> Save',
				array(
					'onclick'=>"javascript: submitform('" . site_url('cms/add') . "', {validate:true}); return false;",
					'class'=>'toolbar'
				)
			),
		'toolbar-apply' =>
			anchor('apply', '<span class="icon-32-apply" title="Apply"></span> Apply',
				array(
					'onclick'=>"javascript: submitform('" . site_url('cms/add/apply') . "', {validate:true}); return false;",
					'class'=>'toolbar'
				)
			),
		'toolbar-cancel' =>
			anchor('cms/display', '<span class="icon-32-cancel" title="Cancel"></span> Cancel',
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
					'onclick'=>"javascript: submitform('" . site_url('cms/edit/' . $db['id']) . "', {validate:true}); return false;",
					'class'=>'toolbar'
				)
			),
		'toolbar-apply' =>
			anchor('apply', '<span class="icon-32-apply" title="Apply"></span> Apply',
				array(
					'onclick'=>"javascript: submitform('" . site_url('cms/edit/' . $db['id'] . '/apply') . "', {validate:true}); return false;",
					'class'=>'toolbar'
				)
			),
		'toolbar-cancel' =>
			anchor('cms/display', '<span class="icon-32-cancel" title="Cancel"></span> Cancel',
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
					'onclick'=>"javascript: if(submitbutton('" . site_url('cms/publish') . "')==0) alert('Please make a selection from the list to Publish'); return false;",
					'class'=>'toolbar'
				)
			),

		'toolbar-unpublish' =>
			anchor('unpublish', '<span class="icon-32-unpublish" title="UnPublish"></span> UnPublish',
				array(
					'onclick'=>"javascript: if(submitbutton('" . site_url('cms/unpublish') . "')==0) alert('Please make a selection from the list to UnPublish'); return false;",
					'class'=>'toolbar'
				)
			),

		'toolbar-trash' =>
			anchor('trash', '<span class="icon-32-trash" title="Trash"></span> Trash',
				array(
					'onclick'=>"javascript: if(submitbutton('" . site_url('cms/trash') . "')==0) alert('Please make a selection from the list to Trash'); return false;",
					'class'=>'toolbar'
				)
			),

		'toolbar-edit' =>
			anchor('edit', '<span class="icon-32-edit" title="Edit"></span> Edit',
				array(
					'onclick'=>"javascript: if(submitedit('" . site_url('cms/edit') . "')==0 || submitedit('" . site_url('cms/edit') . "')>1){alert('Please Select One Item from List to Edit');} return false;",
					'class'=>'toolbar'
				)
			),

		'toolbar-new' =>
			anchor('cms/add', '<span class="icon-32-new" title="New"></span> New',
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

		$toolbar_trash = array(
		'toolbar-restore' =>
			anchor('publish', '<span class="icon-32-restore" title="Restore"></span> Restore',
				array(
					'onclick'=>"javascript: if(submitbutton('" . site_url('cms/restore') . "')==0) alert('Please make a selection from the list to Restore'); return false;",
					'class'=>'toolbar'
				)
			),
		'toolbar-delete' =>
			anchor('trash', '<span class="icon-32-delete" title="Delete"></span> Delete',
				array(
					'onclick'=>"javascript: if(submitbutton('" . site_url('cms/delete_all') . "')==0) alert('Please make a selection from the list to Delete'); return false;",
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

		case 'display_trash':
			$toolbar = $toolbar_trash;
			break;

		case 'display':
			$toolbar = $toolbar_display;
			break;
	}
?>

<div class="toolbar" id="toolbar">
	<table class="toolbar">
		<tr>
			<?php
				if($toolbar != '')
				foreach($toolbar as $key => $value):
			 ?>
				<td class="button" id="<?php echo $key ?>"> <?php echo $value ?></td>
			<?php endforeach; ?>
		</tr>
	</table>
</div>

<div class="header icon-48-addedit"> Article - <?php echo humanize(get_active_function()); ?></div>