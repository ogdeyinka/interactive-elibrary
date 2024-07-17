<!DOCTYPE html>
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8"/>
<?php  include('config.php'); ?>
<?php  include('includes/public_functions.php'); ?>
<?php
if(filter_has_var(INPUT_GET, 'link')) {
    $glink = filter_input(INPUT_GET, 'link', FILTER_SANITIZE_NUMBER_INT);
    $link_exist= false;
if(data_exist('links',$glink)){
$link_exist=true;
$link = getLink($glink);
$media = getMediatype($glink);
}else{$link_exist= false;}
}else{$link_exist= false;}
?>

<title>Polawa Interactive eLibrary | Resource Page</title>
<script type="text/javascript" src="<?php echo BASE_URL . '/static/js/jquery.min.js'; ?>"></script>
<script type="text/javascript">
// convert all characters to lowercase to simplify testing
    var agt=navigator.userAgent.toLowerCase();

    // *** BROWSER VERSION ***
    // Note: On IE5, these return 4, so use is_ie5up to detect IE5.
    var is_major = parseInt(navigator.appVersion);
    var is_minor = parseFloat(navigator.appVersion);

    // Note: Opera and WebTV spoof Navigator.  We do strict client detection.
    // If you want to allow spoofing, take out the tests for opera and webtv.
    var is_nav  = ((agt.indexOf('mozilla')!==-1) && (agt.indexOf('spoofer')===-1)
                && (agt.indexOf('compatible') === -1) && (agt.indexOf('opera')===-1)
                && (agt.indexOf('webtv')===-1) && (agt.indexOf('hotjava')===-1));
    var is_nav2 = (is_nav && (is_major === 2));
    var is_nav3 = (is_nav && (is_major === 3));
    var is_nav4 = (is_nav && (is_major === 4));
    var is_nav4up = (is_nav && (is_major >= 4));
    var is_navonly      = (is_nav && ((agt.indexOf(";nav") !== -1) ||
                          (agt.indexOf("; nav") !== -1)) );
    var is_nav6 = (is_nav && (is_major === 5));
    var is_nav6up = (is_nav && (is_major >= 5));
    var is_gecko = (agt.indexOf('gecko') !== -1);
    var is_safari = (agt.indexOf('safari') !== -1);    
	var is_chrome = (agt.indexOf('chrome') !== -1);  


    var is_ie     = ((agt.indexOf("msie") !== -1) && (agt.indexOf("opera") === -1) && (agt.indexOf('chrome') === -1));
    var is_ie3    = (is_ie && (is_major < 4));
    var is_ie4    = (is_ie && (is_major === 4) && (agt.indexOf("msie 4")!==-1) );
    var is_ie4up  = (is_ie && (is_major >= 4));
    var is_ie5    = (is_ie && (is_major === 4) && (agt.indexOf("msie 5.0")!==-1) );
    var is_ie5_5  = (is_ie && (is_major === 4) && (agt.indexOf("msie 5.5") !==-1));
    var is_ie5up  = (is_ie && !is_ie3 && !is_ie4);
    var is_ie5_5up =(is_ie && !is_ie3 && !is_ie4 && !is_ie5);
    var is_ie6    = (is_ie && (is_major === 4) && (agt.indexOf("msie 6.")!==-1) );
    var is_ie6up  = (is_ie && !is_ie3 && !is_ie4 && !is_ie5 && !is_ie5_5);
    
