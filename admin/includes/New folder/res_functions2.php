<?php

// All variables
$resource_id = 0;
$isEditingResource = false;
$r_title = "";
$r_topic = "";
$topic_name = "";
$topic_def = "";
$r_iconname = "";
$r_iconsize = "";
$r_icontype = "";
$subject = "";
$subject_id = 0;
$info = "";
$multilink = 0;
$homepage_show = 0;
$subpage_show = 0;
$source = "";
$mediatypes = [];
$modalviews = [];
$restypes = [];
$rlevels = [];
$ddtopic = false;
$r_inserted = false;
$r_updated = false;
$clicks = 0;
$link_urls = [];
$link_names = [];
$errors = [];
$tags = $tag = "";
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$imageyesupload = false;

/* - - - - - - - - - - 
  - Actions on Resources
  - - - - - - - - - - - */
// if user clicks the create resource button
if (isset($_POST['create_resource'])) {
    createResource($_POST);
}
// if user clicks the Edit resource button
if (isset($_GET['resource'])) {
$res_id = $_GET['resource'];
if(is_numeric($res_id)) {
 $isEditingResource = true;  
 editResource($res_id);
}  
}
// if user clicks the update post button
if (isset($_POST['update_resource'])) {
    updateResource($_POST);
}
// if user clicks the Delete post button
if (isset($_GET['r-delete'])) {
    $resource_id = $_GET['r-delete'];
    deleteResource($resource_id);
}


// Image Upload function
function ImageIconUpload (){
    global $errors, $r_iconname, $target;
        if (is_uploaded_file($_FILES['r_icon']['tmp_name'])) {
// Image Upload 
            $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
            // Get image name
            $r_iconname = $_FILES['r_icon']['name']; 
            $r_iconsize = $_FILES['r_icon']['size'];
       //     $r_icontype = $_FILES['r_icon']['type'];
            if (empty($r_iconname)) {
                array_push($errors, "Icon image is required");
            }
            // Verify file extension
            $ext = pathinfo($r_iconname, PATHINFO_EXTENSION);
            if (!empty($r_iconname) && !array_key_exists($ext, $allowed)) {
                array_push($errors, "Please select a valid image format");
            }
            // Verify file size - 1MB maximum
            $maxsize = 1 * 1024 * 1024;
            if (!empty($r_iconname) && $r_iconsize > $maxsize) {
                array_push($errors, "File size is larger than the allowed limit.");
            }
            // image file directory
            $target = IMAGE_DIR . basename($r_iconname);
    }
            if (file_exists($target)) {
                $r_iconname = $r_iconname;
            } elseif (!file_exists($target)) {
                move_uploaded_file($_FILES['r_icon']['tmp_name'], $target);
                echo "Your Resource image was uploaded successfully.";
                echo json_encode(array('status' => "success"));
            } else {
                array_push($errors, "Upload of Resource image failed.");
                echo json_encode(array('status' => "failed"));
            }
        }
       
function ResTagsSave ($inserted_resource_id, $tags){  // create relationship between resource and tags
    global $conn, $errors;
                for ($i = 0; $i < count($tags); $i++) {
                $tag = esc(filter_var($tags[$i], FILTER_SANITIZE_STRING));
                //	 echo "Topic table: " . $inserted_resource_id  . ">>" . $topic_id . ">>" . count($topics_id) . ">>" . $i . "<br>" ;
                $sql = "INSERT INTO resource_tags (resource_id, tag_id) VALUES($inserted_resource_id, $tag)";
               if(!mysqli_query($conn, $sql)){
                   array_push($errors, "The Resource Tag(s) failed to save");
               }
               }
                //	if(mysqli_query($conn, $t_sql)){ $_SESSION['message'] = "Topic saved successfully";} else {array_push($errors, "Topic(s) failed to save.");}
            }

function ResTopicsSave($inserted_resource_id, $topics_id){
        global $conn, $errors;
                for ($i = 0; $i < count($topics_id); $i++) {
                $topic_id = esc(filter_var($topics_id[$i], FILTER_SANITIZE_NUMBER_INT));
                //	 echo "Topic table: " . $inserted_resource_id  . ">>" . $topic_id . ">>" . count($topics_id) . ">>" . $i . "<br>" ;
                $sql = "INSERT INTO resource_topic (resource_id, topic_id) VALUES($inserted_resource_id, $topic_id)";
                if(!mysqli_query($conn, $sql)){
                   array_push($errors, "The Resource Topic(s) failed to save");
               }
                //	if(mysqli_query($conn, $t_sql)){ $_SESSION['message'] = "Topic saved successfully";} else {array_push($errors, "Topic(s) failed to save.");}
            }
}

