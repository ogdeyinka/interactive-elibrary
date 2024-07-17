<?php include('../config.php'); ?>
<?php include(ROOT_PATH . '/admin/includes/admin_functions.php'); ?>
<?php include(ROOT_PATH . '/admin/includes/res_functions.php'); ?>
<?php include(ROOT_PATH . '/admin/includes/head_section.php'); ?>
<?php require_once( ROOT_PATH . '/includes/search-ctrl.php') ?> 

<title>Polawa Interactive eLibrary Administration | Manage Resources</title>
</head>
<body>
    <div id="wrapper">
        <div id="header">
            <div class="wsite-header"></div>
        </div>

        <!-- Page content -->
        <div class="adm_container">
             <?php include(ROOT_PATH . '/includes/noscript.php') ?>
            <!-- Left side menu -->
            <?php include(ROOT_PATH . '/admin/includes/menu.php') ?>
            <!-- Display notification message -->
            <?php include(ROOT_PATH . '/includes/messages.php') ?>
            <?php
            $resources_count = 0;
            $allSubjects = getAllSubject();  
            if(filter_has_var(INPUT_GET, 'query')){
            $queryin = trim(filter_input(INPUT_GET, 'query', FILTER_SANITIZE_STRING));
            $min_length = 3;
            if (!empty($queryin)) {
            $query=preg_replace('/[^\p{L}\p{N}\s]/u','',$queryin); // remove all symbols
            $resources = ResourceSearch($query);
            $keywords = filterSearchKeys($query);
            }
            }elseif (filter_has_var(INPUT_GET, 'subject') && !filter_has_var(INPUT_GET, 'topic')) {
                $getsubject = filter_input(INPUT_GET, 'subject', FILTER_SANITIZE_NUMBER_INT);
                $resources = getAllSubResources($getsubject);
            }elseif(filter_has_var(INPUT_GET, 'subject') && filter_has_var(INPUT_GET, 'topic')) {
                $gettopic = filter_input(INPUT_GET, 'topic', FILTER_SANITIZE_NUMBER_INT);
                $resources = getTopicResources($gettopic);
            }else{
                
            }
            ?>
            <div class="admin-res noscript">
                <div style="text-align: center;">   <button type="submit" id="get-resource-search-btn" class="res-main-btn">Search Resources</button>  <button type="submit" id="get-resource-subtop-btn" class="res-main-btn">Get Resources by Subject or Topic</button> </div>
                <hr />
                <form id="res-search" action="" style="display: none;">
                    <input id="res-search-input" type="text" placeholder="Enter keyword to search resources" name="query" value="<?php if(isset($queryin)){echo $queryin;}?>"/>
                    <button type="submit">Search</button>
                </form>
                <form id="res-select" action="" style="display: none;">
                    <select name="subject" id="ddsubjects">
                        <option value="" selected disabled>Choose Resource Subject</option>
                <?php foreach ($allSubjects as $ddsubject): ?>
                            <option value="<?php echo $ddsubject['id']; ?>">
                <?php echo $ddsubject['name']; ?>
                            </option>
                <?php endforeach ?>
                    </select>
                    <br/>
                    <span id="topic-span">
                    </span>
                    <div style="text-align: center;">
                        <button type="submit" class="res-main-btn">Get Resources</button>
                    </div>
                </form>
                <hr/>
    <?php if (isset($getsubject) || isset($gettopic) || isset($query)) : ?>
                <h2 class="wsite-content-title" style="text-align: left;padding: 20px 20px 0;font-size: 22px;">
                    <?php 
            if (!(isset($query) || isset($gettopic)) && $subj_res_count!=0) {
                echo "The Total Number of Available Resources for " . getSubjectNameById($getsubject) . ': ' . $subj_res_count;
            }
            if (!isset($query) && $resources_count!=0) {
                echo "The Total Number of Available Resources for " . getTopicNameById($gettopic) . ' under ' . getSubjectNameById(filter_input(INPUT_GET, 'subject', FILTER_SANITIZE_NUMBER_INT)) . ': ' . $resources_count;
            }
            if (!(isset($getsubject) || isset($gettopic)) && $res_search_count!=0) {
                echo 'Your Search query returns ' . $res_search_count . ' Resources.'; // From the search query the keyword(s) found and used '. qKeywords($query).'.'; 
            }  ?> </h2>
                <div style="height: 20px; overflow: hidden;">
                </div>
                <?php if (!empty($resources)): ?>
    <?php foreach ($resources as $resource): ?>
        <?php if ($resource['multilink'] == 0): ?>
                            <div class="editResource resources">
                                <div class="editResIconHolder  <?php if ($resource['active'] == 0) {echo 'isDisable';} ?>" title="<u><strong><?php echo $resource['title']; ?></strong></u><br/><?php if($resource['info']!=NULL){echo $resource['info'];}else{echo '<s>No further infomation provided for this resource.</s>';} ?>">
            <?php $links = $resource['link'];
            foreach ($links as $link): ?>
                                        <a class="<?php if ($link['modalview'] == 1) { echo 'various';} ?>" href="<?php echo BASE_URL . '/resource.php?link=' . $link['id']; ?>" alt="<?php echo $resource['title'] ?>" >
                                            <img class="resIcon" src="<?php echo IMAGE_URL . $resource['icon']; ?>" alt="<?php echo $resource['title']; ?>"/></a>
                                                <?php endforeach ?>
                                </div>
                                <div class="edit_touch">
                                    <span> 
                                        <a class="fa fa-pencil res-btn edit" onclick="popupWindow(this.href); return false;" href="<?php echo BASE_URL . '/admin/edit.php?resource=' . $resource['id'].'&editok='.md5(rand(1000, 99999)); ?>">Edit</a>
                                        <a  class="fa res-btn active <?php if ($resource['active'] == 1) {echo 'fa-times res-enabled';} if ($resource['active'] == 0) {
                                        echo 'fa-check-square-o res-disabled';
                                    } ?>" data-id="<?php echo $resource['id']; ?>"><?php if ($resource['active'] == 1) {echo 'Disable';}if ($resource['active'] == 0) {echo 'Enable';} ?></a>
                                    </span>
                                </div>
                            </div>
                        <?php endif ?>
                        <?php if ($resource['multilink'] == 1): ?>
                            <div class="editResource resources">
                                <div class="editResIconHolder overlay-small <?php if ($resource['active'] == 0) { echo 'isDisable';} ?>" title="<u><strong><?php echo $resource['title']; ?></strong></u><br/><?php if($resource['info']!=NULL){echo $resource['info'];}else{echo '<s>No Infomation available for this resource.</s>';} ?>">
                                    <img class="resIcon" src="<?php echo IMAGE_URL . $resource['icon']; ?>" alt="<?php echo $resource['title']; ?>"   />
                                    <div class="work-item-overlay">
                                        <div class="inner">
                                            <ul>
            <?php $links = $resource['link'];
            foreach ($links as $link): ?>
                                                    <li>
                                                        <a class="gallery-btn <?php if ($link['modalview'] == 1) {echo ' various';} ?>" href="<?php echo BASE_URL . '/resource.php?link=' . $link['id']; ?>" target="_blank"><?php echo $link['name'] ?></a>
                                                    </li>
            <?php endforeach ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="edit_touch">
                                    <span> <a class="fa fa-pencil res-btn edit" onclick="popupWindow(this.href); return false;"
                                              href="<?php echo BASE_URL . '/admin/edit.php?resource=' . $resource['id'].'&editok='.md5(rand(1000, 99999)); ?>">Edit</a>
                                        <a  class="fa res-btn active <?php if ($resource['active'] == 1) {
                echo 'fa-times res-enabled';
            } if ($resource['active'] == 0) {
                echo 'fa-check-square-o res-disabled';
            } ?>" data-id="<?php echo $resource['id']; ?>"><?php if ($resource['active'] == 1) {
                echo 'Disable';
            }if ($resource['active'] == 0) {
                echo 'Enable';
            } ?></a>
                                    </span>
                                </div>
                            </div>
        <?php endif ?>
    <?php endforeach ?>
                <?php endif ?>
   <?php
    if ((isset($getsubject) || isset($gettopic)) && !isset($query) && !($resources_count || $subj_res_count)) {
        echo "<h2>Hey, no resource is available for your selection.</h2>";
    }
    if (isset($query) && !empty($query) && !isset($getsubject) && !isset($gettopic) && !$res_search_count) {
        echo "<h2>Hey sorry, there is no resource related to '" . $query . "'. <br> Kindly search again with another keyword. </h2>";
    }
    if (isset($query) && empty($query) && !isset($getsubject) && !isset($gettopic)) {
        echo "";
    } 
    ?>
