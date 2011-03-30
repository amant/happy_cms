<?php echo form_hidden('id', $db['id']) ?>

<table cellspacing="0" cellpadding="0" border="0" width="100%">
  <tr>
    <td valign="top"><table  class="adminform">
        <tr>
          <td width="120">Type:</td>
          <td>
			  <select name="cms_type_id" id="cms_type_id" class="inputbox">
				<?php echo display_cms_type($db['cms_type_id']) ?>
			  </select>
			  <?php echo anchor('cms/cms_type/add', '+Add Type', array('class' => 'small')) ?>
		  </td>
        </tr>
		<tr>
           <td class="key" width="120">Image:</td>
           <td>
		   		<div align="right">
                <?php if($db['image'] != ''): ?>
                	<div id="notify" style="border:1px solid red; display:none;"></div>
                    <input name="btn_remove_image" type="button" id="btn_remove_image" value="Remove Image" title="To Remove Image" />&nbsp;
                	<input name="btn_replace_image" type="button" id="btn_replace_image" value="Replace Image"  title="Will Replace the Current Image"/>
                <?php endif; ?>
				</div>

				<div id="replace_image_by" <?php if($db['image'] == ''){ echo ('style="display:block; font-size:9px"'); } else { echo('style="display:none"'); } ?> >
				  <input type="file" size="60" id="image" name="image"/>
				  <input type="hidden" value="2097152" name="MAX_FILE_SIZE"/>
				  <br/>Max 2 MB (gif, jpg, png)
				</div>

				<div id="replace_image" <?php if($db['image'] == ''){ echo ('style="display:none"'); } else { echo('style="display:block"'); } ?>>
				   <a href="<?php echo base_url() ?>public/images/cms/big/<?php echo $db['image'] ?>" rel="prettyPhoto"><?php echo image('cms/thumb/' . $db['image'], $db['title_en']) ?></a>
				</div>
           </td>
        </tr>
		<tr>
          <td>Title (English)*:</td>
          <td>
			<span id="vtitle_en">
				<input name="title_en" value="<?php echo $db['title_en'] ?>" type="text" class="inputbox" id="title_en" size="60" maxlength="255" />
				<span class="textfieldRequiredMsg">Title (Enlgish) Required.</span>
			</span>
          </td>
        </tr>
		<tr>
			<td>Description (English):</td>
			<td>
				<textarea id="fulltext_en" name="fulltext_en" rows="15" style="width:98%;" class="jquery_ckeditor"><?php echo trim_nl($db['fulltext_en']) ?></textarea>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><button id="translate_lang">Translate Fields</button></td>
		</tr>
		<tr>
        	<td>Title (German):</td>
        	<td><input name="title_de" value="<?php echo $db['title_de'] ?>" type="text" class="inputbox" id="title_de" size="60" maxlength="255" /></td>
        </tr>
		<tr>
        	<td>Description (German):</td>
        	<td><textarea name="fulltext_de" id="fulltext_de" rows="15" style="width:98%" class="jquery_ckeditor"><?php echo trim_nl($db['fulltext_de']) ?></textarea></td>
        </tr>
		<tr>
        	<td>Title (Italian):</td>
        	<td><input name="title_it" value="<?php echo $db['title_it'] ?>" type="text" class="inputbox" id="title_it" size="60" maxlength="255" /></td>
        </tr>
		<tr>
        	<td>Description (Italian):</td>
        	<td><textarea name="fulltext_it" id="fulltext_it" rows="15" style="width:98%" class="jquery_ckeditor"><?php echo trim_nl($db['fulltext_it']) ?></textarea></td>
        </tr>
		<tr>
        	<td>Title (Russian):</td>
        	<td><input name="title_ru" value="<?php echo $db['title_ru'] ?>" type="text" class="inputbox" id="title_ru" size="60" maxlength="255" /></td>
        </tr>
		<tr>
        	<td>Description (Russian):</td>
        	<td><textarea name="fulltext_ru" id="fulltext_ru" rows="15" style="width:98%" class="jquery_ckeditor"><?php echo trim_nl($db['fulltext_ru']) ?></textarea></td>
        </tr>
      </table>
    </td>
    <td valign="top" width="320" align="right">

    <?php if(get_active_function() === 'edit'): ?>
    <fieldset class="adminform">
      <legend>Info.</legend>
      <table width="100%">
        <tbody>
          <tr>
            <td>ID:</td>
            <td> <?php echo $db['id'] ?> </td>
          </tr>
          <tr>
            <td>Hits</td>
            <td>
				<div id="hits_label">
					<?php echo $db['hits'] ?> <input type="button" value="Reset" class="button" name="reset_hits" onclick="javascript: $('#hits_input').show(); $('#hits_label').hide();"/>
				</div>
				<span id="hits_input" style="float:left; display:none">
					<input type="text" value="<?php echo $db['hits'] ?>" class="inputbox" name="hits" size="10"/>
				</span>
			 </td>
          </tr>
          <tr>
            <td>Last Modified:</td>
            <td> <?php echo $db['modified'] ?> </td>
          </tr>
        </tbody>
      </table>
    </fieldset>
    <?php endif; ?>

	<fieldset class="adminform">
		<legend>Parameters.</legend>
		<table width="100%">
			<tbody>
				<tr>
					<td>Published:</td>
					<td><input name="status" type="checkbox" id="status" value="1" <?php if($db['status']) { ?>checked="checked"<?php } ?> /></td>
				</tr>

				<tr>
					<td>Start Publishing:</td>
					<td><input name="publish_up" type="text" id="publish_up" value="<?php echo $db['publish_up'] ?>" /></td>
				</tr>

				<tr>
					<td>End Publishing:</td>
					<td><input name="publish_down" type="text" id="publish_down" value="<?php echo $db['publish_down'] ?>" /></td>
				</tr>
			</tbody>
		</table>
	  </fieldset>

      <fieldset class="adminform">
		  <legend>Meta data for SEO.</legend>
		  <table width="100%">
			<tr>
			  <td>Meta description:</td>
			  <td><textarea name="metadesc" id="metadesc" ><?php echo trim_nl($db['metadesc']) ?></textarea></td>
			</tr>
			<tr>
			  <td>Meta key:</td>
			  <td><textarea name="metakey" id="metakey" ><?php echo trim_nl($db['metakey']) ?></textarea></td>
			</tr>
			<tr>
			  <td>Crawler option:</td>
			  <td><textarea name="metadata" id="metadata" ><?php echo trim_nl($db['metadata']) ?></textarea></td>
			</tr>
		  </table>
      </fieldset>
	</td>
  </tr>