function ResLinkUrl($inserted_resource_id,$link_urls){
    global $conn, $errors, $request_values,$mediatype,$link_names;
                for ($j = 0; $j < count($link_urls); $j++) {
                $link_url = esc(filter_var($link_urls[$j], FILTER_SANITIZE_URL));
                $link_name = esc(filter_var($link_names[$j], FILTER_SANITIZE_STRING)) ;
                if (!empty($link_name)){
                    $link_name=$link_name;
                }else{
                  $link_name = 'Link';
                }
                if (!isset($request_values["link_modalview"][$j])) {
                    $modalview = 0;
                } else {
                    $modalview = 1;
                }
                LinkUrlExt($link_url); // Get the $mediatype of the url link
                // echo "Links: " . $inserted_resource_id . ">>" . $link_url . ">>" .  $link_name . ">>" .  $mediatype . ">>" .  $modalview . ">>" . count($link_urls) . ">>" . $j . "<br>";
                $sql = "INSERT INTO links (resources_id, url, name, mediatype_id, modalview, created_at, updated_at) VALUES($inserted_resource_id, '$link_url', '$link_name', $mediatype, $modalview, now(), now())";
                 if(!mysqli_query($conn, $sql)){
                   array_push($errors, "The Resource Link(s) failed to save");
               }
                // if(mysqli_query($conn, $l_sql)){$_SESSION['message'] = "Link saved successfully";}else {array_push($errors, "Link(s) failed to save.");}
            }
}


function ResTypesSave ($inserted_resource_id, $restypes){
    global $conn, $errors;
                for ($k = 0; $k < count($restypes); $k++) {
                $restype = esc(filter_var($restypes[$k], FILTER_SANITIZE_NUMBER_INT));
                $sql = "INSERT INTO resource_type (resource_id, type_id) VALUES($inserted_resource_id, $restype)";
                if(!mysqli_query($conn, $sql)){
                   array_push($errors, "The Resource Types(s) failed to save");
               }
                // if (mysqli_query($conn, $rtype_sql)) { $_SESSION['message'] = "Resource types saved successfully";} else { array_push($errors, "Resource type(s) failed to save.");}
            }
}

function ResLevelsSave ($inserted_resource_id, $rlevels){
    global $conn, $errors;
                for ($k = 0; $k < count($rlevels); $k++) {
                $rlevel = esc(filter_var($rlevels[$k], FILTER_SANITIZE_NUMBER_INT));
                $sql = "INSERT INTO resource_level (resource_id, schl_id) VALUES($inserted_resource_id, $rlevel)";
                if(!mysqli_query($conn, $sql)){
                   array_push($errors, "The Resource Level(s) failed to save");
               }
                // if(mysqli_query($conn, $rlevel_sql)){$_SESSION['message'] = "Resource types saved successfully";} else {array_push($errors, "Resource type(s) failed to save.");}
            }
}
 function ResTagUpdate($res_id, $tags){
             global $conn;
            for ($i = 0; $i < count($tags); $i++) {
                $tag = esc(filter_var($tags[$i], FILTER_SANITIZE_STRING));
                $sql = "INSERT IGNORE INTO resource_tags (resource_id, tag_id) VALUES($res_id, $tag)";
                mysqli_query($conn, $sql);
            } 
            // Delete any tag that is not associated with the resource
              $restags = getResourcesTags($res_id); // Get the all the tags from database for the selected resource   
        //   ResAttrDel("resource_tags","tag_id",$res_id,$restags,$tags);
              foreach ($restags as $restag) {
                if (!multi_in_array($restag['id'], $tags)){
                    $tag = $restag['id'];
                    $del="DELETE FROM resource_tags WHERE resource_id=$res_id AND tag_id = $tag " ;
                     mysqli_query($conn, $del);
                }
            } 
             }
 // Function to to update the Resource Topics
             
 function ResTopicUpdate($res_id, $topics_id) {
     global $conn;
            for ($i = 0; $i < count($topics_id); $i++) {
                $topic_id = esc(filter_var($topics_id[$i], FILTER_SANITIZE_NUMBER_INT));
                $sql = "INSERT IGNORE INTO resource_topic (resource_id, topic_id) VALUES($res_id, $topic_id)";
                mysqli_query($conn, $sql);
                //	if(mysqli_query($conn, $t_sql)){ $_SESSION['message'] = "Topic saved successfully";} else {array_push($errors, "Topic(s) failed to save.");}
            }
            
            $topics = getResourcesTopicsSub($res_id); // Get the all the topics from database for the selected resource            
            // Delete any topic that is not associated with the resource
         //   ResAttrDel("resource_topic","topic_id",$topics,$topics_id,$res_id);
            foreach ($topics as $topic) {
                if (!multi_in_array($topic['id'], $topics_id)){
                    $topic = $topic['id'];
                    $del="DELETE FROM resource_topic WHERE resource_id=$res_id AND topic_id = $topic " ;
                     mysqli_query($conn, $del);
                }
            } 
        }  
  
 // Function to to update the Resource Links        
    function ResLinksUpdate ($res_id,$link_urls) {
        global $conn, $link_names,$request_values,$mediatype;
                $rlinks = getResourceLink($res_id);  // Get the resource existing links in the database
                for ($j = 0; $j < count($link_urls); $j++) { 
                $link_url = esc(filter_var($link_urls[$j], FILTER_SANITIZE_URL));  // Sanitize the url input
                $link_name = esc(filter_var($link_names[$j], FILTER_SANITIZE_STRING));
                 if (!empty($link_name)){$link_name=$link_name;}else{$link_name = 'Link';} //If the link name is not empty, use the name entered, else if not link name is not entered, use link.
                LinkUrlExt($link_url); // Get the $mediatype of the url link
                if (!isset($request_values["link_modalview"][$j])) { $modalview = 0;} else { $modalview = 1; } // Check if modalview is checked
                if (isset($request_values["link_ids"][$j]) && multi_in_array($request_values["link_ids"][$j], array_column($rlinks, 'id'))) { // Can you find the id of the link among the database link records, if yes, then update, if no, go ahead and create it?
                   $link_id = esc(filter_var($request_values["link_ids"][$j], FILTER_SANITIZE_NUMBER_INT));
                    $sql = "UPDATE links SET url='$link_url', name='$link_name', mediatype_id=$mediatype, modalview=$modalview, updated_at= now() WHERE id = $link_id";
                } else{$sql = "INSERT INTO links (resources_id, url, name, mediatype_id, modalview, created_at, updated_at) VALUES($res_id, '$link_url', '$link_name', $mediatype, $modalview, now(), now())";}
             mysqli_query($conn, $sql);
            }
            // Delete any topic that is not associated with the resource
         //   ResAttrDel("links","id",$rlinks,$link_ids);
           foreach ($rlinks as $rlink) {
                if (!multi_in_array($rlink['id'], $request_values["link_ids"])){
                    $del_link="DELETE FROM links WHERE id =".$rlink['id'] ;
                     mysqli_query($conn, $del_link);
                }
            }  
           }
           
  // Function to to update the Resource types
       function  ResTypesUpdate ($res_id, $restypes){
           global $conn,$res_id,$restypes;
            $restypesDb = getAllResourcesType(); // Get all resource types from the database
            foreach ($restypesDb as $type) { // For each resource types from db
                if (!multi_in_array($type, $restypes)){ // If any of the types from db is not found among the new types inputs, delete them
                    $sql="DELETE FROM resource_type WHERE resource_id=$res_id AND type_id=$type" ;  // if not then delete it from already resource associated types
                }else{
                   $sql = "INSERT IGNORE INTO resource_type (resource_id, type_id) VALUES($res_id, $type)"; 
                }
                mysqli_query($conn, $sql);
            }            
            }
            
  // Function to to update the Resource levels
        function ResLevelsUpdate ($res_id, $rlevels) {
            global $conn,$res_id,$rlevels;
           $rlevels_all = getSchlLevel(); // Get all resource levels
            foreach ($rlevels_all as $level) { // For each resource levels
                if (!multi_in_array($level, $rlevels)){ // Check the user input id is not one of the resource types
                    $sql="DELETE FROM resource_level WHERE resource_id=$res_id AND schl_id=$level" ;  // if not then delete it from already resource associated types
                }else{
                   $sql = "INSERT IGNORE INTO resource_level (resource_id, schl_id) VALUES($res_id, $level)"; 
                }
                mysqli_query($conn, $sql);
            }
            }
  // Function to get the Resource mediatype from the extension of the url        
