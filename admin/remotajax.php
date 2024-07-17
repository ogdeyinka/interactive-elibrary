<?php session_start();  ?>
<?php  include('../config.php'); 
header("Access-Control-Allow-Origin: ".BASE_URL);
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 1000");
header("Access-Control-Allow-Headers: x-requested-with, x-file-name, x-index, x-total, x-hash, Content-Type, origin, authorization, accept, client-security-token");
?>
<?php  include(ROOT_PATH . '/admin/includes/admin_functions.php'); ?>
<?php  include(ROOT_PATH . '/admin/includes/res_functions.php'); ?>
<?php

if($isAjax) { 
if (filter_has_var(INPUT_POST, 'ajaxtopicsave') && filter_has_var(INPUT_POST, 'topic_name')) {createAjaxTopic(filter_input_array(INPUT_POST));} //pass to create new topic with subject with ajax.
if (filter_has_var(INPUT_POST, 'ajaxtopicupdate') && filter_has_var(INPUT_POST, 'topic_name')) {updateAjaxTopic(filter_input_array(INPUT_POST));} //pass to create new topic with subject with ajax.
if (filter_has_var(INPUT_POST, 'ajaxsubjectsave') && filter_has_var(INPUT_POST, 'subject_name')) {saveAjaxSubject(filter_input_array(INPUT_POST));} //pass to create new subject.
if (filter_has_var(INPUT_POST, 'ajaxsubjectupdate') && filter_has_var(INPUT_POST, 'subject_name')) {updateAjaxSubject(filter_input_array(INPUT_POST));} //pass to update new subject.
if (filter_has_var(INPUT_POST, 'ajaxtagsave')) {createAjaxTag(filter_input_array(INPUT_POST));} //pass to create new tag with ajax.
if (filter_has_var(INPUT_POST, 'clickcount')) {resLinkClick(filter_input_array(INPUT_POST));}  // resources click count via ajax
if (filter_has_var(INPUT_POST, 'linkcheck')) {linkCheck(filter_input_array(INPUT_POST));}  // link check via ajax
if (filter_has_var(INPUT_POST, 'active_res') && filter_has_var(INPUT_POST, 'active')){resDisable(filter_input_array(INPUT_POST));} // pass to disable resources.
if (filter_has_var(INPUT_POST, 'res_id') && filter_has_var(INPUT_POST,'delekey') && isset($_SESSION['resource_del']) && filter_input(INPUT_POST,'delekey') == $_SESSION['resource_del']){deleteResource(filter_input_array(INPUT_POST));} // pass to delete resources.
if (filter_has_var(INPUT_POST, 'topic_id') && filter_has_var(INPUT_POST,'delekey') && isset($_SESSION['topic_del']) && filter_input(INPUT_POST,'delekey') == $_SESSION['topic_del']){deleteTopic(filter_input_array(INPUT_POST)); unsetSession('topic_del');} // pass to delete topic.
if (filter_has_var(INPUT_POST, 'subject_id') && filter_has_var(INPUT_POST,'delekey') && isset($_SESSION['subject_del']) && filter_input(INPUT_POST,'delekey') == $_SESSION['subject_del']){deleteSubject(filter_input_array(INPUT_POST)); unsetSession('topic_del');} // pass to delete subject.

//Delete files that are no longer needed
if(filter_has_var(INPUT_POST, 'resfiletodel') && filter_has_var(INPUT_POST, 'delekey') && isset($_SESSION['resfile_del']) && filter_input(INPUT_POST,'delekey') == $_SESSION['resfile_del']) {  
$resfile = filter_input(INPUT_POST, 'resfiletodel', FILTER_SANITIZE_STRING);
$ext = pathinfo(strtolower($resfile), PATHINFO_EXTENSION);
//$target = RES_DIR . $r_img;\
if(!empty($resfile)){
    if(empty($ext)){ // directory
    $dir = RES_DIR.$resfile;
}else{
    $dir = RES_DIR.'videos/'.$resfile;
}
}
if (file_exists($dir)){ 
   if (deleteFileDir($dir)) {
       global $conn;
       echo json_encode(array('status' =>'File successfully deleted'));
       tempfiledbdelete($resfile);
 } else {
     echo json_encode(array('status' =>'File failed to be deleted'));
 }
} else {
   echo json_encode(array('Message' =>'There is no file found to be deleted')); 
}
  // unsetSession('resfile_del');
     exit();
}

// Get resource topics with respective subject via ajax
if(filter_has_var(INPUT_GET, 'topic_q')) {  // Get the topic-under-subject list by ajax
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


if(filter_has_var(INPUT_GET, 'tag_q')) {  // Get the list of tag by ajax
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

if(filter_has_var(INPUT_POST, 'remvdelekey') && filter_has_var(INPUT_POST, 'delkey')){  //Ajax to delete delkey session
 $delkey = filter_input(INPUT_POST, 'delkey', FILTER_SANITIZE_STRING);
 $page = filter_input(INPUT_POST, 'remvdelekey', FILTER_SANITIZE_STRING);
 $pagekey = $delkey."_".$page;
 $delkeys = ['res_icon_del','topic_del','resource_del','subject_del','resfile_del']; 
 if(in_array($delkey, $delkeys)){   // Check if the $delkey is part of the authorized keys.
 unsetSession($delkey);
 }
}

/* - - - - - - - - - - 
Set a key to prevent illegal opperation
- - - - - - - - - - -*/
function delkeyset($delkey){ // function to set session
$_SESSION[$delkey] = md5(rand(1000, 9999));
echo json_encode(array('deltok'=>$_SESSION[$delkey]));
exit;
  }
  
if(filter_has_var(INPUT_POST, 'setdelekey') && filter_has_var(INPUT_POST, 'delkey')){
  $delkey = filter_input(INPUT_POST, 'delkey', FILTER_SANITIZE_STRING);
  $page = filter_input(INPUT_POST, 'setdelekey', FILTER_SANITIZE_STRING);
  $pagekey = $delkey."_".$page;
if ($delkey === 'res_icon_del' && isset($_SESSION['resImageUploaded'])){
unsetSession($delkey);
delkeyset($delkey);
}

$delkeys = ['topic_del','resource_del','subject_del','resfile_del']; 
if(in_array($delkey, $delkeys)){
   unsetSession($delkey);
    delkeyset($delkey);
}
}

/* - - - - - - - - - - 
- Function to delete image in target directory to prevent keeping irrelevant via ajax
- - - - - - - - - - -*/
if(filter_has_var(INPUT_POST, 'iconfiletodel') && filter_has_var(INPUT_POST, 'delekey') && isset($_SESSION['res_icon_del']) && filter_input(INPUT_POST,'delekey') == $_SESSION['res_icon_del']) {  
$r_img = filter_input(INPUT_POST, 'iconfiletodel', FILTER_SANITIZE_STRING);
$target = IMAGE_DIR . $r_img;
if (file_exists($target)){ 
   if (unlink($target)) {
       tempfiledbdelete($r_img);
    echo json_encode(array('status' =>'File successfully deleted'));
 } else {
     echo json_encode(array('status' =>'File failed to be deleted'));
 }
} else {
   echo json_encode(array('Message' =>'File not found')); 
}
    unsetSession('res_icon_del');
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

/*
function gridDisplayTopics($topic){ //function to display topics as grid from topic table
    global $resources_count, $subject_id,$subject;
           echo "<div class='topicContainer'>
                  <div class='iconImage'>
                    <div class='screenTopic monitor'>
                      <div class='content'>
                       <span "; if($resources_count===0){ echo "style='color:#5c8ac6'";} echo ">" . ucwords($topic['title']). "</span>
                        </div>
                          <div class='base baseTopic'>
                           <div class='foot top'></div>
                            <div class='foot bottom'></div>
                               <div class='shadow'></div>
                              </div>
                             <div class='work-item-overlay'><div class='inner'><ul>
<li><a data-id='". $topic['id'] ."' data-title='".$topic['title']."' data-def='".$topic['def']."' data-subject='".$subject_id."' data-active='".$topic['active']."' class='topic-edit fa fa-pencil res-btn'>Edit</a></li>";
if($resources_count!==0){echo "<li><a href='" . BASE_URL . "/admin/resources.php?subject=". $subject_id. "&topic= " . $topic['id'] . "' class='topic-view fa fa-link res-btn' target='_blank'>View</a></li>";}
if($resources_count===0){echo "<li><a data-id='". $topic['id'] ."' data-title='".$topic['title']."' data-subject='".$subject."' class='topic-delete fa res-btn fa-times'>Delete</a></li>";}
echo "</ul></div><div class='res-count'>Resources: ".$resources_count."</div></div></div></div></div>";
}
*/

//Code to get topics list by subject
if(filter_has_var(INPUT_POST, 'subject_id') && filter_has_var(INPUT_POST, 'subject_name')) {
    $subject = esc(filter_input(INPUT_POST, 'subject_name', FILTER_SANITIZE_STRING));
    $displaymode = esc(filter_input(INPUT_POST, 'display_mode', FILTER_SANITIZE_STRING));
     $subject_id = filter_input(INPUT_POST, 'subject_id', FILTER_SANITIZE_NUMBER_INT);
    $ddtopics = getSubject_topics(esc($subject_id));
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
      foreach ($ddtopics as $topic) {
    getTopicResources($topic['id']); // if($resources_count!==0){
    $resources_count;
    echo "<div class='topicContainer'><div class='iconImage'><div class='screenTopic monitor'><div class='content'><span ";
    if($resources_count===0){ echo "style='color:#5c8ac6'";}
    echo ">" . ucwords($topic['title']). "</span></div><div class='base baseTopic'><div class='foot top'></div>";
   echo "<div class='foot bottom'></div><div class='shadow'></div></div><div class='work-item-overlay'><div class='inner'><ul><li><a data-id='";
   echo $topic['id'] ."' data-title='".$topic['title']."' data-def='".$topic['def']."' data-subject='".$subject_id."' data-active='".$topic['active']."' class='topic-edit fa fa-pencil res-btn'>Edit</a></li>";
    if($resources_count!==0){echo "<li><a href='" . BASE_URL . "/admin/resources.php?subject=". $subject_id. "&topic= " . $topic['id'] . "' class='topic-view fa fa-link res-btn' target='_blank'>View</a></li>";}
    if($resources_count===0){echo "<li><a data-id='". $topic['id'] ."' data-title='".$topic['title']."' data-subject='".$subject."' class='topic-delete fa res-btn fa-times'>Delete</a></li>";}
    echo "</ul></div><div class='res-count'>Resources: ".$resources_count."</div></div></div></div></div>";
    }  
    }
  //  }
}

//Code to get topics list by search
if(filter_has_var(INPUT_POST, 'topic_query_by_search')) {
$ddtopic = esc(filter_input(INPUT_POST, 'topic_query_by_search', FILTER_SANITIZE_STRING));
$ddtopics = getAllTopicsSubject($ddtopic);
    if (count($ddtopics) > 0) {
        foreach ($ddtopics as $ddtopic) {
     getTopicResources($ddtopic['topic_id']);
     $resources_count;
     //   $response[] = array("id" => $ddtopic['topic_id'], "title" => $ddtopic['topic_title'],"subject" => $ddtopic['subject']);
     echo "<div class='topicContainer'><div class='iconImage'><div class='screenTopic monitor'><div class='content'><span ";
    if($resources_count===0){ echo "style='color:#5c8ac6'";}
    echo ">" . ucwords($ddtopic['topic_title'])."<span style='font-size:14px'><br>Under ".ucwords($ddtopic['subject'])."</span></span></div><div class='base baseTopic'><div class='foot top'></div>";
   echo "<div class='foot bottom'></div><div class='shadow'></div></div><div class='work-item-overlay'><div class='inner'><ul><li><a data-id='";
   echo $ddtopic['topic_id'] ."' data-title='".$ddtopic['topic_title']."' data-def='".$ddtopic['topic_def']."' data-subject='".$ddtopic['subject_id']."' data-active='".$ddtopic['topic_active']."' class='topic-edit fa fa-pencil res-btn'>Edit</a></li>";
    if($resources_count!==0){echo "<li><a href='" . BASE_URL . "/admin/resources.php?subject=". $ddtopic['subject_id']. "&topic= " . $ddtopic['topic_id'] . "' class='topic-view fa fa-link res-btn' target='_blank'>View</a></li>";}
    if($resources_count===0){echo "<li><a data-id='". $ddtopic['topic_id'] ."' data-title='".$ddtopic['topic_title']."' data-subject='".$ddtopic['subject']."' class='topic-delete fa res-btn fa-times'>Delete</a></li>";}
    echo "</ul></div><div class='res-count'>Resources: ".$resources_count."</div></div></div></div></div>";   
     
        }
    } 
}
}

/* - - - - - - - - - - 
-  upload image via ajax
- - - - - - - - - - -*/
if (filter_has_var(INPUT_POST, 'imagefileupload')) {
    if (is_uploaded_file($_FILES['r_image']['tmp_name']) ) {
//$r_iconname = $_FILES['r_image']['name'];
$r_iconname = $_SERVER['HTTP_X_FILE_NAME'];
$sourcePath = $_FILES['r_image']['tmp_name'];
$ext = findexts ($r_iconname) ;
$filename = filter_var(generateRandomString().'.'. $ext, FILTER_SANITIZE_STRING);
$target = IMAGE_DIR . $filename;
if (file_exists($target)){ 
    $filename = filter_var(generateRandomString().'.'. $ext, FILTER_SANITIZE_STRING);
    $target = IMAGE_DIR.$filename;
     } 
if(move_uploaded_file($sourcePath,$target)) {
            $_SESSION['resImageUploaded'] = '1';
            echo json_encode(array('filename' => $filename));
            tempfiledbsave($filename, IMAGE_DIR);
            exit();
}else{
    echo json_encode(array('status' =>'failed'));
    exit();
}
}
}

