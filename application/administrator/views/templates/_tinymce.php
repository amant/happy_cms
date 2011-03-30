<?php echo script('tiny_mce/jquery.tinymce.js') ?>
<script type="text/javascript">
	$(document).ready(function(){
		$('textarea.tinymce').tinymce({
			/* Location of TinyMCE script */
			script_url: '<?php echo base_url() ?>public/javascripts/tiny_mce/tiny_mce_gzip.php',

			/* General options */
			theme: "advanced",
			skin: "o2k7",
			skin_variant: "black",
			plugins: "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",

			/* Theme options */
			theme_advanced_buttons1: "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect,|,forecolor,backcolor,|,undo,redo,|,cut,copy,paste,pastetext,pasteword,|,search,replace|,insertdate,inserttime,preview,|,fullscreen",
			theme_advanced_buttons2: "bullist,numlist,|,outdent,indent,blockquote,|,insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,|,link,unlink,anchor,image,cleanup,help,code,",
			theme_advanced_buttons3: "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl",
			theme_advanced_buttons4: "",
			theme_advanced_toolbar_location: "top",
			theme_advanced_toolbar_align: "left",
			theme_advanced_statusbar_location: "bottom",
			theme_advanced_resizing: true,

			/* Example content CSS (should be your site CSS) */
			content_css: "css/content.css",

			/* Drop lists for link/image/media/template dialogs */
			template_external_list_url: "lists/template_list.js",
			external_link_list_url: "lists/link_list.js",
			external_image_list_url: "lists/image_list.js",
			media_external_list_url: "lists/media_list.js"
		});
	});

</script>
<!-- /TinyMCE -->