function LinkUrlExt($link_url){
    global $mediatype;
                $ext = pathinfo(parse_url($link_url, PHP_URL_PATH),PATHINFO_EXTENSION); // Get the extension of the reource url link.
                if ($ext == 'swf') {
                    $mediatype = getMediatypeId('Flash');
                } elseif ($ext == 'mp4') {
                    $mediatype = getMediatypeId('Video');
                } elseif ($ext == 'dir') {
                    $mediatype = getMediatypeId('Shockwave');
                } elseif ($ext == 'jar' || $ext == 'jnlp') {
                    $mediatype = getMediatypeId('Java');
                } else {
                    $mediatype = getMediatypeId('Html5');
                }
                return $mediatype;
            }
 /*
  function ResAttrDel($table,$attrfield,$attrs_db,$attrs_form,$res_id=0){
    global $conn;
                foreach ($attrs_db as $attr) {
                if (!multi_in_array($attr['id'], $attrs_form)){
                    $attr = $attr['id'];
                    $del="DELETE FROM" .$table. " WHERE ";
                    $del.= "`".$attrfield."` = " .$attr;
                   if($res_id!==0){
                    $del.="AND resource_id=".$res_id;
                    }
                     mysqli_query($conn, $del);
                }
    }
}      
    */   
function dbRowInsert($table, $data){
    global $conn;
   if (sizeof($data) == 0) {
        return false;
    }
    // retrieve the keys of the array (column titles)
    $fields = array_keys($data);
    // build the query
    $sql = "INSERT IGNORE INTO ".$table."
    (`".implode('`,`', $fields)."`)
    VALUES('".implode("','", $data)."')";
    // run and return the query result resource
    return mysql_query($conn, $sql);
}



function dbRowDelete($table, $where=''){
     global $conn;
    // check for optional where clause
    $whereSQL = '';
    if(!empty($where))
    {
        // check to see if the 'where' keyword exists
        if(substr(strtoupper(trim($where)), 0, 5) != 'WHERE')
        {
            // not found, add keyword
            $whereSQL = " WHERE ".$where;
        } else
        {
            $whereSQL = " ".trim($where);
        }
    }
    // build the query
    $sql = "DELETE FROM ".$table.$whereSQL;
    // run and return the query result resource
    return mysql_query($conn, $sql);
}

