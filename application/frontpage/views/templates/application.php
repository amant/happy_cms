<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv=content-type content="text/html; charset=UTF-8"></meta>
	<title><?php echo $page_title ?></title>
	<style type="text/css">

	body {
	 background-color: #fff;
	 margin: 40px;
	 font-family: Lucida Grande, Verdana, Sans-serif;
	 font-size: 14px;
	 color: #4F5155;
	}
	
	a {
	 color: #003399;
	 background-color: transparent;
	 font-weight: normal;
	}
	
	h1 {
	 color: #444;
	 background-color: transparent;
	 border-bottom: 1px solid #D0D0D0;
	 font-size: 16px;
	 font-weight: bold;
	 margin: 24px 0 2px 0;
	 padding: 5px 0 6px 0;
	}
	</style>

	<?php echo style('style.css'); ?>
	<?php echo style('jquery-ui/ui-lightness/jquery-ui.css'); ?>
	
	<?php echo script('jquery/jquery.min.js'); ?>
	<?php echo script('jquery/jquery-ui.min.js'); ?>
	<?php echo script('application.js'); ?>	
</head>
<body>	
	<?php echo $this->ocular->yield() ?>		
</body>
</html>