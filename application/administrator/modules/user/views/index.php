<table class="adminform">
	<tbody>
		<tr>
		<td valign="top" width="55%"><?php echo display_yml_index(); ?></td>
		<td valign="top" width="45%">
        	<div id="content-pane" class="pane-sliders" style="padding-top:8px;">
            	<?php echo display_yml_help(); ?>
        	</div>
      </td>
    </tr>
  </tbody>
</table>

<script type="text/javascript">
	var pane = new Spry.Widget.Accordion("content-pane",{ useFixedPanelHeights: false, openClass: "toggler-down", closedClass: "toggler"});
</script>