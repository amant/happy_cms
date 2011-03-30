<div class="col50">
<?php echo form_open('lab/password'); ?>
<fieldset>
	<legend>Encrypt String</legend>
    	Plain String: <br/>      
        <input type="text" name="plain_text" value="<?php echo $plain_text ?>" id="plain_text" class="inputbox" size="40"/><br/>      
        Salt String: <br/>
        <input type="text" name="salt" value="<?php echo $salt ?>" id="salt" class="inputbox" size="40"/><br/><br/>
        <input type="submit" name="button" id="button" value="Convert" />
</fieldset>
<?php echo form_close(); ?>
</div>


<div class="col50">
<?php echo form_open('lab/password'); ?>
<fieldset>
	<legend>Decrypt String</legend>
    	Chiper String: <br/>      
        <input type="text" name="chiper_text" value="<?php echo $chiper_text ?>" id="chiper_text" class="inputbox" size="40" /> <br/>      
        Salt String: <br/>      
        <input type="text" name="salt" value="<?php echo $salt ?>" id="salt" class="inputbox" size="40"/><br/><br/>   
        <input type="submit" name="button" id="button" value="Convert" />
</fieldset>
<?php echo form_close(); ?>
</div>
<div class="clr"></div>
<div>
	<fieldset>
    	<legend> Processed String </legend>
        <ul>
        	<li>Plain Text: <b><?php echo $plain_text ?></b></li>
            <li>Salt Text: <b><?php echo $salt ?></b></li>
            <li>Chiper Text: <b><?php echo $chiper_text ?></b></li>
        </ul>
    </fieldset>
</div>
