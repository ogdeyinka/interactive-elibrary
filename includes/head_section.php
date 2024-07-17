<!DOCTYPE html>
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8"/>
    <meta content="interactive, math,mathematics,science,social studies,brainteasers,art,music,typing,spanish,french,lang,language," name="keywords" />
    <link rel="stylesheet" type="text/css" href="<?php echo FILE_URL . 'css/google_font_css.css';?>"/>
    <link rel="stylesheet" type="text/css" href="<?php echo FILE_URL . 'source/jquery.fancybox.css?v=2.1.5';?>" media="screen" />
    <link rel="stylesheet" type="text/css" href="<?php echo FILE_URL . 'source/helpers/jquery.fancybox-buttons.css?v=1.0.5';?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo FILE_URL . 'source/helpers/jquery.fancybox-thumbs.css?v=1.0.7';?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo FILE_URL . 'css/main_style.css';?>"/>
    <link rel="stylesheet" type="text/css" href="<?php echo FILE_URL . 'css/font-awesome.min.css';?>"/>
    <link rel="stylesheet" type="text/css" href="<?php echo FILE_URL . 'lib/tokeninput/token-input.css';?>"/>
    <link rel="stylesheet" type="text/css" href="<?php echo FILE_URL . 'lib/confirm/jquery-confirm.min.css';?>"/>
    <link rel="stylesheet" type="text/css" href="<?php echo FILE_URL . 'lib/bootstrap/css/bootstrap.min.css';?>"/>
    <script type="text/javascript" src="<?php echo FILE_URL . 'js/jquery.min.js';?>"></script>
	<script type="text/javascript" src="<?php echo FILE_URL . 'source/jquery.fancybox.js?v=2.1.5';?>"></script>
	<script type="text/javascript" src="<?php echo FILE_URL . 'source/helpers/jquery.fancybox-buttons.js?v=1.0.5';?>"></script>
	<script type="text/javascript" src="<?php echo FILE_URL . 'source/helpers/jquery.fancybox-thumbs.js?v=1.0.7';?>"></script>
	<script type="text/javascript" src="<?php echo FILE_URL . 'source/helpers/jquery.fancybox-media.js?v=1.0.6';?>"></script>
    <script type="text/javascript" src="<?php echo FILE_URL . 'js/main.js';?>"></script>
    <script type="text/javascript" src="<?php echo FILE_URL . 'js/jquery.fullscreen.js';?>"></script>
    <script type="text/javascript" src="<?php echo FILE_URL . 'lib/tokeninput/jquery.tokeninput.js';?>"></script>
    <script type="text/javascript" src="<?php echo FILE_URL . 'lib/ckeditor/ckeditor.js';?>"></script>
    <script type="text/javascript" src="<?php echo FILE_URL . 'lib/repeatable/jquery.repeatable.js';?>"></script>
    <script type="text/javascript" src="<?php echo FILE_URL . 'lib/confirm/jquery-confirm.min.js';?>"></script>
    <script type="text/javascript" src="<?php echo FILE_URL . 'lib/bootstrap/js/bootstrap.bundle.min.js';?>"/></script>
    <script type="text/javascript" src="<?php echo FILE_URL . 'lib/textfill/jquery.textfill.min.js';?>"/></script>
<style type="text/css">
.wsite-header {
	background-image: url(<?php echo FILE_URL . 'images/header_images/header_image.png';?>) !important;
}
</style>

  <script type="text/javascript">
      function clickCount(id){
        var res_id = id;
        $.ajax({
            method: "POST",
            url: "<?php echo BASE_URL . '/admin/remotajax.php'?>",
            data:{
                "clickcount": 1,
                "res_id": res_id
            },
            success: function(){
            console.log('a click on the link of '+res_id+ ' is noted');
            },
            error:function(){
            console.log('click on the link of '+res_id+ ' is not noted');
            }
        });
      }
 $(document).ready(function () {
$(".subResIconHolder, .topicResIconHolder,.tagResIconHolder, .yes-tooltip").tooltip({html: true});
$('.content').textfill({
maxFontPixels: 22
});
});
  </script>