/* detect IE on Mac OS 8-9 */
    var is_mac = (agt.indexOf('mac') !== -1);
    var ieVn = (is_ie)?parseFloat(agt.substr(agt.indexOf('msie ')+5)):null;
    var is_ie_os9 = (is_mac && ( (is_ie5up && (ieVn <= 5.17)) || is_ie4 ));
    var is_po = (location.host === "<?php echo BASE_URL; ?>");
	
    // KNOWN BUG: On AOL4, returns false if IE3 is embedded browser
    // or if this is the first browser window opened.  Thus the
    // variables is_aol, is_aol3, and is_aol4 aren't 100% reliable.
    var is_aol   = (agt.indexOf("aol") !== -1);
    var is_aol3  = (is_aol && is_ie3);
    var is_aol4  = (is_aol && is_ie4);
    var is_aol5  = (agt.indexOf("aol 5") !== -1);
    var is_aol6  = (agt.indexOf("aol 6") !== -1);

    var is_opera = (agt.indexOf("opera") !== -1);
    var is_opera2 = (agt.indexOf("opera 2") !== -1 || agt.indexOf("opera/2") !== -1);
    var is_opera3 = (agt.indexOf("opera 3") !== -1 || agt.indexOf("opera/3") !== -1);
    var is_opera4 = (agt.indexOf("opera 4") !== -1 || agt.indexOf("opera/4") !== -1);
    var is_opera5 = (agt.indexOf("opera 5") !== -1 || agt.indexOf("opera/5") !== -1);
    var is_opera5up = (is_opera && !is_opera2 && !is_opera3 && !is_opera4);


	var nav_required=7.01;
	
	var nav_bad1=6.0;
	
	var nav_bad2=6.01;
	
	var nav_bad3=6.1;
	
	var nav_bad5=6.2; // added by marie
	
	var nav_bad4=7.0;