<?php endif ?> 
            </div>
        </div>

        <script type="text/javascript">
            $(document).ready(function () {
      $('#res-search-input').tooltip({'trigger':'manual','title':'Your search query is empty!'});
       $('#res-search-input').click(function(){
           $(this).tooltip('hide');
       });
     $('#res-search-input').keypress(function(e){
       if(e.which===13 && $.trim($('#res-search-input').val()) === ""){
           $('#res-search-input').tooltip('show');
           setTimeout(function(){$('#res-search-input').tooltip('hide');},2000);
          return false;
       }  
     }); 
     $("#res-search-input").keyup(function() {
        if($.trim($('#res-search-input').val()) !== ""){
         $('#res-search-input').tooltip('hide');   
        } 
     });
     $('#res-search').submit(function(){
      if($.trim($('#res-search-input').val()) === ""){
         $('#res-search-input').tooltip('show');
         setTimeout(function(){$('#res-search-input').tooltip('hide');},2000);
         return false;
      }else{
          $('#res-search-input').tooltip('hide');  
          return true;
      }   
     });
                  <?php if (isset($query)) : ?>
                $('#res-search,.res-search').show();
                $('#get-resource-subtop-btn').removeClass('buttonDisable');
                $('#get-resource-search-btn').addClass('buttonDisable');
                 <?php endif ?>
                  <?php if (!isset($query)) : ?>
                $('#res-select,.res-select').show();
                $('#get-resource-search-btn').removeClass('buttonDisable');
                $('#get-resource-subtop-btn').addClass('buttonDisable');
                 <?php endif ?>   
            $('#get-resource-search-btn').on('click touch', function () {
                $('#res-search,.res-search').show();
                $('#res-select,.res-select').hide();
                $('#get-resource-subtop-btn').removeClass('buttonDisable');
                $('#get-resource-search-btn').addClass('buttonDisable');
            });
            $('#get-resource-subtop-btn').on('click touch', function () {
                $('#res-search,.res-search').hide();
                $('#res-select,.res-select').show();
                $('#get-resource-search-btn').removeClass('buttonDisable');
                $('#get-resource-subtop-btn').addClass('buttonDisable');
            });
                $("#ddsubjects").select2({
                    searchInputPlaceholder: 'Search to select subject'
                });
                $('#res-select button').addClass('buttonDisable');
                $("#topic-span").hide();
            });

            $('#ddsubjects').on('change', function () {
                var subject_id = $(this).val();
                var subject_name = $.trim($(this).find('option:selected').text());
                $("#topic-span").show();
                if (subject_id) {
                    $.ajax({
                        type: 'POST',
                        url: 'remotajax.php',
                        data: {
                            'subject_id': subject_id,
                            'subject_name': subject_name,
                            'display_mode':'dropdown'
                        },
                        beforeSend: function () {
                            $('#topic-span').html('<option value="" selected disabled>Loading Topics</option>');
                        },
                        success: function (data) {
                            $('#topic-span').html(data);
                            $("#ddtopics").select2({
                                searchInputPlaceholder: 'Search to select topic'
                            });
                            $('#res-select button').removeClass('buttonDisable');
                        }
                    });
                } else {
                    $('#topic-span').html('<select><option value="">Select Subject First</option></select>');
                }
            });
            $('.editResIconHolder').on('click touch', function (event) {
                $this = $(this);
                if ($this.hasClass('isDisable')) {
                    event.preventDefault();
                }
            });
            $('.active').on('click touch', function () {
                $this = $(this);
                var res_id = $this.data('id');
                if ($this.hasClass('res-enabled')) {
                    var text = 'Disable';
                    var active = 0;
                }
                if ($this.hasClass('res-disabled')) {
                    var text = 'Enable';
                    var active = 1;
                }
                $.confirm({
                    boxWidth: '270px',
                    useBootstrap: false,
                    title: '' + text + ' Resource!',
                    content: '<span style="font-size: 20px;">' + text + ' this resource?! </span>',
                    buttons: {
                        Yes: function () {
                            $.ajax({
                                method: "POST",
                                url: "<?php echo BASE_URL . '/admin/remotajax.php'; ?>",
                                data: {
                                    "active_res": res_id,
                                    "active": active
                                },
                                success: function (response) {
                                    var response = response['active'];
                                    //     alert ($this.parent().find('.editResIconHolder'));
                                    if (response === 0) {
                                        $this.closest('.editResource').find('.editResIconHolder').addClass('isDisable');
                                        $this.text('Enable');
                                        $this.removeClass('fa-times res-enabled');
                                        $this.addClass('fa-check-square-o res-disabled');
                                        //     console.log('This resource has been disabled');
                                    }
                                    if (response === 1) {
                                        $this.closest('.editResource').find('.editResIconHolder').removeClass('isDisable');
                                        $this.text('Disable');
                                        $this.removeClass('fa-check-square-o res-disabled');
                                        $this.addClass('fa-times res-enabled');
                                        //   console.log('This resource has been enabled');
                                    }
                                },
                                error: function () {
                                    console.log('Due to an error, this resource has not been disabled ');
                                }
                            });

                        },
                        No: function () {
                        }
                    }
                });

            });
        </script>
        <!-- // Page content -->
        <!-- footer -->
<?php include( ROOT_PATH . '/includes/footer.php') ?>
        <!-- // footer -->