function dbRowUpdate($table, $data, $where=''){
    global $conn;
    // check for optional where clause
    $whereSQL = '';
    if(!empty($where)){
        if(substr(strtoupper(trim($where)), 0, 5) != 'WHERE') { // check to see if the 'where' keyword exists
            // not found, add key word
            $whereSQL = " WHERE ".$where;
        } else {
            $whereSQL = " ".trim($where);
        }
    }
    // start the actual SQL statement
    $sql = "UPDATE ".$table." SET ";
    // loop and build the column /
    $sets = array();
    foreach($data as $column => $value) {
         $sets[] = "`".$column."` = '".$value."'";
    }
    $sql .= implode(', ', $sets);
    // append the where statement
    $sql .= $whereSQL;
    // run and return the query result
    return mysql_query($conn, $sql);
}


function inputsError($input,$msg){
    global $errors;
    if (empty($input)) {
        array_push($errors, $msg);
    }
}
    // Iteration of Multiple inputs
    function multinput ($input) {
        $i = 0; 
        $i < count($input); 
        $i++;
    }


/* - - - - - - - - - - 
  -  Resource functions
  - - - - - - - - - - - */

function createResource($request_values) {
   global $conn, $errors, $r_title, $r_iconname, $inserted_resource_id, $topics_id, $r_info, $source, $restypes, $link_names, $link_urls, $subpage_show, $rlevels, $tags;
    //Start of Non-Ajax File upload
    if (isset($request_values['r_icon'])) {
        ImageIconUpload ();
        }
    //End of Non-Ajax File upload
    $r_iconname = esc($request_values['r_img']);
    $r_title = esc($request_values['r_title']);
    $r_info = htmlentities(esc($request_values['r_info']));
    $source = htmlentities(esc($request_values['source']));
    $link_names = $request_values["link_name"];
    $link_urls = $request_values["link_url"];
    if (isset($request_values["restypes"])) {
        $restypes = $request_values["restypes"];
    }
    if (isset($request_values["rlevels"])) {
        $rlevels = $request_values["rlevels"];
    }
    if (isset($request_values['topics_id'])) {
        $topics_id = explode(',', $request_values['topics_id']);
    }
        if (isset($request_values['r_tags'])) {
        $tags = explode(',', $request_values['r_tags']);
    }

    if (isset($request_values['subjectshow'])) {
        $subpage_show = esc($request_values['subjectshow']);
    }
    if (count($request_values["link_url"]) > 1) {
        $multilink = 1;
    } else {
        $multilink = 0;
    }
    // validate form
    inputsError($r_iconname,"Resource icon image is required");
    inputsError($r_title,"Resource title is required.");
    inputsError($topics_id,"Atleast a topic is required for your resource.");
    inputsError($link_urls,"Resource link URL is required and must be valid URL");
    inputsError($rlevels,"Resource School level is required, select atleast one level");
    inputsError($restypes,"Atleast a resource type is required");
    
    if (count($errors) != 0 && !empty($r_iconname)) {
     $target = IMAGE_DIR . $r_iconname;
     if (file_exists($target)) {
        unlink($target);
    }
    }

    // create resource if there are no errors in the form
     if (count($errors) == 0) {
         // Resource data saving function
         $r_title = filter_var($r_title, FILTER_SANITIZE_STRING);
         $r_iconname = filter_var($r_iconname, FILTER_SANITIZE_STRING);
         $r_info = filter_var ($r_info, FILTER_SANITIZE_STRING);
         $source = filter_var($source, FILTER_SANITIZE_STRING);
      //   $subpage_show = filter_var($subpage_show, FILTER_SANITIZE_BOOLEAN); 
        $sql = "INSERT INTO resources (title, icon, info, source, multilink, subpage_show, created_at, updated_at) VALUES('$r_title', '$r_iconname', '$r_info', '$source', $multilink, $subpage_show, now(), now())";
        if (mysqli_query($conn, $sql)) { // if resource created successfully
            $inserted_resource_id = mysqli_insert_id($conn);
            ResTagsSave ($inserted_resource_id, $tags);  // create relationship between resource and tags         
            ResTopicsSave($inserted_resource_id, $topics_id);   // create relationship between resource and topic         
            ResLinkUrl($inserted_resource_id,$link_urls);    // create relationship between resource and link       
            ResTypesSave ($inserted_resource_id, $restypes);    // create relationship between resource and resource type
            ResLevelsSave ($inserted_resource_id, $rlevels);  // create relationship between resource and resource levels
            $_SESSION['res_saved'] = "Resource saved successfully";
            echo
            header('location: add_resource.php');
            exit(0);
        } else {
            array_push($errors, "Resource failed to save.");
            exit();
        }
    }
}

/* * * * * * * * * * * * * * * * * * * * *
 * - Takes resource id as parameter
 * - Fetches the resource from database
 * - sets resource fields on form for editing
 * * * * * * * * * * * * * * * * * * * * * */

function editResource($res_id) {
    global $conn, $r_title, $r_icon, $r_info, $source, $rtypes, $rlinks, $subpage_show, $reslevels, $restags,$r_topics;
    $sql = "SELECT title,icon,info,source,subpage_show FROM resources r WHERE id =$res_id LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $resource = mysqli_fetch_assoc($result);
   // $final_resources = [];
    // set form values on the form to be updated
    $r_title = $resource['title'];
    $r_icon = $resource['icon'];
    $r_info = $resource['info'];
    $source = $resource['source'];
    $subpage_show = $resource['subpage_show'];
    $rlinks = getResourceLink($res_id);
    $rtypes = getResourcesType($res_id);
    $restags = getResourcesTags($res_id);
    $reslevels = getResourcesLevels($res_id);
    $r_topics = getResourcesTopicsSub($res_id);
}