function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!==0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_findObj(n, d) { //v4.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!==null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

function MM_popupMsg(msg) { //v1.0
  alert(msg);
}

var isPO=true, d=document;
if(d.layers){var nn4 = true;}
var bc = new Array(10);
	bc.acrobat 		= false;	// Adobe Acrobat Reader
	bc.flash		= true;		// Flash
	bc.shockwave 	= true;		// Shockwave //MARIE
	bc.java 		= true;	// Java

var bc_plugins = ["acrobat", "flash", "shockwave", "java"];

var bc_topStyle = ["106","136","167","198"];

function bc_hideMe(x){
	var objID = bc_plugins[x],xDiv;
	if(d.getElementById && d.getElementById(objID)) { 
		xDiv = d.getElementById(objID);
		xDiv.parentNode.removeChild(xDiv);
		}
	else if (d.all && d.all(objID)) { 
		xDiv = d.all(objID);
		xDiv.parentNode.removeChild(xDiv);
		}
	else if(d.layers && d.layers[objID]) { 
		xDiv = d.layers[objID];
		xDiv.visibility = 'hidden';
		}
	else {}
}

var count=-1;
function bc_showMe(x){
	count++;
	var px = nn4?'':'px';
	var xStyle = getStyleObject(bc_plugins[x]);
	xStyle.top = bc_topStyle[count]+px;
	xStyle.visibility = 'visible';
	}

function bc_hideUnused(){
	for(i=0;i<bc_plugins.length;i++){
		if(bc[bc_plugins[i]]===true){bc_showMe(i);}
		else {bc_hideMe(i);}
	}
}

function getStyleObject(objID) {
	if(d.getElementById && d.getElementById(objID)) { // W3C DOM
	  return d.getElementById(objID).style;
	} else if (d.all && d.all(objID)) { // MSIE 4 DOM
	  return d.all(objID).style;
	} else if (d.layers && d.layers[objID]) { // NN 4 DOM
	  return d.layers[objID];
    } else {return false;}
}
</script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<style type="text/css" media="screen">
		html, body { height:100%; background-color: #FFFFFF;}
		body {margin:0; padding:0; overflow:hidden; text-align:center;}
        	#res-content {width:75%; height:100%; text-align: center; border: none;}
		</style>
</head>
<body>
  <?php include(ROOT_PATH . '/includes/noscript.php') ?>
    <?php if($link_exist===false): ?>
    <h2>Hey sorry, it seems your link has been messed up!!!</h2>
        <?php endif ?>
    <?php if($link_exist===true): ?>
    <?php if($media['name']==='Video'): ?>
    <video width="100%" height="100%" autoplay controls oncontextmenu="return false;">
    <source src="<?php echo $link['url'];?>" type="video/mp4">
    <p>Sorry, your browser doesn't support HTML5 video.</p>
    </video>
     <?php endif ?>
    <?php if($media['name']!=='Flash' && $media['name']!=='Video'): ?>
    <script language="JavaScript">
        $('iframe').on('load', function(){
           $('iframe').width($('iframe').contents().width()); 
        });    
     </script>       
    <iframe src="<?php echo $link['url'];?>" id="res-content">
    <?php endif ?>
    <?php if($media['name']==='Flash'): ?>
    <script language="javascript"><!--
var Flash;
 --></script><script language="vbscript"><!--
on error resume next
Flash = not IsNull(CreateObject("ShockwaveFlash.ShockwaveFlash"));
// --></script><script language="JavaScript">
<!--

if(bc.flash===true){

var app=navigator.appName;
var ie_latest=11.165;
var ie_required=4.6;


var app_ver = parseFloat(navigator.appVersion);
     //For IE5
var app_ver1 = navigator.appVersion;    
var app_ver1 = app_ver1.substring(22,25);
	
if (app.indexOf('Netscape') !== -1) {
 mimetype = navigator.mimeTypes["application/x-shockwave-flash"];
  if (mimetype) {
    plugin = mimetype.enabledPlugin;
    if (plugin) {
	document.write('<iframe src="<?php echo $link['url'];?>" id="res-content">');
} else {
	document.write('Flash Player is installed on your system but not activated on this browser. <br/>To activate it, go to ' + '<a href=\"about:addons\">about:addons</a> on Firefox or ' + '<a href=\"chrome://plugins/\">chrome://plugins/</a> on Chrome Browsers.');
      }
} else {
    document.write('Flash Player Plugin is not enabled on your device!<br/><a href=\"http://polawa.pow/#flash_instal\" target=\"_blank\">Get it here!</a>');
}

}

else if (app.indexOf('Microsoft') !== -1) {
if (navigator.platform.indexOf('Win') > -1) {
if (navigator.mimeTypes[0] !== null) {
	Flash = navigator.mimeTypes["application/x-shockwave-flash"].enabledPlugin;
}
pluginArray = new Array('Flash');
for (plugin in pluginArray) {
	if (eval(pluginArray[plugin])) {
  		document.write('<iframe src="<?php echo $link['url'];?>" id="res-content">');
	}
	else {
		document.write('Flash Player Plugin is not enabled on your device!<br/><a href=\"http://polawa.pow/#flash_instal\" target=\"_blank\">Get it here!</a>');
	}
}
} else if ((navigator.platform.indexOf('Mac') > -1)  || (navigator.platform.indexOf('PPC') > -1 )) {

if ((app_ver > ie_required) || (app_ver1 > ie_required)) {
	 mimetype = navigator.mimeTypes["application/x-shockwave-flash"];
  if (mimetype) {
    plugin = mimetype.enabledPlugin;
    if (plugin) {
	document.write('<iframe src="<?php echo $link['url'];?>" id="res-content">');
} else {
	document.write('Flash Player is installed but not activated on this browser. <br/> You can activate it via the plugins section in the menu.');
	}  
  } else {
	document.write('Flash Player Plugin is not enabled on your device!<br/><a href=\"http://polawa.pow/#flash_instal\" target=\"_blank\">Get it here!</a>'); 
}
	}  else {
	document.write('Sorry, your web browser doesn\'t flash player plugin');
      } 
  }
}

else if (app.indexOf('Opera') !== -1) {
 mimetype = navigator.mimeTypes["application/x-shockwave-flash"];
  if (mimetype) {
    plugin = mimetype.enabledPlugin;
    if (plugin) {
	document.write('<iframe src="<?php echo $link['url'];?>" id="res-content">');
} else {
	document.write('Flash Player is installed but not activated on this browser. <br/>To activate it, go to' + '<a href=\"opera://plugins\">opera://plugins</a>');
      }
} else {      
	document.write('Flash Player Plugin is not enabled on your device!<br/><a href=\"http://polawa.pow/#flash_instal\" target=\"_blank\">Get it here!</a>');
}

}}
//-->
</script>
    <?php endif ?>
        <?php endif ?>
	</body>
</html>
