<div class="spacer w49">
  <div class="block">
    <h2>Login</h2>
    <fieldset>
      <legend>Login</legend>
      <?php echo form_open('login/validate', array('name'=>'adminForm', 'id'=>'adminForm', 'style'=>'clear:both;')) ?>
      <p>
        <label for="user_login">Username</label>
        <span id="username">
        <input name="user_login" id="user_login" type="text" class="inputbox" style="width:200px" value="admin@happy-cms.com"/>
        <span class="textfieldRequiredMsg">Username required</span>
        </span>
      </p>
      <p>
        <label for="password">Password</label>
        <span id="passwd">
        &nbsp;<input name="password" id="password" type="password" class="inputbox" style="width:200px" value="a" />
        <span class="textfieldRequiredMsg">Password required</span>
        </span>
      </p>
      
      <input type="submit" value="Login" onclick="javascript: submitform('<?php echo site_url('login/validate') ?>', {validate:true}); return false;" /> |
      <?php echo anchor('login/forgot_password', 'Forgot Password') ?>
      
      <?php echo form_close() ?>
      <script type="text/javascript">
		<!--
		//Text Field Vlidation
		var username = new Spry.Widget.ValidationTextField("username", "none", {minChars:1, maxChars:200, validateOn:["blur"]});
		var password = new Spry.Widget.ValidationTextField("passwd", "none", {minChars:1, maxChars:200, validateOn:["blur"]});
		//-->
		</script>
    </fieldset>
  </div>
  <div class="block">
    <h3>Login Help</h3>
    <ul>
      <li>Your email address is your username.</li>
      <li>Password is case sensitive, check if your keyboards caps lock is on or off.</li>
      <li>You can use forgot password to retrive password.</li>      
    </ul>
  </div>
</div>
<script language="javascript">$('user_login').focus()</script>