// Disable Resource
function resDisable ($request_values){  
    global $conn, $res_id;
       $active = $request_values['active'];
       $res_id = filter_var($request_values['active_res'], FILTER_SANITIZE_NUMBER_INT);
    if (filter_var($active,FILTER_VALIDATE_BOOLEAN)) {
        $sql = "UPDATE resources SET active=1 WHERE id = $res_id";
        $msgout = json_encode(array('msg' => 'Enabled', 'active' => 1));
    }else{
        $sql = "UPDATE resources SET active=0 WHERE id = $res_id";
        $msgout = json_encode(array('msg' => 'Disabled', 'active' => 0));
    }
        if (mysqli_query($conn, $sql)) {
            header('Content-Type: application/json');
            echo $msgout;
            exit();
        }
    }


 /* * * * * * * * * * * * * * * * * * * * *
 * Update Resource
 * * * * * * * * * * * * * * * * * * * * * */       
        
function updateResource($request_values) {
    global $conn, $errors, $r_title, $r_iconname, $res_id, $topics_id, $r_info, $source, $link_ids, $link_names, $link_urls, $subpage_show, $rlevels, $tags;
    //Start of Non-Ajax File upload
    if (isset($request_values['r_icon'])) {ImageIconUpload (); }
    //End of Non-Ajax File upload

    $res_id = esc(filter_var($request_values['resource_id'], FILTER_SANITIZE_NUMBER_INT));
    $r_iconname = esc(filter_var($request_values['r_img'], FILTER_SANITIZE_STRING));
    $r_title = esc(filter_var($request_values['r_title'], FILTER_SANITIZE_STRING));
    $r_info = esc(filter_var($request_values['r_info'], FILTER_SANITIZE_STRING));
  //  $r_info = htmlentities(filter_var(esc($request_values['r_info']), FILTER_SANITIZE_STRING));
    $source = esc(filter_var($request_values['source'], FILTER_SANITIZE_STRING));
    $link_names = $request_values["link_name"];
     $link_urls = $request_values["link_url"];
    if (isset($request_values["link_ids"])) { $link_ids = $request_values["link_ids"]; }
    if (isset($request_values["restypes"])) {$restypes = $request_values["restypes"]; }
    if (isset($request_values["rlevels"])) { $rlevels = $request_values["rlevels"]; }
    if (isset($request_values['topics_id'])) {$topics_id = explode(',', $request_values['topics_id']);}
    if (isset($request_values['r_tags'])) {$tags = explode(',', $request_values['r_tags']);}
    if (!isset($request_values['subjectshow'])) { $subpage_show = 0;}else{$subpage_show = 1;}
    if (count($request_values["link_url"]) > 1) { $multilink = 1; } else {$multilink = 0;}
    // validate form
    inputsError($r_iconname,"Resource icon image is required");
    inputsError($r_title,"Resource title is required.");
    inputsError($topics_id,"Atleast a topic is required for your resource.");
    inputsError($link_urls,"Resource link URL is required and must be valid URL");
    inputsError($rlevels,"Resource School level is required, select atleast one level");
    inputsError($restypes,"Atleast a resource type is required");


    // Update resource if there are no errors in the form
     if (count($errors) == 0) {
      //   $subpage_show = filter_var($subpage_show, FILTER_SANITIZE_BOOLEAN); 
   $r_sql = "UPDATE resources SET title='$r_title', icon='$r_iconname', info='$r_info', source='$source', multilink=$multilink, subpage_show=$subpage_show, updated_at=now() WHERE id=$res_id";
        if (mysqli_query($conn, $r_sql)) { // if resource updated successfully
         //   $inserted_resource_id = mysqli_insert_id($conn);
            $r_updated = true;
        } else {
            array_push($errors, "Resource failed to save.");
            exit();
        }
            
        if ($r_updated) { // if the resource is successfully updated
           
            
            ResTagUpdate($res_id, $tags); // update relationship between resource and tags
            ResTopicUpdate($res_id, $topics_id);   // update relationship between resource and topic
        
            // update relationship between resource and link
            ResLinksUpdate ($res_id,$link_urls);
           
            // Update relationship between resource and resource type
            ResTypesUpdate ($res_id, $restypes);

            // Update relationship between resource and school level
           ResLevelsUpdate ($res_id, $rlevels);
           
          //  $_SESSION['message'] = "Resource updated successfully";
            $_SESSION['res_updated'] = "yes_updated";
            echo
            header('location: edit.php?resource='.$res_id);
            exit(0);
        }
    }
}

