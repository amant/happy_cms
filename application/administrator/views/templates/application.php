<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></meta>
<title><?php echo $page_title ?></title>
<?php echo favicon('favicon.png') ?>
<?php echo style('themes/general.css') ?>
<?php echo style('themes/rounded.css') ?>
<?php echo style('themes/icon.css') ?>
<?php echo style('themes/global.css') ?>
<?php echo style('themes/table_layout.css') ?>

<?php render_partial('/jsframework') ?>

<?php echo script('administrator/index.js') ?>
<script>
	ca = {};
	ca.BASE_URL = '<?php echo site_url(); ?>';
</script>

</head>
<body class="yui-skin-sam">
    <div id="page">
      <div id="nonFooter">
        <div id="hdr">
          <div id="hdr_wrap">
            <div id="main_logo"><h1><a href="#">Happy-CMS</a></h1></div>
            <div id="main_nav"><?php render_partial('/menu') ?></div>
            <div id="hdr_rgt">
            	<div id="login"><?php render_partial('/top_right_menu')?></div>
            </div>
          </div>
        </div>
        <?php if(isset($sub_nav) === true && $sub_nav === true): ?>
        <div id="subnav">
          <div id="subnav_wrap">
            <?php render_partial('sub_nav') ?>
          </div>
        </div>
		<?php endif; ?>		  
        <div id="body">
          <div id="body_wrap">
            <?php echo render_partial('/flash_notification') ?>
			<?php if(get_active_controller() == 'user'): ?>
            <?php echo render_partial('/breadcrumb') ?>
			<?php endif; ?>
          </div>
          <div id="body_content">

			<div id="toolbar-box">
				<div class="t">
					<div class="t">
						<div class="t"></div>
					</div>
				</div>
				<div class="m">
					<?php render_partial('toolbar'); ?>
					<div class="clr"></div>
				</div>
				<div class="b">
					<div class="b">
						<div class="b"></div>
					</div>
				</div>
			</div>

			<?php echo $this->ocular->yields() ?>
          </div>
        </div>
      </div>
    </div>
    <div id="footer">
      <div id="footer_wrap">
        <?php render_partial('/footer') ?>
      </div>
    </div>	
	<?php if(isset($enable_jseditor) && $enable_jseditor === true): ?>
	<?php render_partial('/ckeditor') ?>
	<?php endif; ?>
</body>
</html>
