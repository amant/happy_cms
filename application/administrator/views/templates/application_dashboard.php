<?php bootstrap(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv=content-type content="text/html; charset=UTF-8"></meta>
    <title><?php echo $page_title ?></title>
	<?php echo favicon('favicon.png') ?>

    <?php echo style('themes/general.css') ?>
    <?php echo style('themes/icon.css') ?>
    <?php echo style('themes/rounded.css') ?>

    <?php echo style('themes/global.css') ?>
    <?php echo style('themes/table_layout.css') ?>

    <!-- START Prototype Framework -->
	<?php render_partial('/jsframework') ?>    
	<!-- END Prototype Framework -->

    <!-- START SPRY FRAME WORK -->
	<?php echo script('spry/widgets/textfieldvalidation/SpryValidationTextField.js') ?>
	<?php echo style('spry/widgets/textfieldvalidation/SpryValidationTextField.css') ?>
	<!-- END SPRY FRAME WORK -->

    <?php echo script('administrator/index.js') ?>
</head>
<body>
    <div id="page">
      <div id="nonFooter">
        <div id="hdr">
          <div id="hdr_wrap">
            <div id="main_logo"><h1><a href="#">Happy-CMS</a></h1></div>
            <div id="main_nav"><?php echo render_partial('menu') ?></div>
            <div id="hdr_rgt">
            	<div id="login"><?php echo render_partial('top_right_menu')?></div>
            </div>
          </div>
        </div>
        <div id="subnav">
          <div id="subnav_wrap">
            <?php echo render_partial('breadcrumb')?>
          </div>
        </div>
        <div id="body">
          <div id="body_wrap">
            <?php echo render_partial('/flash_notification') ?>
            <?php echo $this->ocular->yields() ?>        
          </div>
        </div>
      </div>
    </div>
    <div id="footer">
      <div id="footer_wrap">
        <?php echo render_partial('/footer') ?>
      </div>
    </div>
</body>
</html>
