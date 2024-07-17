  <script type="text/javascript">
<?php if (isset($_SESSION['res_updated']) || isset($_SESSION['res_saved'])) : ?>
     
$.confirm({
                    boxWidth: '600px',
                    useBootstrap: false,
                  title: false,
                    content: '<span style="font-size: 20px;">The Resource has been <?php if(isset($_SESSION['res_updated'])){echo'updated';} if(isset($_SESSION['res_saved'])){echo'saved';} ?> successfully! </span>'+'<?php if(isset($_SESSION['res_img_saved'])){echo '<div style="width:50%;margin:5px auto;"><img src="' .IMAGE_URL . $_SESSION['res_img_saved'].'"></div>';} ?>',
                     buttons: {
                         <?php if(isset($_SESSION['res_saved']) && isset($_SESSION['res_id_saved']) ):?>
                            Review: {
                                text: 'Review the Saved Resource',
                                btnClass: 'btn-blue',
                                action: function(){
                                     var  url = "<?php echo BASE_URL . "/admin/edit.php?resource=" . $_SESSION['res_id_saved'].'&editok='.md5(rand(1000, 99999));?>" 
                                     window.open(url,'popupwindow','width=1000, height=600, scrollbars, resizable');
                                 }
                            },
                            <?php endif ?>
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
                                    uploadFile: {
                                    text: 'Upload Resource File',
                                            btnClass: 'btn-blue',
                                            action: function () {
                                                 $('#r_file').click();
                                             }
                                             },
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
                              } <?php if(isset($_SESSION['res_updated'])):?>  ,
                               Resource: {
                               // text:  function(){if(1<window.history.length){var buttonText='Go to Manage Resources';}else{var buttonText='Close this page'} return buttonText;}, // Check if the page is popup or redirect
                                text: 'Close this page',
                                btnClass: 'btn-blue',
                                action: function () {
                                    window.close(); 
                                   if($('.resource-edit-page').length){
                                 var  url = "<?php echo BASE_URL . '/admin/resources.php'?>";
                                  window.open(url,'_self');
                                  }
                                    }
                                }
                                <?php endif ?>
                            }
                            });
                            
<?php endif ?>    
  </script>
<?php  unsetSession('res_id_saved');unsetSession('res_img_saved');unsetSession('res_updated');unsetSession('res_saved'); ?>
