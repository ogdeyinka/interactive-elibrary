<?php  include('../config.php'); ?>
<?php  include(ROOT_PATH . '/admin/includes/admin_functions.php'); ?>
<?php  include(ROOT_PATH . '/admin/includes/res_functions.php'); ?>
<?php include(ROOT_PATH . '/admin/includes/head_section.php'); ?>
<?php require_once( ROOT_PATH . '/includes/search-ctrl.php') ?> 
	<title>Admin | Manage Resources</title>
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
                    <div class="admin-res noscript">
                    <form id="res-search" action="">
                    <input type="text" placeholder="Search.." name="query">
                    <button type="submit">Search</button>
                    </form>
                     <?php if (isset($query) && !empty($query) && $res_search_count): ?>
                         <hr />
		<h2 class="content-title" ><?php echo 'Your Search query returns ' . $res_search_count . ' Resources.'; // From the search query the keyword(s) found and used '. qKeywords($query).'.'; ?> </h2>
                        <?php foreach ($sresources as $resource): ?>
                            <?php  if ($resource['multilink']==0):?>
                                    <div class="editResource resources ">
                                        <div class="editResIconHolder  <?php if($resource['active']==0){ echo 'isDisable';} ?>" title="<u><strong><?php echo $resource['title']; ?></strong></u><br/><?php if($resource['info']!=NULL){echo $resource['info'];}else{echo '<s>No further infomation provided for this resource.</s>';} ?>">
                                            <?php $links=$resource['link']; foreach ($links as $link):?>
                                                <a class="<?php if($link['modalview']==1){echo 'various';}?>" href="<?php echo BASE_URL . '/resource.php?link=' . $link['id'];?>" alt="<?php echo $resource['title'] ?>" >
                                                    <img class="resIcon" src="<?php echo IMAGE_URL . $resource['icon']; ?>" alt="<?php echo $resource['title']; ?>"/></a>
                                            <?php endforeach ?>
                                        </div>
                                        <div class="edit_touch">
                                        <span> 
                                            <a class="fa fa-pencil btn edit" onclick="window.open(this.href, 'popupwindow', 'width=1000, height=600, scrollbars, resizable'); return false;" href="<?php echo BASE_URL . '/admin/edit.php?resource=' . $resource['id']; ?>">Edit</a>
                                            <a  class="fa btn active <?php if($resource['active']==1) {echo 'fa-times res-enabled';} if($resource['active']==0) {echo 'fa-check-square-o res-disabled';} ?>" data-id="<?php echo $resource['id']; ?>"><?php if($resource['active']==1) {echo 'Disable';}if($resource['active']==0){echo 'Enable';} ?></a>
                                        </span>
                                        </div>
                                    </div>
                            <?php endif ?>
                            <?php  if ($resource['multilink']==1):?>
                                <div class="editResource resources">
                                    <div class="editResIconHolder small-frame <?php if($resource['active']==0){ echo 'isDisable';} ?>" title="<u><strong><?php echo $resource['title']; ?></strong></u><br/><?php if($resource['info']!=NULL){echo $resource['info'];}else{echo '<s>No further infomation provided for this resource.</s>';} ?>">
                                        <img class="resIcon" src="<?php echo IMAGE_URL . $resource['icon']; ?>" alt="<?php echo $resource['title']; ?>"   />
                                        <div class="work-item-overlay">
                                            <div class="inner">
                                                <ul>
                                                    <?php $links=$resource['link']; foreach ($links as $link):?>
                                                        <li>
                                                            <a class="gallery-btn <?php if($link['modalview']==1){echo ' various';}?>" href="<?php echo BASE_URL . '/resource.php?link=' . $link['id'];?>" target="_blank"><?php echo $link['name']?></a>
                                                        </li>
                                                    <?php endforeach ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="edit_touch">
                                        <span> <a class="fa fa-pencil btn edit" onclick="window.open(this.href, 'popupwindow', 'width=1000, height=600, scrollbars, resizable'); return false;"
                                                  href="<?php echo BASE_URL . '/admin/edit.php?resource=' . $resource['id']; ?>">Edit</a>
                                         <a  class="fa btn active <?php if($resource['active']==1) {echo 'fa-times res-enabled';} if($resource['active']==0) {echo 'fa-check-square-o res-disabled';} ?>" data-id="<?php echo $resource['id']; ?>"><?php if($resource['active']==1) {echo 'Disable';}if($resource['active']==0){echo 'Enable';} ?></a>
                                        </span>
                                    </div>
                                </div>
                            <?php endif ?>
                        <?php endforeach ?>
                      <?php endif ?> 
    <?php if (isset($query) && !empty($query) && !$res_search_count) {
        echo "<hr /><h2>Hey sorry, there is no resource related to '" . $query . "'. <br> Kindly search again with another keyword. </h2>";
    } ?>
    <?php if (isset($query) && empty($query)) {
        echo "<hr /><h2>Hey your search query is empty!!!</h2>";
    } ?>
                    </div>
                </div>
               
           <script type="text/javascript">
               $(document).ready(function(){
            $("#ddsubjects").select2({
                 searchInputPlaceholder: 'Search to select subject'
            });
            $('#res-select button').addClass('buttonDisable');
               $("#topic-span").hide();
                });
                
        $('#ddsubjects').on('change', function(){
        var subject_id = $(this).val();
        var subject_name =  $.trim($(this).find('option:selected').text());
         $("#topic-span").show();
        if(subject_id){
            $.ajax({
                type:'POST',
                url:'remotajax.php',
                data:{
                    'subject_id':subject_id,
                    'subject_name':subject_name
                },
               beforeSend: function () {
             $('#topic-span').html('<option value="" selected disabled>Loading Topics</option>');
             },
                success:function(data){
                    $('#topic-span').html(data); 
             $("#ddtopics").select2({
                 searchInputPlaceholder: 'Search to select topic'
            });
            $('#res-select button').removeClass('buttonDisable');
                }
            }); 
        }else{
            $('#topic-span').html('<select><option value="">Select Subject First</option></select>');
        }
    }); 
                    $('.editResIconHolder').on('click', function(event){
                        $this = $(this);
                   if($this.hasClass('isDisable')){
                       event.preventDefault();
                   }
               });
               $('.active').on('click touch', function(){
                 $this = $(this);
                 var res_id = $this.data('id');
                 if ($this.hasClass('res-enabled')){
                     var text = 'Disable';
                     var active = 0;
                 } if($this.hasClass('res-disabled')){
                     var text = 'Enable';
                     var active = 1;
                 }
                 $.confirm({
                    boxWidth: '270px',
                    useBootstrap: false,
                    title: ''+ text + ' Resource!',
                    content: '<span style="font-size: 20px;">'+ text + ' this resource?! </span>',
                     buttons: {
                            Yes: function () {
                                 $.ajax({
                                    method: "POST",
                                    url: "<?php echo BASE_URL . '/admin/remotajax.php'?>",
                                    data:{
                                    "active_res": res_id,
                                    "active" : active
                                      },
                                    success: function(response){
                                      var response = response['active'];
                                 //     alert ($this.parent().find('.editResIconHolder'));
                                       if (response===0){
                                        $this.closest('.editResource').find('.editResIconHolder').addClass('isDisable');
                                        $this.text('Enable');
                                        $this.removeClass('fa-times res-enabled');
                                        $this.addClass('fa-check-square-o res-disabled');
                                   //     console.log('This resource has been disabled');
                                        }
                                        if(response===1){
                                            $this.closest('.editResource').find('.editResIconHolder').removeClass('isDisable');
                                            $this.text('Disable');
                                            $this.removeClass('fa-check-square-o res-disabled');
                                            $this.addClass('fa-times res-enabled');
                                         //   console.log('This resource has been enabled');
                                         }
                                },
                                error:function(){
                                console.log('Due to an error, this resource has not been disabled ' );
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
