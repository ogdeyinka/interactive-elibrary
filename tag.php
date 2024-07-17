<?php  include('config.php'); ?>
<?php  include('includes/public_functions.php'); ?>

<?php
if(filter_has_var(INPUT_GET, 'tag')) {
    $tag = filter_input(INPUT_GET, 'tag', FILTER_SANITIZE_NUMBER_INT);
    $tag_exist= false;
if(data_exist('tags',$tag)){
$tag_exist=true;
$resources = getTagResources($tag);
$tag = getTagNameById($tag);
}else{$tag_exist= false;}
}else{$tag_exist= false;}
?>
<?php include('includes/head_section.php'); ?>
<title>Interactive Learning Aids for Education <?php if($tag_exist){ echo "- ". $tag['tag']; }else{echo "Error Page";}?></title>
    <?php if ($tag_exist===false):?> 
<script type="text/javascript">
 $.confirm({ 
          boxWidth: '600px',
          useBootstrap: false,
          title: false,
          content: '<span style="font-size: 20px;">Hey Sorry, it seems there is something wrong with your link. <br/>Kindly check the link.</span>',
          buttons: {
              Exit:{
                 text: 'Go back to Subject Page',
                       btnClass: 'btn-blue',
                       action: function () {
                           var  url = "<?php echo BASE_URL ?>";
                           window.open(url,'_self');
                   }
              }
          }
     });
     </script>
<?php endif ?>
</head>
<body>
<?php include( ROOT_PATH . '/includes/navbar.php') ?>
	<div id="wrapper">
	<div id="header">
		<div class="wsite-header"></div>
	</div>
		<!-- Page content -->
				<div id="content-wrapper">
              <?php include(ROOT_PATH . '/includes/noscript.php') ?>
                    <?php if(isset($tag) && $tag_exist===true):?>
		<h2 class="content-title noscript" >
                    <?php
// Check if Referral URL exists
 if(filter_has_var(INPUT_SERVER, 'HTTP_REFERER')) {
   $refUrlDomain = getDomainName(filter_input(INPUT_SERVER,'HTTP_REFERER'));
   if ($refUrlDomain == getDomainName(BASE_URL)){
  echo "<a href=' " .filter_input(INPUT_SERVER,'HTTP_REFERER'). " '>Back</a> >> " ;
   }
}
?>
        <?php echo "Resources related to '" .$tag['tag'] . "' (" . $resources_count . " in total)." ;?> </h2>
        <?php if($tag['def']==NULL):?>
            <hr/>
            <?php endif ?>  
            <?php if($tag['def']!=NULL):?>
            <div id="def">
               <span> <?php echo $tag['def']; ?> </span>
             </div> 
              <?php endif ?> 
			<div style="height: 20px; overflow: hidden;"></div>
			<div id="topicGallery" class="noscript">
                <?php foreach ($resources as $resource): ?>
                    <?php if ($resource['multilink']==0 && $resource['active']==1):?>
                            <div class="tagResource resources">
                                <div class="tagResIconHolder" title="<?php if($resource['info']!=NULL){echo $resource['info'];}else{echo '<strong>'. $resource['title'] .'</strong>}?>'; }?>">
                                    <?php $links=$resource['link'];foreach ($links as $link):?>
                                        <a class="<?php if($link['modalview']==1){echo 'various';}?>" href="<?php $media =$link['mediatype']['name']; if($media=='Flash'){echo BASE_URL . '/resource.php?link=' . $link['id']; }else{echo $link['url'];}?>" alt="<?php echo $resource['title'] ?>" target="_blank" onclick="clickCount(<?php echo $resource['id']; ?>)">
                                            <img class="resIcon" src="<?php echo IMAGE_URL . $resource['icon']; ?>" alt="<?php echo $resource['title']; ?>" title="<?php echo $resource['info']; ?>"/></a>
                                    <?php endforeach ?>
                                </div>
                                <div class="res-title"><strong><?php echo $resource['title']; ?></strong></div>
                            </div>
                    <?php endif ?>
                    <?php  if ($resource['multilink']==1 && $resource['active']==1 ):?>
                        <div class="tagResource resources">
                            <div class="tagResIconHolder overlay-big" title="<?php if($resource['info']!=NULL){echo $resource['info'];}else{echo '<strong>'. $resource['title'] .'</strong>}?>'; }?>">
                                <img class="resIcon" src="<?php echo IMAGE_URL . $resource['icon']; ?>" alt="<?php echo $resource['title']; ?>" />
                                <div class="work-item-overlay" title="<?php echo $resource['info']; ?>">
                                    <div class="inner">
                                        <ul>
                                            <?php $links=$resource['link']; foreach ($links as $link):?>
                                                <li>
                                                    <a class="gallery-btn <?php if($link['modalview']==1){echo ' various';}?>" href="<?php $media =$link['mediatype']['name']; if($media=='Flash'){echo BASE_URL . '/resource.php?link=' . $link['id']; }else{echo $link['url'];}?>" target="_blank" onclick="clickCount(<?php echo $resource['id']; ?>)"><?php echo $link['name']?></a></li>
                                            <?php endforeach ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                             <div class="link-count"><span><?php echo count($links).""; ?><i class="fa fa-file"></i></span></div>
                        <div class="res-title"><strong><?php echo $resource['title']; ?></strong></div>
                        </div>
                    <?php endif ?>
                <?php endforeach ?>
			<?php if ($resources_count == 0) {echo "<h2>Hey sorry, there is no content for " . getTopicNameById($topic) . ". Kindly inform your ICT Administrator about this. </h2>";}?>
<span style="height: 0px; overflow: hidden; display: block; clear: both;"></span>
            </div>
                    <?php endif ?>
                </div> 
		<!-- footer -->
		<?php include( ROOT_PATH . '/includes/footer.php') ?>
		<!-- // footer -->
