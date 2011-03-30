<?php echo form_hidden('id', $db['id']) ?>

<table cellspacing="0" cellpadding="0" border="0" width="100%">
  <tr>
    <td valign="top">
		<table  class="adminform">
        <tr>
          <td width="120">Parent:</td>
          <td>
            <select name="parent_id" size="1" class="inputbox" id="access">
              <option value="0"> Root </option>
              <?php echo display_cms_type($db['parent_id']) ?>
            </select>
          </td>
        </tr>
		<tr>
           <td class="key" width="120">Main Image:</td>
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
				   <?php echo image('cms_type/big/' . $db['image']) ?>
				</div>
           </td>
        </tr>
		<tr>
          <td>Title (English)*:</td>
          <td>
            <input name="title_en" value="<?php echo $db['title_en'] ?>" type="text" class="inputbox" id="title_en" size="40" maxlength="255" />
          </td>
        </tr>
		<tr>
			<td>Description (English):</td>
			<td><textarea id="fulltext_en" name="fulltext_en" rows="10" style="width:98%"><?php echo trim_nl($db['fulltext_en']) ?></textarea></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><button id="translate_lang">Translate Fields</button></td>
		</tr>
		<tr>
          <td>Title (German):</td>
          <td>
            <input name="title_de" value="<?php echo $db['title_de'] ?>" type="text" class="inputbox" id="title_de" size="40" maxlength="255" />
          </td>
        </tr>
		<tr>
			<td>Description (German):</td>
			<td><textarea id="fulltext_de" name="fulltext_de" rows="10" style="width:98%"><?php echo trim_nl($db['fulltext_de']) ?></textarea></td>
		</tr>
		<tr>
          <td>Title (Italian):</td>
          <td>
            <input name="title_it" value="<?php echo $db['title_it'] ?>" type="text" class="inputbox" id="title_it" size="40" maxlength="255" />
          </td>
        </tr>
		<tr>
			<td>Description (Italian):</td>
			<td><textarea id="fulltext_it" name="fulltext_it" rows="10" style="width:98%"><?php echo trim_nl($db['fulltext_it']) ?></textarea></td>
		</tr>
		<tr>
          <td>Title (Russian):</td>
          <td>
            <input name="title_ru" value="<?php echo $db['title_ru'] ?>" type="text" class="inputbox" id="title_ru" size="40" maxlength="255" />
          </td>
        </tr>
		<tr>
			<td>Description (Russian):</td>
			<td><textarea id="fulltext_ru" name="fulltext_ru" rows="10" style="width:98%"><?php echo trim_nl($db['fulltext_ru']) ?></textarea></td>
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
            <td><strong>Hits</strong> </td>
            <td>
				<div id="hits_label"> <?php echo $db['hits'] ?>
				<input type="button" value="Reset" class="button" name="reset_hits" onclick="javascript: $('#hits_input').show(); $('#hits_label').hide();"/>
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
        </tbody>
      </table>
      </fieldset>

	  <fieldset class="adminform">
      <legend>Meta data for SEO.</legend>
      <table width="100%">
        <tbody>
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
        </tbody>
      </table>
      </fieldset>
   </td>
  </tr>
</table>
<script>
	window.onload = function(){
		/* onbserve the remove image button */
		$('#btn_remove_image').click(function (event) {
			event.stopPropagation();
			event.preventDefault();

			$('#replace_image').hide();
			$('#replace_image_by').show();
			$('#btn_remove_image').hide();
			$('#btn_replace_image').hide();

			$.get('<?php echo site_url('cms/cms_type/update_image')?>/' + $('input[name=id]').val(), function (response) {
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
		$('#translate_lang').click(function(e){
			e.preventDefault();

			var from = 'en', title = '', description = '';

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