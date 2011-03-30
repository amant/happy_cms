<?php echo script('ckeditor/ckeditor.js') ?>
<?php echo script('ckeditor/adapters/jquery.js') ?>
<?php echo script('ckfinder/ckfinder.js') ?>

<?php /*
var config = {
		toolbar:
		[
			['Source','-','Save','NewPage','Preview','-','Templates'],
			['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
			['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
			['Maximize', 'ShowBlocks','-','About'],
			'/',
			['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
			'/',
			['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
			['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
			['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
			['BidiLtr', 'BidiRtl'],
			['Link','Unlink','Anchor'],
			['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe'],
			'/',
			['Styles','Format','Font','FontSize'],
			['TextColor','BGColor']
		]
	};
 */
?>
<script>
	$('document').ready(function(){
	var config = {
		toolbar:
		[
			['Source','-','Preview','-','Templates'],
			['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
			['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
			['Maximize', 'ShowBlocks','-','About'],
			'/',			
			['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
			['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
			['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
			['BidiLtr', 'BidiRtl'],
			['Link','Unlink','Anchor'],			
			'/',
			['Styles','Format','Font','FontSize'],
			['TextColor','BGColor'],
			['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe']
		]
	};

		/* Initialize the editor. */
		/* Callback function can be passed and executed after full instance creation. */
		$('.jquery_ckeditor').ckeditor(config);
		var editor = $('.jquery_ckeditor').ckeditorGet();
		CKFinder.setupCKEditor( editor, '<?php echo base_url() ?>public/javascripts/ckfinder/' ) ;
	});
</script>