// delete blog post
function deleteResource($request_values) {
    global $conn;
 //   $delete = filter_var($request_values['delete'], FILTER_SANITIZE_NUMBER_INT);
    $res_id = filter_var($request_values['res_id'], FILTER_SANITIZE_NUMBER_INT);
//    if($delete===1){
    $sql = "DELETE FROM resources WHERE id=$res_id;";
    $sql .= "DELETE FROM resource_tags WHERE resource_id=$res_id;";
    $sql .= "DELETE FROM links WHERE resources_id=$res_id;";
    $sql .= "DELETE FROM resource_type WHERE resource_id=$res_id;";
    $sql .= "DELETE FROM resource_level WHERE resource_id=$res_id";
//    }
    if (mysqli_multi_query($conn, $sql)) {
            echo json_encode(array('msg' => 'deleted', 'delete' => 1));
            exit();
    }
}

// if user clicks the publish post button
if (isset($_GET['publish']) || isset($_GET['unpublish'])) {
    $message = "";
    if (isset($_GET['publish'])) {
        $message = "Post published successfully";
        $post_id = $_GET['publish'];
    } else if (isset($_GET['unpublish'])) {
        $message = "Post successfully unpublished";
        $post_id = $_GET['unpublish'];
    }
    togglePublishPost($post_id, $message);
}

// delete blog post
function togglePublishPost($post_id, $message) {
    global $conn;
    $sql = "UPDATE posts SET published=!published WHERE id=$post_id";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = $message;
        header("location: posts.php");
        exit(0);
    }
}




/* * * * * * * * * * * *
 *  Returns all subjects
 * * * * * * * * * * * * */

function getAllSubject() {
    global $conn;
    $sql = "SELECT * FROM subject";
    $result = mysqli_query($conn, $sql);
    $subjects = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $subjects;
}

/* * * * * * * * * * * * * * * *
* Returns subject name by id
* * * * * * * * * * * * * * * * */
function getSubjectNameById($id)
{
    $sub_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
    global $conn;
    $sql = "SELECT name FROM subject WHERE id=$sub_id LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $subject = mysqli_fetch_assoc($result);
    return $subject['name'];
}


/* * * * * * * * * * * *
 * Ajax call:  Returns all tags
 * * * * * * * * * * * * */

function getAllTags($tag) {
    global $conn;
    $sql = "SELECT tags.id AS tag_id,tags.tag AS tag FROM tags WHERE tag like '%" . $tag . "%' ORDER BY tag ASC";
    $result = mysqli_query($conn, $sql);
    $taglist = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $taglist;
}

/* * * * * * * * * * * * * * * *
 * Returns topic name by topic id
 * * * * * * * * * * * * * * * * */

function getTopicNameById($id) {
    global $conn;
$t_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
    $sql = "SELECT title FROM topics WHERE id=$t_id LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $topic = mysqli_fetch_assoc($result);
    return $topic['title'];
}

/* * * * * * * * * * * *
 *  Resources Click Counts
 * * * * * * * * * * * * */

function resLinkClick($request_values) {
    global $conn, $resource_id, $clicks;
    $resource_id = esc($request_values['res_id']);
    if (is_numeric($resource_id)) {
        $query = "SELECT resources.clicks FROM resources WHERE id=$resource_id";
        $result = mysqli_query($conn, $query);
        $clicks = mysqli_fetch_assoc($result);
        $nclicks = $clicks['clicks'] + 1;
        $sql = "UPDATE resources SET clicks=$nclicks WHERE id = $resource_id";
        if (mysqli_query($conn, $sql)) {
            header('Content-Type: application/json');
            echo json_encode(array('msg' => 'success', 'clicks' => $nclicks));
            exit();
        }
    }
}


/* * * * * * * * * * * *
 *  Create Tag List
 * * * * * * * * * * * * */
function createAjaxTag($request_values) {
    global $conn, $tag, $tagdef;
    $tag = filter_var(esc($request_values['ajaxtagsave']), FILTER_SANITIZE_STRING);
    $tagdef = filter_var(esc($request_values['tagdef']), FILTER_SANITIZE_STRING);
    if (strlen($tag)>1){
       // Ensure that no tag is saved twice under a subject. 
    $tag_check = "SELECT * FROM tags WHERE tag='$tag' LIMIT 1";
    $result = mysqli_query($conn, $tag_check); 
    if (mysqli_num_rows($result) == 0) {
       $query = "INSERT INTO tags (tag, def) VALUES('$tag','$tagdef')";
        $result = mysqli_query($conn, $query);
        if ($result){
            $inserted_tag = mysqli_insert_id($conn);
            header('Content-Type: application/json');
            echo json_encode(array('msg' => 'The tag has been successfully saved.', 'tag_id' => $inserted_tag));
            exit();
        } else {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(array('msg' => 'The tag failed to save, try again.'));
        exit();
    }
    } else {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(array('msg' => 'The tag is already existing in the system tags list, try another word.'));
        exit();
    }
    }
    }
    
    
/* - - - - - - - - - - 
-  Function to load list of all subject via ajax
- - - - - - - - - - -*/
function linkCheck($request_values) {
    global $conn;
    $link = esc($request_values['linkcheck']);
    if (filter_var($link, FILTER_VALIDATE_URL)) {
        $query="SELECT resources_id, url FROM `links` WHERE url='".$link."'";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) > 0) {
            $link = mysqli_fetch_assoc($result);
  	    header('Content-Type: application/json');
            echo json_encode(array('msg' => 'yes', 'res_id' => $link['resources_id']));
            exit();
  	}
    }else{ exit();}
}
/* * * * * * * * * * * *
 *  Create Topic in respect to subject
 * * * * * * * * * * * * */

