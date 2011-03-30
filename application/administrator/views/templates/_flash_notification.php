<?php if($this->session->flashdata('notify') != ''): ?>
    <div class="notify"><?php echo $this->session->flashdata('notify') ?></div>
<?php endif; ?>

<?php if($this->session->flashdata('message') != ''): ?>
    <div class="message"><?php echo $this->session->flashdata('message') ?></div>
<?php endif; ?>

<?php if($this->session->flashdata('error') != ''): ?>
    <div class="error"><?php echo $this->session->flashdata('error') ?></div>
<?php endif; ?>

<?php if($this->session->userdata('notify') != ''): ?>
    <div class="notify"><?php echo $this->session->userdata('notify') ?></div>
	<?php $this->session->unset_userdata('notify') ?>
<?php endif; ?>

<?php if($this->session->userdata('message') != ''): ?>
    <div class="message"><?php echo $this->session->userdata('message') ?></div>
	<?php $this->session->unset_userdata('message') ?>
<?php endif; ?>

<?php if($this->session->userdata('error') != ''): ?>
    <div class="error"><?php echo $this->session->userdata('error') ?></div>
	<?php $this->session->unset_userdata('error') ?>
<?php endif; ?>