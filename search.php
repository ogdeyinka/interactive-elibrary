<!-- The first include should be config.php -->
<?php require_once('config.php') ?>
<?php require_once( ROOT_PATH . '/includes/public_functions.php') ?>
<?php require_once( ROOT_PATH . '/includes/head_section.php') ?>
<?php
require_once( ROOT_PATH . '/includes/search-ctrl.php');
$queryin = trim(filter_input(INPUT_GET, 'query', FILTER_SANITIZE_STRING));
$min_length = 3;
if (!empty($queryin)) {
    $query=preg_replace('/[^\p{L}\p{N}\s]/u','',$queryin); // remove all symbols
        $sresources = ResourceSearch($query);
}
?> 
<title>Interactive Learning Aids for Education - Search Page </title>
</head>
<body>
    <style type="text/css">
        .search-container{display: none; }
    </style> 
    <?php include( ROOT_PATH . '/includes/navbar.php') ?>
    <div id="wrapper">
        <div id="header"><div class="wsite-header"></div></div>
        
        <div id="content-wrapper">
            
        <?php include_once( ROOT_PATH . '/includes/search-form.php') ?>
            <?php if (isset($query)): ?>
                <!--hr style="color: #dbdbdb;"-->
                <?php if (!empty($query) && $res_search_count!=0): ?>
                    <h2 class="content-title" ><?php echo 'Your Search query returns ' . $res_search_count . ' Resources.'; // From the search query the keyword(s) found and used '. qKeywords($query).'.'; ?> </h2>  
                    <div id="tags">
                    <?php echo '<span style="font-size:18px">Related topic(s) to your search query:</span><br/>'; foreach ($sresources as $resource): ?>
                        <?php  $topics=$resource['topics']; foreach ($topics as $topic){
                        $t_titles[]="";
                        if(is_array($t_titles) && in_array($topic['title'], $t_titles)){continue;}
                        $t_titles[]= $topic['title'];
                     echo "<a class='tag' href='" . BASE_URL. "/topic.php?topic=" . $topic['id']."' > " . ucfirst($topic['title'])."</a>";
                      } ?>
                    <?php endforeach ?>
                        </div>
                <div id="topicGallery" >
                    <?php foreach ($sresources as $resource): ?>
                    <?php if ($resource['multilink']==0 && $resource['active']==1):?>
                            <div class="topicResource resources">
                                <div class="topicResIconHolder" title="<?php if($resource['info']!=NULL){echo $resource['info'];}else{echo '<strong>'. $resource['title'] .'</strong>}?>';} ?>">
                                    <?php $links=$resource['link']; foreach ($links as $link):?>
                                        <a class="<?php if($link['modalview']==1){echo 'various';}?>" href="<?php echo BASE_URL . '/resource.php?link=' . $link['id'];?>" alt="<?php echo $resource['title'] ?>" target="_blank" onclick="clickCount(<?php echo $resource['id']; ?>)">
                                            <img class="resIcon" src="<?php echo IMAGE_URL . $resource['icon']; ?>" alt="<?php echo $resource['title']; ?>"/></a>
                                    <?php endforeach ?>
                                </div>
                                <div class="res-title"><strong><?php echo $resource['title']; ?></strong></div>
                            </div>
                    <?php endif ?>
                    <?php  if ($resource['multilink']==1 && $resource['active']==1 ):?>
                        <div class="topicResource resources">
                            <div class="topicResIconHolder overlay-big" title="<?php if($resource['info']!=NULL){echo $resource['info'];}else{echo '<strong>'. $resource['title'] .'</strong>}?>'; }?>">
                                <img class="resIcon" src="<?php echo IMAGE_URL . $resource['icon']; ?>" alt="<?php echo $resource['title']; ?>" />
                                <div class="work-item-overlay" >
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
            <?php endif ?>
    <?php if (!empty($query) && $res_search_count == 0) {
        echo "<h2>Hey sorry, there is no resource related to '" . $query . "'. <br> Kindly search again with another keyword. </h2>";
    }?>
                    <span style="height: 0px; overflow: hidden; display: block; clear: both;"></span>
                </div>
<?php endif ?>
        </div> 
        <!-- footer -->
<?php include( ROOT_PATH . '/includes/footer.php') ?>



