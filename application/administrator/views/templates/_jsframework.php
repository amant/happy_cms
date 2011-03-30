<?php if ($_SERVER["HTTP_HOST"] === 'localhost'): ?>
	<?php if(get_active_function() === 'edit' OR get_active_function() === 'add'): ?>
		<?php echo style('jquery-ui/ui-lightness/jquery-ui.css'); ?>
		<?php echo script('jquery/jquery.min.js'); ?>
		<?php echo script('jquery/jquery-ui.min.js'); ?>		
	<?php else: ?>
		<?php echo script('prototype/prototype.js') ?>
		<script type="text/javascript" src="<?php echo base_url() ?>public/javascripts/prototype/scriptaculous.js?load=effects"></script>
	<?php endif; ?>

<?php else: ?>
	<?php if(get_active_function() === 'edit' OR get_active_function() === 'add'): ?>
		<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css" />
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>		
	<?php else: ?>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/prototype/1/prototype.js"></script>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/scriptaculous/1/scriptaculous.js?load=effects"></script>
	<?php endif; ?>
<?php endif; ?>