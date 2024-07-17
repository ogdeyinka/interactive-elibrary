<?php  include('config.php'); ?>
<?php  include('includes/public_functions.php'); ?>
<?php
if(filter_has_var(INPUT_GET, 'topic')) {
 $topic = filter_input(INPUT_GET, 'topic', FILTER_SANITIZE_NUMBER_INT);
$topic_exist= false;
if(data_exist('topics',$topic)){
$topic_exist=true;
$resources = getTopicResources($topic);
$topicSub = getTopicSubjectNameById($topic);
$tags = getTopicTags($topic);
}else{$topic_exist= false;}
}else{$topic_exist= false;}
?>
<?php include('includes/head_section.php'); ?>
<style type="text/css">
.tag-minus{display:inline-block!important;position:relative;width:20px;height:20px;border-radius:10%;background-color:#001e46;border:calc(20px/8) solid #001e46;box-sizing:content-box}
.tag-minus:after{position:absolute;content:'';margin:auto;width:calc(20px/1.5);height:calc(20px/10);background-color:#fff;top:0;bottom:0;left:0;right:0}
.tag-plus{display:inline-block!important;position:relative;width:20px;height:20px;border-radius:10%;background-color:#001e46;border:calc(20px/8) solid #001e46;box-sizing:content-box}
.tag-plus:before{position:absolute;content:'';margin:auto;width:calc(20px/8);height:calc(20px/1.5);background-color:#fff;top:0;bottom:0;left:0;right:0}
.tag-plus:after{position:absolute;content:'';margin:auto;width:calc(20px/1.5);height:calc(20px/8);background-color:#fff;top:0;bottom:0;left:0;right:0}
 .tag-ctrl{float:right;width:25px; cursor: pointer}   
 .tag-ctrl-plus{display:block;margin-top:-2px;}
 .tag-ctrl-minus{display:block;margin-top:-24px;
}
</style>
<script type="text/javascript">
    <?php if ($topic_exist===false):?> 
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
<?php if ($topic_exist===true):?> 
 $(document).ready(function () {
if($("#tags").height()>32){
   $("#tags").css({"height":"31","overflow":"hidden"}); 
$("<div class='tag-ctrl'><span class='tag-ctrl-minus'><i class='tag-minus'></i> </span><span class='tag-ctrl-plus'><i class='tag-plus'></i></span></div>").insertAfter("#tags");
$(".tag-ctrl-minus").hide();
}
function tag_ctrl(this_ctrl,that_ctrl,tag_height){
$(this_ctrl).on("click touch", function(){
    $("#tags").css({"height":tag_height}); 
    $(this).hide();
   $(that_ctrl).show(); 
});
}
tag_ctrl(".tag-ctrl-plus",".tag-ctrl-minus","auto");
tag_ctrl(".tag-ctrl-minus",".tag-ctrl-plus","31");
});
<?php endif ?>
</script>
<title>Interactive Learning Aids for Education - <?php echo $topicSub ['topic_title'] . ' Under ' . $topicSub['subject']?></title>
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
                    <?php if(isset($topic) && $topic_exist===true):?>
                <h2 class="content-title noscript" ><?php echo  "<a href=' " . BASE_URL. " '> Subjects </a> >><a href=' " . BASE_URL. "/subject.php?subject=" . $topicSub['subject_id']. " '>  " .  $topicSub['subject']. "</a> >> " . $topicSub ['topic_title'] . " Resources (" . $resources_count . " in total).";?> </h2>
                    <?php if(!$tags){echo '<hr/>';} ?>
                    <!--hr /-->
                    <?php if ($tags): ?>
                    <div id="tags" class="noscript">
                     <?php foreach ($tags as $tag): ?>
                      <a class='tag' href='<?php echo BASE_URL. "/tag.php?tag=" . $tag['id']?>'> <?php echo ucfirst($tag['tag']); ?> </a>
                    <?php endforeach ?>
                    </div>
                    <?php endif; ?>
<?php  require_once( ROOT_PATH . '/includes/search-form.php') ?>
			<div style="height: 25px; overflow: hidden;"></div>
                        <div id="topicGallery" class="noscript" >
                <div id="innerGallery" >
                <?php foreach ($resources as $resource): ?>
                    <?php if ($resource['multilink']==0 && $resource['active']==1):?>
                        <!--div class="imageSmallContainer"-->
                            <div class="topicResource resources">
                                <div class="topicResIconHolder" title="<u><strong><?php echo $resource['title']; ?></strong></u><br/><?php if($resource['info']!=NULL){echo $resource['info'];}?>">
                                    <?php $links=$resource['link'];foreach ($links as $link):?>
                                        <a class="<?php if($link['modalview']==1){echo 'various';}?>" href="<?php echo BASE_URL . '/resource.php?link=' . $link['id'];?>" alt="<?php echo $resource['title'] ?>" target="_blank" onclick="clickCount(<?php echo $resource['id']; ?>)">
                                            <img class="resIcon" src="<?php echo IMAGE_URL . $resource['icon']; ?>" alt="<?php echo $resource['title']; ?>"/></a>
                                    <?php endforeach ?>
                                </div>
                            <div class="res-title"><strong><?php echo $resource['title']; ?></strong></div>
                            </div>
                        <!--/div-->
                    <?php endif ?>
                    <?php  if ($resource['multilink']==1 && $resource['active']==1 ):?>
                        <div class="topicResource resources">
                            <div class="topicResIconHolder overlay-big" title="<?php if($resource['info']!=NULL){echo $resource['info'];}else{echo '<strong>'. $resource['title'] .'</strong>}?>'; }?>">
                                <img class="resIcon" src="<?php echo IMAGE_URL . $resource['icon']; ?>" alt="<?php echo $resource['title']; ?>" />
                                <div class="work-item-overlay">
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
			<?php if ($resources_count == 0) {echo "<h2>Hey sorry, there is no content for " . getTopicNameById($topic) . ". Kindly inform your ICT Administrator about this. </h2>";}?>
        </div>       
<span style="height: 0px; overflow: hidden; display: block; clear: both;"></span>
            </div>
                    <?php endif ?>
                                </div> 

		<!-- footer -->
		<?php include( ROOT_PATH . '/includes/footer.php') ?>
		<!-- // footer -->
