<table class="adminform">
	<tbody>
		<tr>
		<td valign="top" width="55%">
        	<fieldset>
            	<legend>Users</legend>
                <div id="cpanel">
					<div style="float: left;">
						<div class="icon">
						<?php echo anchor('user/display',image('administrator/theme/header/icon-48-article.png', 'User List', array('align'=>'top','border'=>'0')).'<span>User List</span>') ?>
						</div>
					</div>
                    <div style="float: left;">
						<div class="icon">
						<?php echo anchor('user/add',image('administrator/theme/header/icon-48-article-add.png', 'Add New User', array('align'=>'top','border'=>'0')).'<span>Add New User</span>') ?>
						</div>
					</div>
                    
                    <div style="float: left;">
						<div class="icon">
						<?php echo anchor('user/display_trash',image('administrator/theme/header/icon-48-trash.png', 'Trash Manager', array('align'=>'top','border'=>'0')).'<span>Trash Manger</span>') ?>
						</div>
					</div>
                    	        
				</div>
            </fieldset>
            
        	<fieldset>
            	<legend>User Group</legend>                
				<div id="cpanel">
					<div style="float: left;">
						<div class="icon">
						<?php echo anchor('user_type/display',image('administrator/theme/header/icon-48-category.png', 'User Group List', array('align'=>'top','border'=>'0')).'<span>User Group List</span>') ?>
						</div>
					</div>
                    <div style="float: left;">
						<div class="icon">
						<?php echo anchor('user_type/add',image('administrator/theme/header/icon-48-article-add.png', 'Add New User Group', array('align'=>'top','border'=>'0')).'<span>Add New User Group</span>') ?>
						</div>
					</div>				
				</div>
            </fieldset>
		</td>
		<td valign="top" width="45%"><div id="content-pane" class="pane-sliders" style="padding-top:8px;">
        <div class="panel">
            <h3 class="title toggler-down" id="cpanel-panel">User Management Module</h3>
            <div style="border-top: medium none; border-bottom: medium none; overflow: hidden; padding-top: 0px; padding-bottom: 0px; height: 300px;" class="slider content">
                <div style="padding: 5px;">
                    <p>Through this module you can manage the users of the Administrative Panel and Website</p>
                </div>
            </div>
        </div>
      </td>
    </tr>
  </tbody>
</table>

<script type="text/javascript">
	var pane = new Spry.Widget.Accordion("content-pane",{ useFixedPanelHeights: false, openClass: "toggler-down", closedClass: "toggler"});
</script>