<?php echo script('spry/widgets/accordion/SpryAccordion.js') ?>
<?php echo style('spry/widgets/accordion/SpryAccordion.css') ?>
<div class="col60">
	<?php echo display_yml_index(); ?>
</div>
<div class="col40">
	<div id="content-pane" class="pane-sliders" style="padding-top:8px; color: #666666;">		
		<?php echo display_yml_help(); ?>
    </div>
</div>
<div class="clear">&nbsp;</div>
<script type="text/javascript">
	var pane = new Spry.Widget.Accordion("content-pane",{ useFixedPanelHeights: false, openClass: "toggler-down", closedClass: "toggler"});
</script>