function createAjaxTopic($request_values) {
    global $conn, $errors, $topic_name, $topic_def, $subject_id;
    $topic_name = filter_var(esc($request_values['topic_name']), FILTER_SANITIZE_STRING);
    $topic_def = filter_var(esc($request_values['topic_def']), FILTER_SANITIZE_STRING);
    $subject_id = filter_var(esc($request_values['subject_id']), FILTER_SANITIZE_NUMBER_INT);
    // validate form
    if (empty($topic_name)) {
        array_push($errors, "Topic name required");
    }
    if ($subject_id == 0) {
        array_push($errors, "Ensure a Subject is selected");
    }
    // Ensure that no topic is saved twice under a subject. 
    $topic_check_query = "SELECT * FROM topics WHERE title='$topic_name' AND subject_id=$subject_id LIMIT 1";
    $result = mysqli_query($conn, $topic_check_query);

    if (mysqli_num_rows($result) > 0) { // if topic exists
        array_push($errors, "A post already exists with that title.");
    }
    if (count($errors) == 0) {
        $query = "INSERT INTO topics (title, def, subject_id) 
				  VALUES('$topic_name', '$topic_def', '$subject_id')";
        $result = mysqli_query($conn, $query);
        if ($result) {
            $inserted_topic_id = mysqli_insert_id($conn);
            header('Content-Type: application/json');
            echo json_encode(array('msg' => 'success', 'topic_id' => $inserted_topic_id));
            exit();
        }
    } else {
        header('HTTP/1.1 500 Internal Server Booboo');
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(array('msg' => 'failure'));
        exit();
    }
}


/* * * * * * * * * * * *
 *  Ajax call: Returns all topics with corresponding subject
 * * * * * * * * * * * * */

function getAllTopicsSubject($topic) {
    global $conn;
    $sql = "SELECT t.id AS topic_id,t.title AS topic_title,t.def AS topic_def,s.id AS subject_id, s.name AS subject FROM topics t JOIN subject s ON t.subject_id=s.id Where t.title like '%" . $topic . "%' ORDER BY t.title ASC";
    $result = mysqli_query($conn, $sql);
    $topicsub = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $topicsub;
}

/* * * * * * * * * * * * * * *
 * Returns Subject Topics
 * * * * * * * * * * * * * * */

function getSubject_topics($subject) {
    global $conn, $topics_count;
    // Get single subject topics
    $sql = "SELECT * FROM topics WHERE subject_id = (SELECT id FROM subject WHERE id = '$subject')";
    $result = mysqli_query($conn, $sql);
    $topics_count = mysqli_num_rows($result);
    $topics = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $topics;
}

/* * * * * * * * * * * * * * *
* Returns All Resources by Subjects
* * * * * * * * * * * * * * */
function getAllSubResources($subject){
    global $conn;
    global $resources_count;
    global $res_row;
    // Get single subject topics
 //   $topic = getSubject_topics($subject);
    $sql = "SELECT r.id,r.title,r.icon,r.info,r.multilink,r.subpage_show,r.active FROM resources r LEFT JOIN resource_topic rt ON rt.resource_id = r.id LEFT JOIN topics t ON t.id = rt.topic_id WHERE subject_id=$subject";
    $result = mysqli_query($conn, $sql);
    $resources = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $res_row= mysqli_fetch_assoc($result);
    $resources_count = mysqli_num_rows($result);
//	return $resources;
    $final_resources = array();
    foreach ($resources as $resource) {
        $resource['link'] = getResourceLink($resource['id']);
        array_push($final_resources, $resource);
    }
    return $final_resources;
}


/* * * * * * * * * * * * * * *
* Returns Resources by Topics
* * * * * * * * * * * * * * */
function getTopicResources($topic){
	global $conn;
	global $resources_count;
	global $res_row;
	// Get single subject topics
	$sql = "SELECT r.*, t.title AS topic_title  FROM resources r LEFT JOIN resource_topic rt ON rt.resource_id = r.id LEFT JOIN topics t ON t.id = rt.topic_id WHERE t.id =$topic ORDER BY r.clicks DESC";
	$result = mysqli_query($conn, $sql);
	$resources_count = mysqli_num_rows($result);
	$resources = mysqli_fetch_all($result, MYSQLI_ASSOC);
	$res_row= mysqli_fetch_assoc($result);
//	return $resources;
    $final_resources = array();
    foreach ($resources as $resource) {
        $resource['link'] = getResourceLink($resource['id']);
        $resource ['resourcetype'] = getResourcesType($resource['id']);
        array_push($final_resources, $resource);
    }
    return $final_resources;
}

/* * * * * * * * * * * * * * *
 * Returns Resources by Topics
 * * * * * * * * * * * * * * */

function getAllResources() {
    global $conn, $resources_count, $res_row;
    // Get single subject topics
    $sql = "SELECT resources.* FROM resources";
    $result = mysqli_query($conn, $sql);
    $resources_count = mysqli_num_rows($result);
    $resources = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $res_row = mysqli_fetch_assoc($result);
//	return $resources;
    $final_resources = array();
    foreach ($resources as $resource) {
        $resource['link'] = getResourceLink($resource['id']);
        $resource ['resourcetype'] = getResourcesType($resource['id']);
        array_push($final_resources, $resource);
    }
    return $final_resources;
}

