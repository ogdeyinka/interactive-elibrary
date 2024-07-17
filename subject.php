<?php  include('config.php'); ?>
<?php  include('includes/public_functions.php'); ?>
<?php
if(filter_has_var(INPUT_GET, 'subject')){
    $subject = filter_input(INPUT_GET, 'subject', FILTER_SANITIZE_NUMBER_INT);
    $subject_exist= false;
    if(data_exist('subject',$subject)){
      $subject_exist=true;
        $topics = getSubject_topics($subject);
        $resources = getSubResources($subject);
    }else{$subject_exist= false;}
}else{$subject_exist= false;}
?>
<?php include('includes/head_section.php'); ?>
<title> Interactive Learning Aids for Education - <?php echo getSubjectNameById($subject) ?> Page </title>
<script type="text/javascript">
    <?php if ($subject_exist===false):?> 
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
<?php endif ?>
</script>
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
            <?php if(isset($subject) && $subject_exist===true):?>
		<h2  class="content-title noscript" ><?php echo "<a href=' " . BASE_URL. " '> Subjects </a>>> " . getSubjectNameById($subject) . " Topics <!-- : " . $topics_count . "-->" ?> </h2>
		<?php   include ( ROOT_PATH . '/includes/search-form.php') ?>
			<div style="height: 20px; overflow: hidden;"></div>
                        <div id="subjectGallery" class="noscript">
			<?php foreach ($topics as $topic): ?>
                         <?php   getTopicResources($topic['id']); if($resources_count!==0): ?>
                <div class="topicContainer">
								<a 	href="<?php echo BASE_URL . '/topic.php?topic=' . $topic['id'] ?>" alt="<?php echo $topic['title'] ?>">
                                    <div class="iconImage">
                                        <div class="screenTopic monitor">
                                            <div class="content">
                                                <span><?php echo ucwords($topic['title']) ?></span></div>
                                            <div class="base baseTopic">
                                                <div class="foot top"></div>
                                                <div class="foot bottom"></div>
                                                <div class="shadow"></div>
                                            </div>
                                        </div>
                                    </div>
								<!--img class="resIcon" src="<?php echo FILE_URL . 'images/'.$topic['icon']; ?>" style="position: absolute; border: 0; width: 88.29%; top: 0%; left: 5.86%" /-->
								</a>
				</div>
                            <?php endif ?>
			<?php endforeach ?>
                <?php if ($topics_count == 0) {
                    echo "<h2>Hey sorry, there is no topic for " . getSubjectNameById($subject) . ". Kindly inform your ICT Administrator about this. </h2>";
                }
                ?>
                <span style="display: block; clear: both; height: 0px; overflow: hidden;"></span>
		</div>
            <div style="height:0px; overflow: hidden;">

            </div>
            <?php if ($resources): ?>
                <br/><br/>
            <div id="subjectResources" class="noscript">
                <h2>Some <?php echo ucwords(getSubjectNameById($subject))?> Relevant Resources:</h2>
                <hr/>
                <?php foreach ($resources as $resource): ?>
                <?php  if ($resource['multilink']==0):?>
                        <div class="subjectResource resources">
                            <div class="subResIconHolder" title="<?php if($resource['info']!=NULL){echo $resource['info'];}else{echo '<strong>'. $resource['title'] .'</strong>}?>'; }?>">
                                    <?php $links=$resource['link'];foreach ($links as $link):?>
                                         <a class="<?php if($link['modalview']==1){echo 'various';}?>" href="<?php echo BASE_URL . '/resource.php?link=' . $link['id'];?>" alt="<?php echo $resource['title'] ?>" target="_blank" onclick="clickCount(<?php echo $resource['id']; ?>)">
                                            <img class="resIcon" src="<?php echo IMAGE_URL . $resource['icon']; ?>" alt="<?php echo $resource['title']; ?>"/></a>
                                    <?php endforeach ?>
                            </div>
                            <div class="res-title"><strong><?php echo $resource['title']; ?></strong></div>
                        </div>
                    <?php endif ?>
                <?php  if ($resource['multilink']==1):?>
                    <div class="subjectResource resources">
                        <div class="subResIconHolder overlay-small" title="<?php if($resource['info']!=NULL){echo $resource['info'];}else{echo '<strong>'. $resource['title'] .'</strong>}?>'; }?>">
                                <img class="resIcon" src="<?php echo FILE_URL . 'images/'.$resource['icon']; ?>" />
                                <div class="work-item-overlay" title="<?php if($resource['info']!=NULL){echo $resource['info'];} ?>">
                                    <div class="inner">
                                        <ul>
                                            <?php $links=$resource['link']; foreach ($links as $link):?>
                                                <li>
                                                     <a class="gallery-btn <?php if($link['modalview']==1){echo ' various';}?>" href="<?php echo BASE_URL . '/resource.php?link=' . $link['id'];?>" target="_blank" onclick="clickCount(<?php echo $resource['id']; ?>)"><?php echo $link['name']?></a></li>
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
                <span style="display: block; clear: both; height: 0px; overflow: hidden;"></span>
            </div>
            <?php endif ?>
            <?php endif ?>
        </div>

		<!-- // Page content -->

		<!-- footer -->
		<?php include( ROOT_PATH . '/includes/footer.php') ?>
		<!-- // footer -->
