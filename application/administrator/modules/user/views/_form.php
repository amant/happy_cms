<?php echo script('spry/widgets/textfieldvalidation/SpryValidationTextField.js') ?>
<?php echo style('spry/widgets/textfieldvalidation/SpryValidationTextField.css') ?>

<?php echo script('pwd_strength/pwd_strength.js') ?>

<?php echo form_hidden('user_id', $db['user_id']) ?>
<div class="col100">
    <fieldset class="adminform">
    <legend>Login Details</legend>
    <table class="admintable" cellspacing="1">
      <tr>
        <td width="150" class="key">Email*:</td>
        <td><span id="v_email">
        <input name="email" value="<?php echo $db['email'] ?>" type="text" class="inputbox" id="email" size="60" maxlength="255" />
        <span class="textfieldRequiredMsg">Email Required.</span> <span class="textfieldInvalidFormatMsg">Invalid format.</span></span>        </td>
      </tr>
      <tr>
        <td class="key">Password*:</td>
        <td>
			<span id="v_password">
				<input name="password" value="<?php echo decrypt($db['password'], $db['email']) ?>" type="password" class="inputbox" id="password" size="60" maxlength="255" onkeyup="runPassword(this.value, 'password');"/>
				<span class="textfieldRequiredMsg">Password Required.</span>
			</span>

          <div style="width: 100px;">
			<div id="password_text" style="font-size: 10px;"></div>
			<div id="password_bar" style="font-size: 1px; height: 2px; width: 0px; border: 1px solid white;"></div>
		  </div>

        </td>
      </tr>

      <tr>
        <td class="key">User Group:</td>
        <td><select name="user_type_id" size="1" class="inputbox" id="user_type_id">
            <?php echo display_user_type($db['user_type_id']) ?>
          </select>
		</td>
      </tr>

      <tr>
        <td class="key">Active:</td>
        <td><input name="status" type="checkbox" id="status" value="1" <?php if($db['status']==1) echo 'checked="checked"'; ?>/></td>
      </tr>
    </table>
    </fieldset>
    <fieldset class="adminform">
    <legend>Contact Information </legend>
    <table class="admintable" cellspacing="1">
      <tr>
        <td width="150" class="key"><label for="name"> Firstname*: </label></td>
        <td>
		<span id="v_first_name">
          <input name="first_name" type="text" class="inputbox" id="first_name" value="<?php echo $db['first_name'] ?>" size="60" maxlength="255" />
		  <span class="textfieldRequiredMsg">Firstname Required.</span> </span></td>
      </tr>
      <tr>
        <td class="key">Middle Name: </td>
        <td><input name="middle_name" value="<?php echo $db['middle_name'] ?>" type="text" class="inputbox" id="middle_name" size="60" maxlength="255" /></td>
      </tr>
      <tr>
        <td class="key">Last Name:</td>
        <td>
          <input name="last_name" value="<?php echo $db['last_name'] ?>" type="text" class="inputbox" id="last_name2" size="60" maxlength="255" />
        </td>
      </tr>
    </table>
  </fieldset>
</div>
<script type="text/javascript">  
  	var v_email = new Spry.Widget.ValidationTextField("v_email", "email", {validateOn:["blur"]});
	var v_password = new Spry.Widget.ValidationTextField("v_password", "none", {validateOn:["blur"]});
	var v_first_name = new Spry.Widget.ValidationTextField("v_first_name", "none", {validateOn:["blur"]});  
</script>
