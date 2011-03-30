<?php echo form_hidden('id', $db['id']) ?>
<table cellspacing="0" cellpadding="0" border="0" width="100%">
  <tr>
    <td valign="top">
		<table  class="adminform">
			<tr>
			  <td widht="120">Article:</td>
			  <td>
				<select name="cms_content_id">
				  <option value="0">- Select Content -</option>
				  <?php echo display_cms_content_by_cms_type_id($db['cms_content_id']) ?>
				</select>
			  </td>
			</tr>
			<tr>
			  <td width="120">Comment:</td>
			  <td><textarea id="fulltext" name="fulltext" rows="15" style="width:98%;"><?php echo trim_nl($db['fulltext']) ?></textarea></td>
			</tr>
			<tr>
			  <td>Author*:</td>
			  <td>
				<input name="author" value="<?php echo $db['author'] ?>" type="text" class="inputbox" id="author" size="40" maxlength="255">
			  </td>
			</tr>
			<tr>
			  <td>Author Email*:</td>
			  <td>
				<input name="author_email" value="<?php echo $db['author_email'] ?>" type="text" class="inputbox" id="author_email" size="40" maxlength="255" />
			  </td>
			</tr>
			<tr>
			  <td>Author URL:</td>
			  <td>
				<input name="author_url" value="<?php echo $db['author_url'] ?>" type="text" class="inputbox" id="author_url" size="40" maxlength="255" />
			  </td>
			</tr>
		</table>
    </td>
    <td valign="top" width="320" align="right">
	<?php if(get_active_function() == 'edit'): ?>
	<fieldset class="adminform">
		<legend>Info.</legend>
		<table width="100%">
			<tbody>
			  <tr>
				<td>ID:</td>
				<td> <?php echo $db['id'] ?> </td>
			  </tr>
			  <tr>
				<td><strong>Last Modified:</strong> </td>
				<td> <?php echo $db['modified'] ?> </td>
			  </tr>
			</tbody>
		</table>
	</fieldset>
	<?php endif; ?>
	<fieldset class="adminform">
		<legend>Publishing Parameters </legend>
		<table width="100%" class="paramlist admintable" cellspacing="1">
			<tr>
				<td>Published:</td>
				<td><input name="status" type="checkbox" id="status" value="1" <?php if($db['status']) { ?>checked="checked"<?php } ?> /></td>
			</tr>
			<tr>
			  <td>Author IP:</td>
			  <td>
				<input name="author_ip" value="<?php echo $db['author_ip'] ?>" type="text" class="inputbox" id="author_ip" size="40" maxlength="255" />
			  </td>
			</tr>
		</table>
	</fieldset>
	</td>
  </tr>
</table>