/* * * * * * * * * * * * * * *
 * Resources links
 * * * * * * * * * * * * * * */

function getResourceLink($res_id) {
    global $conn;
    $sql = "SELECT id, url, name, modalview FROM links WHERE resources_id = $res_id";
    $result = mysqli_query($conn, $sql);
    $links = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $final_links = array();
    foreach ($links as $link) {
        $link['mediatype'] = getLinkMediatype($link['id']);
        array_push($final_links, $link);
    }
    return $final_links;
}

//Get Id of the Mediatype
function getMediatypeId($extn) {
    global $conn;
    $sql = "SELECT id FROM mediatype WHERE name = '$extn'";
    $result = mysqli_query($conn, $sql);
    $mediatype = mysqli_fetch_assoc($result);
    return $mediatype['id'];
}

//Mediatype
function getLinkMediatype($link) {
    global $conn;
    $sql = "SELECT name FROM mediatype WHERE id = (SELECT mediatype_id FROM links where id =$link)";
    $result = mysqli_query($conn, $sql);
    $links = mysqli_fetch_assoc($result);
    return $links;
}

//Get Schools levels
function getSchlLevel() {
    global $conn;
    $sql = "SELECT schl.id, schl.level FROM school schl";
    $result = mysqli_query($conn, $sql);
    $schllevels = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $schllevels;
}


/* * * * * * * * * * * * * * *
* Returns Resources Types
* * * * * * * * * * * * * * */
function getResourcesType($res){
    global $conn;
    $sql = "SELECT rt.id FROM resourcetype rt INNER JOIN resource_type rjt ON rjt.type_id=rt.id INNER JOIN resources r ON rjt.resource_id=r.id WHERE r.id=$res";
    $result = mysqli_query($conn, $sql);
    $types = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $types;
}

/* * * * * * * * * * * * * * *
* Returns Resources Tags
* * * * * * * * * * * * * * */
function getResourcesTags($res){
    global $conn;
    $sql = "SELECT tg.id AS id, tg.tag AS tag FROM tags tg INNER JOIN resource_tags rtg ON rtg.tag_id=tg.id INNER JOIN resources r ON rtg.resource_id=r.id WHERE r.id=$res";
    $result = mysqli_query($conn, $sql);
    $tags = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $tags;
}


/* * * * * * * * * * * * * * *
* Returns Resources Levels
* * * * * * * * * * * * * * */
function getResourcesLevels($res){
    global $conn;
    $sql = "SELECT schl.* FROM school schl INNER JOIN resource_level rl ON rl.schl_id=schl.id INNER JOIN resources r ON rl.resource_id=r.id WHERE r.id=$res";
    $result = mysqli_query($conn, $sql);
    $levels = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $levels;
}



//Get all resources types
function getAllResourcesType() {
    global $conn;
    $sql = "SELECT rt.id, rt.name, rt.icon FROM resourcetype rt";
    $result = mysqli_query($conn, $sql);
    $restype = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $restype;
}

//Resources Topic and subject
function getResourcesTopicsSub($res) {
    global $conn;
    $sql = "SELECT t.id, t.title AS title, s.name AS subject FROM topics t JOIN subject s ON t.subject_id=s.id INNER JOIN resource_topic rt ON rt.topic_id=t.id INNER JOIN resources r ON rt.resource_id=r.id WHERE r.id=$res";
    $result = mysqli_query($conn, $sql);
    $topics = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $topics;
}

// Check if a value exists in a multidimensional array

function multi_in_array($value, $array) {
    foreach ($array AS $item){
        if (!is_array($item)){
            if ($item == $value){
                return true;
            }
           continue; 
        }
        if (in_array($value, $item)){
            return true;
        } else if (multi_in_array($value,$item)) {
        return true;
    }
}
return false;
}
//function to filter user's input.
function esc($value) {
    // bring the global db connect object into function
    global $conn;
    // remove empty space sorrounding string
 //   $val = trim($value);
    $val = mysqli_real_escape_string($conn, trim($value));
    return $val;
}

function mres($var){
    if (get_magic_quotes_gpc()){
        $var = stripslashes(trim($var));
    }
    return mysql_real_escape_string(trim($var));
}

//function to get file extension
function findexts($filenameext) {
    $filename = strtolower($filenameext);
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    return $ext;
}

//function to generate random alphanumeric string
function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
}

function extractKeyWords($string) {
  mb_internal_encoding('UTF-8');
  $stopwords = array();
  $string = preg_replace('/[\pP]/u', '', trim(preg_replace('/\s\s+/iu', '', mb_strtolower($string))));
  $matchWords = array_filter(explode(' ',$string) , function ($item) use ($stopwords) { return !($item == '' || in_array($item, $stopwords) || mb_strlen($item) <= 2 || is_numeric($item));});
  $wordCountArr = array_count_values($matchWords);
  arsort($wordCountArr);
  return array_keys(array_slice($wordCountArr, 0, 10));
}
?>
