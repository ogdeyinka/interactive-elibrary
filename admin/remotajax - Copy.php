<?php session_start();  ?>
<?php  include('../config.php'); ?>
<?php  include(ROOT_PATH . '/admin/includes/admin_functions.php'); ?>
<?php  include(ROOT_PATH . '/admin/includes/res_functions.php'); ?>
<?php

if($isAjax) { 
if (filter_has_var(INPUT_POST, 'ajaxtopicsave')) {createAjaxTopic(filter_input_array(INPUT_POST));} //pass to create new topic with subject with ajax.
if (filter_has_var(INPUT_POST, 'ajaxtagsave')) {createAjaxTag(filter_input_array(INPUT_POST));} //pass to create new tag with ajax.
if (filter_has_var(INPUT_POST, 'clickcount')) {resLinkClick(filter_input_array(INPUT_POST));}  // resources click count via ajax
if (filter_has_var(INPUT_POST, 'linkcheck')) {linkCheck(filter_input_array(INPUT_POST));}  // link check via ajax
if (filter_has_var(INPUT_POST, 'active_res')&& filter_has_var(INPUT_POST, 'active')){resDisable(filter_input_array(INPUT_POST));} // pass to disable resources.
if (filter_has_var(INPUT_POST, 'res_id')&& filter_has_var(INPUT_POST,'delete')){deleteResource(filter_input_array(INPUT_POST));} // pass to delete resources.

// Get resource topics with respective subject via ajax

if(filter_has_var(INPUT_GET, 'topic_q')) {
    $ddtopic = esc(filter_input(INPUT_GET, 'topic_q', FILTER_SANITIZE_STRING));
    $ddtopics = getAllTopicsSubject($ddtopic);
    $response = array();
    if (count($ddtopics) > 0) {
        foreach ($ddtopics as $ddtopic) {
            $response[] = array("id" => $ddtopic['topic_id'], "title" => $ddtopic['topic_title'],"subject" => $ddtopic['subject']);
        }
    } 
    echo json_encode($response);
}

// Get resource tags list


if(filter_has_var(INPUT_GET, 'tag_q')) {
    $tag= esc(filter_input(INPUT_GET, 'tag_q', FILTER_SANITIZE_STRING));
    $tags = getAllTags($tag);
    $response = array();
    if (count($tags) > 0) {
        foreach ($tags as $tag) {
            $response[] = array("id" => $tag['tag_id'], "tag" => $tag['tag']);
        }
    } 
    echo json_encode($response);
}

if(filter_has_var(INPUT_POST, 'remdelfilekey')){  //Ajax to delete delkey session
 unsetSession('delkey');   
}

/* - - - - - - - - - - 
- Function to delete image in target directory to prevent keeping irrelevant via ajax
- - - - - - - - - - -*/
if(filter_has_var(INPUT_POST, 'resImageUpdate')){
  unsetSession('delkey');
if (isset($_SESSION['resImageDone'])){
$_SESSION['delkey'] = md5(rand(1000, 9999));
echo json_encode($_SESSION['delkey']);
exit;
}
}
if(filter_has_var(INPUT_POST, 'filetodel') && filter_has_var(INPUT_POST, 'delfilekey') && filter_input(INPUT_POST,'delfilekey') == $_SESSION['delkey']) {
// if(filter_input(INPUT_POST,'delkey') == $_SESSION['delkey']){   
$r_img = filter_input(INPUT_POST, 'filetodel', FILTER_SANITIZE_STRING);
$target = IMAGE_DIR . $r_img;
if (file_exists($target)){ 
   if (unlink($target)) {
    echo json_encode(array('status' =>'File successfully deleted'));
 } else {
     echo json_encode(array('status' =>'File failed to be deleted'));
 }
} else {
   echo json_encode(array('Message' =>'File not found')); 
}
    unsetSession('delkey');
     exit();
}


/* - - - - - - - - - - 
-  Function to load list of all subject via ajax
- - - - - - - - - - -*/

if(filter_has_var(INPUT_POST, 'ajaxsubjects')){
    $ddsubjects = getAllSubject();
    $response = array();
    if (count($ddsubjects) > 0) {
        foreach ($ddsubjects as $ddsubject) {
            $response[] = array("id" => $ddsubject['id'], "name" => $ddsubject['name']);
        }
    }
  //  print_r($ddtopics);
    echo json_encode($response);
}
}
/* - - - - - - - - - - 
-  upload image via ajax
- - - - - - - - - - -*/
if (filter_has_var(INPUT_POST, 'imageupload')) {
    if (is_uploaded_file($_FILES['r_image']['tmp_name']) ) {
$r_iconname = $_FILES['r_image']['name'];
$sourcePath = $_FILES['r_image']['tmp_name'];
$ext = findexts ($r_iconname) ;
$filename = filter_var(generateRandomString().'.'. $ext, FILTER_SANITIZE_STRING);
$target = IMAGE_DIR . $filename;
if (file_exists($target)){ 
    $filename = filter_var(generateRandomString().'.'. $ext, FILTER_SANITIZE_STRING);
    $target = IMAGE_DIR.$filename;
     } 
if(move_uploaded_file($sourcePath,$target)) {
            $_SESSION['resImageDone'] = '1';
            echo json_encode(array('filename' =>$filename /*,'imagedone'=>$_SESSION['resImageDone']*/));
           // unsetSession('delkey');
            exit();
}else{
    echo json_encode(array('status' =>'failed'));
  //  unsetSession('delkey');
    exit();
}
}
}

//Code to get dropdown list of all topics by subject
 $ddsubject = false;
if(filter_has_var(INPUT_POST, 'subject_id')) {
    $subject = esc(filter_input(INPUT_POST, 'subject_name', FILTER_SANITIZE_STRING));
    $displaymode = esc(filter_input(INPUT_POST, 'display_mode', FILTER_SANITIZE_STRING));
    $ddtopics = getSubject_topics(esc(filter_input(INPUT_POST, 'subject_id', FILTER_SANITIZE_NUMBER_INT)));
    if($displaymode=='dropdown'){
    echo "<select name='topic' id='ddtopics'>";
    echo "<option value='' selected disabled>Choose " . $subject. " Topic</option>";
    echo "<option value='0' >Get All " . $subject. " Resources</option>";
    foreach ($ddtopics as $ddtopic) {
        getTopicResources($ddtopic['id']); if($resources_count!==0){
        echo "<option value='" . $ddtopic['id'] . "'>" . $ddtopic['title'] . "</option>";
    }
    }
      echo "</select>";
    }
    if($displaymode=='grid'){
        
    }
    }

