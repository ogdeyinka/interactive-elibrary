<?php session_start(); ?>
<?php include('../config.php'); ?>
<?php include(ROOT_PATH . '/admin/includes/res_functions.php'); ?>
    <?php
global $conn;
$restypes = getAllResourcesType();
$subjects = getAllSubject();
$resources = getAllResources();
$rlevels = getSchlLevel();
$page = "addResource";
//$mediatypes = getAllMediatype();
?>
    <?php require_once(ROOT_PATH . '/admin/includes/head_section.php'); ?>
<title>Polawa Interactive e-Library Administration | Add Interactive Resource aids for Education</title>

 <!-- Display notification message --><?php include(ROOT_PATH . '/includes/messages.php') ?>
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
            <div class="action create-res-div noscript">
                <h1 class="page-title">Create Resources</h1>
                <form action="<?php echo BASE_URL . '/admin/add_resource.php'; ?>" enctype="multipart/form-data" method="post" >
                    <!-- validation errors for the form --><?php include(ROOT_PATH . '/includes/errors.php') ?>
                   <fieldset name="rFile">
                        <legend>Resource File Upload <br><span class="input-warn">(If you have multiple files for a resource, zip them and upload! Acceptable files are zip archive, mp4 and swf video files)</span></legend>
                        <span><input id="r_file" name="r_file" type="button" value="* Upload Resource File" /></span>             
                    </fieldset> 
                    <fieldset name="rImage">
                        <legend>* Resource Image Icon <span class="input-warn">(Ensure you attach an image file)</span></legend>
                        <div id="rimageShow">
                            <div id="loading"> Loading.... </div>
                            <?php if (isset($_SESSION['res_updated'])): ?>
                            <img src="<?php echo IMAGE_URL . $_SESSION['r_img']; ?>">
                            <?php endif ?>
                        </div>
                        <span><input id="r_icon" name="r_icon" type="button" value="* Upload Resource Preview Image Icon" /></span> 
                        <input id="r_img" name="r_img" style="display: none;" type="text"  <?php if (isset($_SESSION['res_updated'])){echo 'value="'. $_SESSION['r_img'].'"';} ?> required/>
                    </fieldset> <fieldset name="rdata" id="rdata">
                        <legend>* Resource Info</legend>
                        <input name="r_title" placeholder="* Resource's Title (Not more than 80 characters)" type="text" maxlength="80" required/>
                        <textarea id="r_info" class="info-edit" cols="30" name="r_info" placeholder="Information about the resource" rows="3"></textarea>
                        <input id="r_tags" name="r_tags" placeholder="Resource Tags(s)" type="text" required />
                        <input name="source" placeholder="Source of Resource" type="text" />
                    </fieldset>
                    <fieldset id="rLinkGroup" name="rLinkGroup">
                        <legend>* Resource Link(s)</legend>
                        <div>
                        <div class="link-container">
                        </div>
                        <input class="r_link_add" id="r_link_add" type="button" value="Add More Resource Link(s)" />
                        </div>   
                    </fieldset> 
                    <fieldset name="rTypeGroup" id="rTypeGroup">
                        <legend>* Resource types <span class="input-warn">(select at least one type)</span></legend>
                        <?php foreach ($restypes as $restype): ?>
                        <span>
                        <label><?php echo $restype['name']; ?>
                                    <!--img class="restype" src="<?php echo FILE_URL . 'images/' . $restype['icon']; ?>"/-->
                                <input name="restypes[]" type="checkbox" value="<?php echo $restype['id']; ?>" />
                        </label>
                          </span>  
                        <?php endforeach ?></fieldset> <br />
                    <fieldset name="rLevel" id="rLevel">
                        <legend>* Resource School Level <span class="input-warn"> (select at least one level)</span></legend>
                        <?php foreach ($rlevels as $rLevel): ?>
                        <span>
                        <label>
                            <?php echo $rLevel['level']; ?> 
                                <input name="rlevels[]" type="checkbox" value="<?php echo $rLevel['id']; ?>" />
                        </label>
                        </span>
                        <?php endforeach ?>
                    </fieldset>
                    <fieldset name="rTopic_Subject" id="rTopicSub">
                        <legend>* Resource Topic(s) with Subject Display <span class="input-warn">(Ensure at least a topic is selected)</span></legend>
                        <input id="ddtopic" name="topics_id" placeholder="Resource Topic(s)" type="text" required />
                        <input id="token_hidden" style="display: none;" type="text" />
                       <?php unset($_SESSION['r_img']); ?>
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
                    <!-- if editing post, display the update button instead of create button -->
                    <!--input type="hidden" name="create_resource" value="1"-->
                    <button class="res-btn res-btn-fix" name="create_resource" type="submit">Save Resource </button>
                </form>
            </div>
        </div>
 <?php require_once(ROOT_PATH . '/admin/includes/scripts.php'); ?>
<?php include( ROOT_PATH . '/includes/footer.php') ?>
<!--script>
CKEDITOR.replace('r_info');
</script-->
