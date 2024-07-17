<?php session_start(); ?>
<?php include('../config.php'); ?>
<?php include(ROOT_PATH . '/admin/includes/res_functions.php'); ?>
<!-- Get all topics --><?php
global $conn;
$restypes_all = getAllResourcesType();
$subjects = getAllSubject();
$resources = getAllResources();
$rlevels = getSchlLevel();
$page = "editResource";
//$mediatypes = getAllMediatype();
?><?php require_once(ROOT_PATH . '/admin/includes/head_section.php'); ?>
<title>Polawa Interactive e-Library Administration | Add Interactive Resource aids for Education</title>
 <!-- Display notification message --><?php include(ROOT_PATH . '/includes/messages.php') ?>
 
     <?php if(!isset($_SESSION['res_to_edit']) || isset($_SESSION['res_to_edit']) && $_SESSION['res_to_edit']===false): ?>
 <script type="text/javascript">   
 $.confirm({ 
          boxWidth: '600px',
          useBootstrap: false,
          title: false,
          content: '<span style="font-size: 20px;">Sorry, there is no resource available for your request. <br/>Kindly check the link.</span>',
          buttons: {
              Exit:{
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
          }
     });
     </script>
 <?php endif ?>   
</head>

<body>

    <div id="wrapper">
        <div id="header">
            <div class="wsite-header">
            </div>
        </div>
        <!-- Page content -->
        <div class="adm_container">
             <?php include(ROOT_PATH . '/includes/noscript.php') ?>
            <!-- Left side menu --><?php include(ROOT_PATH . '/admin/includes/menu.php') ?>
            <!-- Middle form - to create and edit  -->
            <div class="action create-res-div resource-edit-page noscript">
                <h1 class="page-title">Edit Resources</h1>
                <?php if (isset($_SESSION['res_to_edit']) && $_SESSION['res_to_edit'] === true): ?>
                <form action="<?php echo BASE_URL . '/admin/edit.php?resource='.$res_id; ?>" enctype="multipart/form-data" method="post">
                    <!-- validation errors for the form --><?php include(ROOT_PATH . '/includes/errors.php') ?>
                <fieldset name="rActive" id="rActive">
                        <legend>* Resource Active Status</legend>   
                    <div class="r-active">
                    <input type="checkbox" name="r_active" value="1" class="r-switch-checkbox" id="r-active-label" <?php if ($r_active==1){ echo ' checked ';} ?> >
                    <label class="r-switch-label" for="r-active-label">
                    <span class="r-switch-inner r-active-inner"></span>
                    <span class="r-switch-ctrl r-active-switch-ctrl"></span>
                    </label>
                    </div>
                        </fieldset>
                       <fieldset name="rImage" id="rImage">
                        <legend>* Resource Image Icon</legend>
                        <div id="rimageShow">
                            <img src="<?php echo IMAGE_URL . $r_icon; ?>">
                        </div>
                        <input id="r_icon" name="r_icon" type="button" value="Change Resource Image Icon" />
                        <input id="r_img" name="r_img" style="display: none;" type="text" value="<?php echo $r_icon; ?>" required/>
                    </fieldset>
                    <fieldset id="rdata" name="rdata">
                        <legend>* Resource Info</legend>
                        <input type="hidden" readonly name="resource_id" value="<?php echo $res_id; ?>">
                        <input name="r_title" placeholder="* Resource's Title (Not more than 80 characters)" type="text" maxlength="80" value="<?php echo $r_title; ?>" required/>
                        <textarea id="r_info" class="info-edit" cols="30" rows="3" name="r_info" placeholder="Information about the resource" ><?php echo $r_info; ?></textarea>
                        <input id="r_tags" name="r_tags" placeholder="Resource Tags(s)" type="text" value="" required/>
                        <input name="source" placeholder="Source of Resource" type="text" value="<?php echo $source; ?>" />
                    </fieldset> <fieldset id="rLinkGroup" name="rLinkGroup">
                        <legend>* Resource Links</legend>
                        <div class="link-container">
                    <?php foreach ($rlinks as $rlink): ?>        
           <div class="repeatable-container">
            <table style="width: 100%">
            <tr>
            <td style="width: 75%">
            <input type="hidden" readonly name="link_ids[]" value="<?php echo $rlink['id']; ?>">
            <input type="url" name="link_url[]" value="<?php echo $rlink['url']; ?>" placeholder="* Enter Resource URL" required>
            <input type="text" name="link_name[]" value="<?php echo $rlink['name']; ?>" placeholder="* Enter Resource URL Name" maxlength="20"> 
            <label for="link_modalview[]"> Open Link in Modal view? <input type="checkbox" value="<?php echo $rlink['modalview']; ?>" <?php if ($rlink['modalview']==1){ echo ' checked ';} ?> name="link_modalview[]"/></label>
            </td>
            <td style="width: 25%; padding-top: 10px" valign="top">
                <a class="various" href="<?php $media =$rlink['mediatype']['name']; if($media=='Flash'){echo BASE_URL . '/resource.php?link=' . $rlink['id']; }else{echo $rlink['url'];}?>" >Preview this link</a>
            <input type="button" value="Delete this Link" class="r_link_delete" />
            </td>
            </tr>
            </table>
            </div>
                 <?php endforeach ?>           
                        </div>
                        <input class="r_link_add" type="button" value="Add More Resource Link(s)" />
                    </fieldset> <fieldset name="rTypeGroup" id="rTypeGroup">
                        <legend>* Resource types</legend>
                        <?php foreach ($restypes_all as $restype): ?>
                        <span><label>
                            <?php echo $restype['name']; ?>
                            <!--img class="restype" src="<?php echo FILE_URL . 'images/' . $restype['icon']; ?>"/-->
                            <input name="restypes[]" type="checkbox" <?php if (multi_in_array($restype['id'], $rtypes)){ echo ' checked ';} ?> value="<?php echo $restype['id']; ?>" />
                            </label></span>
                        <?php endforeach ?></fieldset>
                    <fieldset name="rLevel" id="rLevel">
                        <legend>* Resource School Level</legend>
                        <?php foreach ($rlevels as $rLevel): ?>
                        <span><label><?php echo $rLevel['level']; ?>
                                <input name="rlevels[]" type="checkbox" <?php if (multi_in_array($rLevel['id'], $reslevels)){ echo ' checked ';} ?> value="<?php echo $rLevel['id']; ?>" /></span>
                        </label></span>
                      <?php endforeach ?></fieldset>
                    <fieldset name="rTopic_Subject" id="rTopicSub">
                        <legend>* Resource Topic(s) with Subject Display</legend>
                        <input id="ddtopic" name="topics_id" placeholder="Resource Topic(s)" type="text" value="" required/>
                        <input id="token_hidden" style="display: none;" type="text" />
                        <!--div id="subject_display"></div-->
                        </label>
                    </fieldset>
                    <fieldset name="rSetting" id="rSetting">
                        <legend>* Resource Display Control</legend>
                     <label>Show Resource Directly on Subject Page? 
                      <div class="r-subjectshow">
                    <input type="checkbox" name="subjectshow" value="1" class="r-switch-checkbox" id="r-subjectshow" <?php if ($subpage_show==1){ echo ' checked ';} ?>  >
                    <label class="r-switch-label" for="r-subjectshow">
                    <span class="r-switch-inner r-subjectshow-inner"></span>
                    <span class="r-switch-ctrl r-subjectshow-switch-ctrl"></span>
                    </label>
                    </div> 
                     </label>  
                    </fieldset> <br /><hr />

                        <button class="res-btn res-btn-fix" name="update_resource" type="submit">Update Resource</button> <a class="res-btn res-btn-fix" onclick="self.close()" href="#">Cancel Update</a><!--button class="btn" name="create_resource" type="submit">Save as New Resource</button--> <a class="res-delete" href="<?php echo BASE_URL . '/admin/resources.php'; ?>">Delete This Resource</a>
                </form>
                <?php endif ?>
                
            </div>
        </div>
 <?php require_once(ROOT_PATH . '/admin/includes/scripts.php'); ?>
<?php include( ROOT_PATH . '/includes/footer.php') ?>
         <?php unsetSession('res_to_edit'); ?>
<!--script>
CKEDITOR.replace('r_info');
</script-->
