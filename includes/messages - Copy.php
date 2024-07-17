<?php  if (isset($_SESSION['message'])) : ?>
      <!--div class="message" >
      	<p>
          <?php 
          //	echo $_SESSION['message']; 
          //	unset($_SESSION['message']);
          ?>
      	</p>
      </div-->
<?php endif ?>
<?php if (isset($_SESSION['res_updated']) || isset($_SESSION['res_saved'])) : ?>
       <script type="text/javascript">
$.confirm({
                    boxWidth: '600px',
                    useBootstrap: false,
                  title: false,
                    content: '<span style="font-size: 20px;">The Resource has been <?php if(isset($_SESSION['res_updated'])){echo'updated';} if(isset($_SESSION['res_saved'])){echo'saved';} ?> successfully! </span>',
                     buttons: {
                             last-res: {
                                text:<?php if(isset($_SESSION['res_id_saved'])):?>
                      //New Resource  
                                <?php if ($resource['multilink'] == 0): ?>
                            "<div class='editResource resources \'>
                                <div class='editResIconHolder' title=\"<u><strong><?php echo $resource['title']; ?></strong></u><br/><?php if($resource['info']!=NULL){echo $resource['info'];}else{echo '<s>No further infomation provided for this resource.</s>';} ?>\">
            <?php $links = $resource['link'];
            foreach ($links as $link): ?>
                                        <a class="<?php if ($link['modalview'] == 1) { echo 'various';} ?>" href="<?php echo BASE_URL . '/resource.php?link=' . $link['id']; ?>" alt="<?php echo $resource['title'] ?>" >
                                            <img class="resIcon" src="<?php echo IMAGE_URL . $resource['icon']; ?>" alt="<?php echo $resource['title']; ?>"/></a>
                                                <?php endforeach ?>
                                </div>
                                <div class="edit_touch">
                                    <span> 
                                        <a class="fa fa-pencil btn edit" onclick="window.open(this.href, 'popupwindow', 'width=1000, height=600, scrollbars, resizable'); return false;" href="<?php echo BASE_URL . '/admin/edit.php?resource=' . $resource['id']; ?>">Edit</a>
                                        <a  class="fa btn active <?php if ($resource['active'] == 1) {echo 'fa-times res-enabled';} if ($resource['active'] == 0) {
                                        echo 'fa-check-square-o res-disabled';
                                    } ?>" data-id="<?php echo $resource['id']; ?>"><?php if ($resource['active'] == 1) {echo 'Disable';}if ($resource['active'] == 0) {echo 'Enable';} ?></a>
                                    </span>
                                </div>
                            </div>
                        <?php endif ?>
                        <?php if ($resource['multilink'] == 1): ?>
                            <div class="editResource resources">
                                <div class="editResIconHolder <?php if ($resource['active'] == 0) { echo 'isDisable';} ?>" title="<u><strong><?php echo $resource['title']; ?></strong></u><br/><?php if($resource['info']!=NULL){echo $resource['info'];}else{echo '<s>No Infomation available for this resource.</s>';} ?>">
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
                                    <span> <a class="fa fa-pencil btn edit" onclick="window.open(this.href, 'popupwindow', 'width=1000, height=600, scrollbars, resizable'); return false;"
                                              href="<?php echo BASE_URL . '/admin/edit.php?resource=' . $resource['id']; ?>">Edit</a>
                                        <a  class="fa btn active <?php if ($resource['active'] == 1) {
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
                                <?php endif ?>
                            },
                            Stay: {
                                 text: '<?php if(isset($_SESSION['res_updated'])){echo'Review the Edited Resource';} if(isset($_SESSION['res_saved'])){echo'Add Another Resource';} ?>',
                                 btnClass: 'btn-blue'<?php if(isset($_SESSION['res_saved'])):?>,
                                 action: function(){
                                    // $('#r_icon').click();
                                     $.confirm({
                                    boxWidth: '40%',
                                    useBootstrap: false,
                                    title: 'What do you want to start with?',
                                    content: '',
                                    buttons: {
                                    uploadImage: {
                                    text: 'Upload Icon Image',
                                            btnClass: 'btn-blue',
                                            action: function () {
                                                 $('#r_icon').click();
                                             }
                                             },
                                    urlLink: {
                                      text: 'Enter Resource url link',
                                            btnClass: 'btn-blue',
                                            action: function () {
                                                 $('html, body').animate({ scrollTop: $("#rLinkGroup").offset().top }, 500);
                                             }
                                    },
                                    cancel: {
                                       text: 'Let me decide!',
                                            btnClass: 'btn-blue'
                                    }
                                             }
                                    });
                                 }
                                 <?php endif ?>
                              },
                               Resource: {
                               // text:  function(){if(1<window.history.length){var buttonText='Go to Manage Resources';}else{var buttonText='Close this page'} return buttonText;}, // Check if the page is popup or redirect
                                text: 'Close this page',
                                btnClass: 'btn-blue',
                                action: function () {
                                    <?php if(isset($_SESSION['res_updated'])):?>
                                    window.close();
                                    <?php unset($_SESSION['res_updated']); ?>
                                    <?php endif ?>
                                    <?php if(isset($_SESSION['res_saved'])):?>
                                    var  url = "<?php echo BASE_URL . '/admin/resources.php'?>";
                                    window.open(url,'_self');
                                    <?php unset($_SESSION['res_saved']); ?>
                                    <?php endif ?>
                                    }
                                }
                            }
                            });
                            </script>
                            <?php unset($_SESSION['res_updated']); ?>
<?php endif ?>
