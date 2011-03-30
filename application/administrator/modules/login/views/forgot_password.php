<div class="spacer w49">
  <div class="block">
    <h2>Forgot Password</h2>
    <fieldset>
      <legend>Email Address</legend>
      <?php echo form_open('login/sendpassword', array('name'=>'adminForm', 'id'=>'adminForm', 'style'=>'clear:both;')) ?>
      <p>
        <label for="email">User Email *</label>
        <span id="email">
        <input name="email" type="text" class="inputbox" id="email" size="25" maxlength="250" />
        <span class="textfieldRequiredMsg">Email required.</span> <span class="textfieldInvalidFormatMsg">Invalid email format</span>
        </span>
      </p>
      
      <input type="submit" value="Send"  onClick="javascript: submitform('<?php echo site_url('login/sendpassword') ?>', {validate:true}); return false;" /> |
	  <?php echo anchor('login/index', 'Back to Login') ?>
      <?php echo form_close() ?>
	  <script type="text/javascript">
      <!--
      //Text Field Vlidation
          var email = new Spry.Widget.ValidationTextField("email", "email", {validateOn:["blur"]});
      //-->
      </script>
    </fieldset>
  </div>
  <div class="block">
    <h3>Password Forgot Help</h3>
    <ul>
      <li>Enter your user name to get back your password in email.</li>
      <li>It is highly recommended to use somekind of password manager to manager your password.</li>
    </ul>
  </div>
</div>