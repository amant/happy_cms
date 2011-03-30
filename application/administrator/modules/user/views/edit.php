<?php echo form_open_multipart('user/display',array('name'=>'adminForm', 'id'=>'adminForm')); ?>
  <?php render_partial('form'); ?>
<?php echo form_close(); ?>