</table>

<script type="text/javascript">
	window.onload = function(){
        /* onbserve the remove image button */
		$('#btn_remove_image').click(function (event) {
			event.stopPropagation();
			event.preventDefault();

			$('#replace_image').hide();
			$('#replace_image_by').show();
			$('#btn_remove_image').hide();
			$('#btn_replace_image').hide();

			$.get('<?php echo site_url('cms/update_image')?>/' + $('input[name=id]').val(), function (response) {
				$('#notify').html(response).fadeIn();
			});
		});

		/* onbserve the replace image button */
		$('#btn_replace_image').click(function (event) {
			event.stopPropagation();
			event.preventDefault();

			$('#replace_image').hide();
			$('#replace_image_by').show();
		});
    };

	$('document').ready(function(){
		$("#publish_up, #publish_down").datepicker({dateFormat: 'yy-mm-dd'});

		$('#translate_lang').click(function(e){
			var from = 'en', title = '', description = '';

			e.preventDefault();
			/* Translate title and description */
			title = $('#title_en').val();
			description = $('#fulltext_en').val();

			$.each(['de', 'it', 'ru'], function(key, to){
				translate(from, to, title, $('#title_' + to));
				translate(from, to, description, $('#fulltext_' + to));
			});
		});
	});
</script>

<?php echo script('spry/widgets/textfieldvalidation/SpryValidationTextField.js') ?>
<?php echo style('spry/widgets/textfieldvalidation/SpryValidationTextField.css') ?>
<script type="text/javascript">  
  	var vtitle_en = new Spry.Widget.ValidationTextField("vtitle_en", "none", {validateOn:["blur"]});
</script>

<?php echo script('prettyPhoto/jquery.prettyPhoto.js') ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>public/javascripts/prettyPhoto/css/prettyPhoto.css" media="screen" charset="utf-8" />
<script type="text/javascript" charset="utf-8">
	$(document).ready(function(){
		$("a[rel^='prettyPhoto']").prettyPhoto({
			gallery_markup: ''
		});
	});
</script>