<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
// All variables
$resource_id = 0;
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
$isAjax = filter_has_var(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH') && strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest';
$imageyesupload = false;

/* - - - - - - - - - - 
  - Actions on Resources
  - - - - - - - - - - - */
// if user clicks the create resource button
if (filter_has_var(INPUT_POST, 'create_resource')) {
    createResource(filter_input_array(INPUT_POST));
}
// if user clicks the Edit resource button
unsetSession('res_to_edit');
if (filter_has_var(INPUT_GET, 'resource') && filter_has_var(INPUT_GET, 'editok')) {
$res_id = filter_input(INPUT_GET, 'resource', FILTER_SANITIZE_NUMBER_INT);
if(is_numeric($res_id)) {
    $sql = "SELECT id FROM resources WHERE id=".$res_id;
    $result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
 $_SESSION['res_to_edit'] = true; 
 editResource($res_id);
} else {
    $_SESSION['res_to_edit'] = false;
} 
}
}
// if user clicks the update resource button
if (filter_has_var(INPUT_POST, 'update_resource')) {
    updateResource(filter_input_array(INPUT_POST));
}

// Image Upload function
//$file = $_FILES['r_icon']; where 'r_icon' is the input name
    function ImageIconUpload ($required=false){
    global $errors, $r_iconname, $target;
        if (is_uploaded_file($_FILES['r_icon']['tmp_name'])) {
// Image Upload 
            $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
            // Get image name
            $r_iconname = $_FILES['r_icon']['name'];  $r_iconsize = $_FILES['r_icon']['size'];
        if ($required===true && empty($r_iconname)) { array_push($errors, "The image is required");}
            // Verify file extension
            $ext = pathinfo($r_iconname, PATHINFO_EXTENSION);
            if (!empty($r_iconname) && !array_key_exists($ext, $allowed)) {array_push($errors, "Please select a valid image format");}
            // Verify file size - 1MB maximum
            $maxsize = 1 * 1024 * 1024;
            if (!empty($r_iconname) && $r_iconsize > $maxsize) { array_push($errors, "File size is larger than the allowed limit.");}
            // image file directory
            $target = IMAGE_DIR . basename($r_iconname);
    }
            if (file_exists($target)) {$r_iconname = $r_iconname; } elseif (!file_exists($target)) {
                move_uploaded_file($_FILES['r_icon']['tmp_name'], $target);
                echo "Your Resource image was uploaded successfully.";
                echo json_encode(array('status' => "success"));
            } else {
                array_push($errors, "Upload of Resource image failed.");
                echo json_encode(array('status' => "failed"));
            }
        }
       
/*       
function ResTagsSave ($res_id, $tags){  // create relationship between resource and tags
    global $conn, $errors;
                for ($i = 0; $i < count($tags); $i++) {
                $tag = filter_var(esc($tags[$i]), FILTER_SANITIZE_STRING);
                //	 echo "Topic table: " . $res_id  . ">>" . $topic_id . ">>" . count($topics_id) . ">>" . $i . "<br>" ;
                $sql = "INSERT INTO resource_tags (resource_id, tag_id) VALUES($res_id, $tag)";
               if(!mysqli_query($conn, $sql)){
                   array_push($errors, "The Resource Tag(s) failed to save");
               }
               }
                //	if(mysqli_query($conn, $t_sql)){ $_SESSION['message'] = "Topic saved successfully";} else {array_push($errors, "Topic(s) failed to save.");}
            }

function ResTopicsSave($res_id, $topics_id){
        global $conn, $errors;
                for ($i = 0; $i < count($topics_id); $i++) {
                $topic_id = filter_var(esc($topics_id[$i]), FILTER_SANITIZE_NUMBER_INT);
                //	 echo "Topic table: " . $res_id  . ">>" . $topic_id . ">>" . count($topics_id) . ">>" . $i . "<br>" ;
                $sql = "INSERT INTO resource_topic (resource_id, topic_id) VALUES($res_id, $topic_id)";
                if(!mysqli_query($conn, $sql)){
                   array_push($errors, "The Resource Topic(s) failed to save");
               }
                //	if(mysqli_query($conn, $t_sql)){ $_SESSION['message'] = "Topic saved successfully";} else {array_push($errors, "Topic(s) failed to save.");}
            }
}

function ResLinkUrl($request_values,$res_id,$link_urls){
    global $conn,$errors,$mediatype,$modalview;
    $link_names = $request_values['link_name'];
 //   $link_urls = $request_values['link_url'];
                for ($j = 0; $j < count($link_urls); $j++) {
                $link_url = esc(filter_var($link_urls[$j], FILTER_SANITIZE_URL));
                $link_name = esc(filter_var($link_names[$j], FILTER_SANITIZE_STRING));
                if (!empty($link_name)){$link_name=$link_name;}else{ $link_name = 'Link';}
                if (!isset($request_values["link_modalview"][$j])) {$modalview = 0;} else {$modalview = 1;}
                LinkUrlExt($link_url); // Get the $mediatype of the url link
                $sql = "INSERT INTO links (resources_id, url, name, mediatype_id, modalview, created_at, updated_at) VALUES($res_id, '$link_url', '$link_name', $mediatype, $modalview, now(), now())";
                 if(!mysqli_query($conn, $sql)){
                   array_push($errors, "The Resource Link(s) failed to save");
               }
                // if(mysqli_query($conn, $l_sql)){$_SESSION['message'] = "Link saved successfully";}else {array_push($errors, "Link(s) failed to save.");}
            }
}
*/
 // Function to to update the Resource Links        
    function ResLinkUrl ($request_values,$res_id,$link_urls,$linksDb=0) {
        global $conn,$errors,$mediatype,$modalview;
                $link_names = $request_values['link_name'];
                for ($j = 0; $j < count($link_urls); $j++) { 
                $link_url = esc(filter_var($link_urls[$j], FILTER_SANITIZE_URL));  // Sanitize the url input
                $link_name = esc(filter_var($link_names[$j], FILTER_SANITIZE_STRING));
                 if (!empty($link_name)){$link_name=$link_name;}else{$link_name = 'Link';} //If the link name is not empty, use the name entered, else if not link name is not entered, use link.
                LinkUrlExt($link_url); // Get the $mediatype of the url link
                if (!isset($request_values["link_modalview"][$j])) { $modalview = 0;} else { $modalview = 1; } // Check if modalview is checked
                if (isset($request_values["link_ids"][$j]) && $linksDb!==0 && multi_in_array($request_values["link_ids"][$j], array_column($linksDb, 'id'))) { // Can you find the id of the link among the database link records, if yes, then update, if no, go ahead and create it?
                   $link_id = esc(filter_var($request_values["link_ids"][$j], FILTER_SANITIZE_NUMBER_INT));
                    $sql = "UPDATE links SET url='$link_url', name='$link_name', mediatype_id=$mediatype, modalview=$modalview, updated_at= now() WHERE id = $link_id";
                } else{$sql = "INSERT INTO links (resources_id, url, name, mediatype_id, modalview, created_at, updated_at) VALUES($res_id, '$link_url', '$link_name', $mediatype, $modalview, now(), now())";}
             if(!mysqli_query($conn, $sql)){
                   array_push($errors, "The Resource Link(s) failed to save");
               }
            } 
           }
/*
function ResTypesSave ($res_id, $restypes){
    global $conn, $errors;
                for ($k = 0; $k < count($restypes); $k++) {
                $restype = filter_var(esc($restypes[$k]), FILTER_SANITIZE_NUMBER_INT);
                $sql = "INSERT INTO resource_type (resource_id, type_id) VALUES($res_id, $restype)";
                if(!mysqli_query($conn, $sql)){
                   array_push($errors, "The Resource Types(s) failed to save");
               }
                // if (mysqli_query($conn, $rtype_sql)) { $_SESSION['message'] = "Resource types saved successfully";} else { array_push($errors, "Resource type(s) failed to save.");}
            }
}

function ResLevelsSave ($res_id, $rlevels){
    global $conn, $errors;
                for ($k = 0; $k < count($rlevels); $k++) {
                $rlevel = filter_var(esc($rlevels[$k]), FILTER_SANITIZE_NUMBER_INT);
                $sql = "INSERT INTO resource_level (resource_id, schl_id) VALUES($res_id, $rlevel)";
                if(!mysqli_query($conn, $sql)){
                   array_push($errors, "The Resource Level(s) failed to save");
               }
                // if(mysqli_query($conn, $rlevel_sql)){$_SESSION['message'] = "Resource types saved successfully";} else {array_push($errors, "Resource type(s) failed to save.");}
            }
}
*/
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
//Function to insert inputs into tables with single variable types
     function ResAttrInsert($table,$inputs,$res_id=0){
             global $conn,$errors;
             if(is_array($inputs)){
               for ($i = 0; $i < count($inputs); $i++) {
                $input = esc(filter_var($inputs[$i], FILTER_SANITIZE_NUMBER_INT));
                $sql="SHOW COLUMNS FROM ".$table;
                $result = mysqli_query($conn, $sql);
                $array= mysqli_fetch_all($result, MYSQLI_ASSOC);
                $allcol= array_column($array, 'Field');
                $fields =array_diff($allcol,array('created_at','updated_at'));
               $sql = "INSERT IGNORE INTO ".$table."(`".implode('`,`', $fields)."`) VALUES($res_id, $input)";
                if(!mysqli_query($conn, $sql)){
                   array_push($errors, "There is an error in saving one of the resource attributes");
               } 
            }
            }else{exit();}
     }
       //Function to delete inputs in tables with single variable types  ResAttrDel('links',$link_ids,$rlinks);
            function ResAttrDel ($table,$inputs,$inputsFrDb,$res_id=0){
                global $conn,$errors;
                if(is_array($inputs)){
                foreach ($inputsFrDb as $inputFrDb) {
                if (!multi_in_array($inputFrDb['id'], $inputs)){
             //   $resAttrId = $inputFrDb['id'];
                $sql="SHOW COLUMNS FROM ".$table;
                $result = mysqli_query($conn, $sql);
                $array= mysqli_fetch_all($result, MYSQLI_ASSOC);
                $allcol= array_column($array, 'Field');
                $attr =array_diff($allcol,array('resource_id','created_at','updated_at'));
                   $sql="DELETE FROM ".$table." WHERE ".$attr[1]. "=" .$inputFrDb['id'];
                  if($res_id !==0){$sql.=" AND resource_id=".$res_id ;}
                   if(!mysqli_query($conn, $sql)){
                   array_push($errors, "There is an error in carrying out an operation on one of the resource attributes");
                       }
                }
                }
                }else{exit();}
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

 function ResFieldKeep($inputname,$field){
    global $errors;
   if (count($errors) != 0 && !empty($field)) {
    $_SESSION[$inputname]= $field;
    }
}

/* - - - - - - - - - - 
  -  Resource functions
  - - - - - - - - - - - */

function createResource($request_values) {
   global $conn, $errors, $r_title, $r_iconname, $res_id, $topics, $r_info, $source, $types, $link_names, $link_urls, $subpage_show, $levels, $tags,$link_ids;
    //Start of Non-Ajax File upload
    if (isset($request_values['r_icon'])) {ImageIconUpload (); }
    //End of Non-Ajax File upload

    $r_iconname = esc(filter_var($request_values['r_img'], FILTER_SANITIZE_STRING));
    $r_title = esc(filter_var($request_values['r_title'], FILTER_SANITIZE_STRING));
    $resfile = (isset($request_values['fileupload']))? esc(filter_var($request_values['fileupload'], FILTER_SANITIZE_STRING)):NULL; //Elvis or ternary operator
    $r_info = esc(filter_var($request_values['r_info'], FILTER_SANITIZE_STRING));
  //  $r_info = htmlentities(filter_var(esc($request_values['r_info']), FILTER_SANITIZE_STRING));
    $source = esc(filter_var($request_values['source'], FILTER_SANITIZE_STRING));
    $link_names = $request_values['link_name'];
    $link_urls = $request_values['link_url'];
    if (isset($request_values["resource_id"])) { $res_id = esc(filter_var($request_values['resource_id'], FILTER_SANITIZE_NUMBER_INT));}
    if (isset($request_values["link_ids"])) { $link_ids = $request_values['link_ids']; }
    if (isset($request_values["restypes"])) {$types = $request_values['restypes']; }
    if (isset($request_values["rlevels"])) { $levels = $request_values['rlevels']; }
    if (isset($request_values['topics_id'])) {$topics = explode(',', $request_values['topics_id']);}
    if (isset($request_values['r_tags'])) {$tags = explode(',', $request_values['r_tags']);}
    if (!isset($request_values['subjectshow'])) { $subpage_show = 0;}else{$subpage_show = 1;}
    if (count($request_values["link_url"]) > 1) { $multilink = 1; } else {$multilink = 0;}
    // validate form
    inputsError($r_iconname,"Resource icon image is required");
    inputsError($r_title,"Resource title is required.");
    inputsError($topics,"Atleast a topic is required for your resource.");
    inputsError($link_urls,"Resource link URL is required and must be valid URL");
    inputsError($levels,"Resource School level is required, select atleast one level");
    inputsError($types,"Atleast a resource type is required");
   
   if (count($errors) != 0 && !empty($r_iconname)) {
    $_SESSION['r_img']= $r_iconname;
    }
    
//ResFieldKeep('r_img',$r_iconname);
//ResFieldKeep('r_img',$r_iconname);
    // create resource if there are no errors in the form
     if (count($errors) == 0) {
      //   $subpage_show = filter_var($subpage_show, FILTER_SANITIZE_BOOLEAN); 
        $r_sql = "INSERT INTO resources (title, icon,resfile,info, source, multilink, subpage_show, created_at, updated_at) VALUES('$r_title', '$r_iconname','$resfile','$r_info', '$source', $multilink, $subpage_show, now(), now())";
        if (mysqli_query($conn, $r_sql)) { // if resource created successfully
            $res_id = mysqli_insert_id($conn);   // The id of the resource inserted.
            $r_inserted = true;
        } else {
            array_push($errors, "Resource failed to save.");
        }
            
        if ($r_inserted) {
            tempfiledbdelete($r_iconname);tempfiledbdelete($resfile);
          ResAttrInsert('resource_tags',$tags,$res_id);  // create relationship between resource and tags         
          ResAttrInsert('resource_topic',$topics,$res_id);    // create relationship between resource and topic         
          ResLinkUrl($request_values,$res_id,$link_urls);    // create relationship between resource and link       
          ResAttrInsert('resource_type',$types,$res_id);   // create relationship between resource and resource type
          ResAttrInsert('resource_level',$levels,$res_id);  // create relationship between resource and resource levels
            $_SESSION['res_saved'] = "Resource saved successfully";
            $_SESSION['res_id_saved'] = $res_id;
            $_SESSION['res_img_saved'] = $r_iconname;
            echo
            header('location: add_resource.php');
            exit(0);
        }
    }
}

/* * * * * * * * * * * * * * * * * * * * *
 * - Takes resource id as parameter
 * - Fetches the resource from database
 * - sets resource fields on form for editing
 * * * * * * * * * * * * * * * * * * * * * */

function editResource($res_id) {
    global $conn, $r_title, $r_icon, $r_info, $source, $rtypes, $rlinks, $subpage_show,$r_active, $reslevels, $restags,$r_topics,$r_file;
    $sql = "SELECT title,icon,resfile,info,source,subpage_show,active FROM resources r WHERE id =$res_id LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $resource = mysqli_fetch_assoc($result);
   // $final_resources = [];
    // set form values on the form to be updated
    $r_title = $resource['title'];
    $r_file = $resource['resfile'];
    $r_icon = $resource['icon'];
    $r_info = $resource['info'];
    $source = $resource['source'];
    $subpage_show = $resource['subpage_show'];
    $r_active = $resource['active'];
    $rlinks = getResourceLink($res_id);
    $rtypes = getResourcesType($res_id);
    $restags = getResourcesTags($res_id);
    $reslevels = getResourcesLevels($res_id);
    $r_topics = getResourcesTopicsSub($res_id);
}

// Disable Resource
function resDisable ($request_values){  
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
    global $conn, $errors, $r_title, $r_iconname, $res_id, $topics_id, $r_info, $source, $link_ids, $link_names, $link_urls, $subpage_show, $rlevels, $tags,$res_id;
    //Start of Non-Ajax File upload
    if (isset($request_values['r_icon'])) {ImageIconUpload (); }
    //End of Non-Ajax File upload

    $r_iconname = esc(filter_var($request_values['r_img'], FILTER_SANITIZE_STRING));
    $r_title = esc(filter_var($request_values['r_title'], FILTER_SANITIZE_STRING));
    $r_info = esc(filter_var($request_values['r_info'], FILTER_SANITIZE_STRING));
  //  $r_info = htmlentities(filter_var(esc($request_values['r_info']), FILTER_SANITIZE_STRING));
    $source = esc(filter_var($request_values['source'], FILTER_SANITIZE_STRING));
    $link_names = $request_values['link_name'];
    $link_urls = $request_values['link_url'];
    if (isset($request_values["resource_id"])) { $res_id = esc(filter_var($request_values['resource_id'], FILTER_SANITIZE_NUMBER_INT));}
    if (isset($request_values["link_ids"])) { $link_ids = $request_values['link_ids']; }
    if (isset($request_values["restypes"])) {$restypes = $request_values['restypes']; }
    if (isset($request_values["rlevels"])) { $rlevels = $request_values['rlevels']; }
    if (isset($request_values['topics_id'])) {$topics_id = explode(',', $request_values['topics_id']);}
    if (isset($request_values['r_tags'])) {$tags = explode(',', $request_values['r_tags']);}
    if (!isset($request_values['subjectshow'])) { $subpage_show = 0;}else{$subpage_show = 1;}
    if (!isset($request_values['r_active'])) {$r_active = 0;}else{$r_active = 1;}
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
         // Resource data saving function 
   $r_sql = "UPDATE resources SET title='$r_title', icon='$r_iconname', info='$r_info', source='$source', multilink=$multilink, subpage_show=$subpage_show, active=$r_active, updated_at=now() WHERE id=$res_id";
        if (mysqli_query($conn, $r_sql)) { // if resource updated successfully
            $r_updated = true;
        } else {
            array_push($errors, "Resource failed to save.");
            exit();
        }
            
        if ($r_updated) { // if the resource is successfully updated            
            ResAttrInsert('resource_tags',$tags,$res_id); // update relationship between resource and tags
            $restagsdb = getResourcesTags($res_id); //Fetch from database the resource tags
            ResAttrDel ('resource_tags',$tags,$restagsdb,$res_id); // Delete any tag that is not associated with the resource
           ResAttrInsert('resource_topic',$topics_id,$res_id);  // update relationship between resource and topic
            $topicsdb = getResourcesTopicsSub($res_id); // Get the all the topics from database for the selected resource            
            ResAttrDel ('resource_topic',$topics_id,$topicsdb,$res_id); // Delete any topic that is not associated with the resource
            $rlinks = getResourceLink($res_id);  // Get $linksDb i.e. the resource existing links in the database
            ResLinkUrl($request_values,$res_id,$link_urls,$rlinks);    // update relationship between resource and link
          //  ResAttrDel('links',$link_ids,$rlinks);      // Delete any link that is not associated with the resource
            foreach ($rlinks as $rlink) {
                if (!multi_in_array($rlink['id'], $link_ids)){
                    $del_link="DELETE FROM links WHERE id =".$rlink['id'] ;
                     mysqli_query($conn, $del_link);
                }
            }
            // Update relationship between resource and resource type
            $restypes_all = getAllResourcesType(); // Get all resource types
            foreach ($restypes_all as $rtype) { // For each resource types
                $type = esc(filter_var($rtype['id'], FILTER_SANITIZE_NUMBER_INT));      // get the id of the type
                if (!multi_in_array($type, $restypes)){ // Check the user input id is not one of the resource types
                    $rtype_sql="DELETE FROM resource_type WHERE resource_id=$res_id AND type_id=$type" ;  // if not then delete it from already resource associated types
                }else{
                   $rtype_sql = "INSERT IGNORE INTO resource_type (resource_id, type_id) VALUES($res_id, $type)"; 
                }
                mysqli_query($conn, $rtype_sql);
            }            
               
            // Update relationship between resource and school level
           $rlevels_all = getSchlLevel(); // Get all resource levels
            foreach ($rlevels_all as $rlevel) { // For each resource levels
                $level = esc(filter_var($rlevel['id'], FILTER_SANITIZE_NUMBER_INT));      // get the id of the level
                if (!multi_in_array($level, $rlevels)){ // Check the user input id is not one of the resource types
                    $rlevel_sql="DELETE FROM resource_level WHERE resource_id=$res_id AND schl_id=$level" ;  // if not then delete it from already resource associated types
                }else{
                   $rlevel_sql = "INSERT IGNORE INTO resource_level (resource_id, schl_id) VALUES($res_id, $level)"; 
                }
                mysqli_query($conn, $rlevel_sql);
            }  
       // Delete the previous resource icon image
            if (isset($request_values['editedoutResImg'])) {
            $r_icondel = filter_var($request_values['editedoutResImg'], FILTER_SANITIZE_STRING);
            $target = IMAGE_DIR . $r_icondel;
            deleteFileDir($target);
            }
            tempfiledbdelete($r_iconname); //Clear the uploaded image as tempfile
            $_SESSION['res_updated'] = "yes_updated";
            echo
            header('location: edit.php?resource='.$res_id.'&editok='.md5(rand(1000, 99999)));
            exit(0);
        }
    }
}

// delete resource
function deleteResource($request_values) {
    global $conn;
    $r_img = filter_var($request_values['res_image'], FILTER_SANITIZE_STRING);
    $res_id = filter_var($request_values['res_id'], FILTER_SANITIZE_NUMBER_INT);

    $sql = "DELETE FROM resources WHERE id=$res_id;";
    $sql .= "DELETE FROM resource_tags WHERE resource_id=$res_id;";
    $sql .= "DELETE FROM links WHERE resources_id=$res_id;";
    $sql .= "DELETE FROM resource_type WHERE resource_id=$res_id;";
    $sql .= "DELETE FROM resource_level WHERE resource_id=$res_id";
       $target = IMAGE_DIR . $r_img;
    if (mysqli_multi_query($conn, $sql) && deleteFileDir($target)) {
            echo json_encode(array('msg' => 'deleted'));
    }else {
        echo json_encode(array('msg' => 'failed'));
    }
     exit();
}


// delete topic
function deleteTopic($request_values) {
    global $conn;
    $topic_id = filter_var($request_values['topic_id'], FILTER_SANITIZE_NUMBER_INT);
    $sql = "DELETE FROM topics WHERE id=$topic_id";
    if (mysqli_query($conn, $sql)) {
            echo json_encode(array('msg' => 'The Topic successfully deleted'));
            exit();
    }
}

// delete subject
function deleteSubject($request_values) {
    global $conn;
    $subject_id = filter_var($request_values['subject_id'], FILTER_SANITIZE_NUMBER_INT);
    $sql = "DELETE FROM subject WHERE id=$subject_id";
    if (mysqli_query($conn, $sql)) {
            echo json_encode(array('msg' => 'The Subject successfully deleted'));
            exit();
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
 * Ajax call:  Search and Returns all tags
 * * * * * * * * * * * * */

function getAllTags($keywrd) {
    global $conn;
    $tag= '%'.filter_var($keywrd, FILTER_SANITIZE_STRING).'%';
    $sql = "SELECT tags.id AS tag_id,tags.tag AS tag FROM tags WHERE tag like '".$tag."' ORDER BY tag ASC";
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
if(is_numeric($t_id)){
    $sql = "SELECT title, def, subject_id FROM topics WHERE id=".$t_id." LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $topic = mysqli_fetch_assoc($result);
    return $topic['title'];
}else{exit();}
}

/* * * * * * * * * * * * * * * *
 * Returns topic name by topic id
 * * * * * * * * * * * * * * * * */

function getTopicInfoById($id) {
    global $conn;
$t_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
    $sql = "SELECT title, info, subject_id FROM topics WHERE id=$t_id LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $topic = mysqli_fetch_assoc($result);
    return $topic;
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
            echo json_encode(array('msg' => 'The tag has been successfully saved.', 'tag_id' => $inserted_tag));
            exit();
        } else {
        echo json_encode(array('msg' => 'The tag failed to save, try again.'));
        exit();
    }
    } else {
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
 *  Update Subject
 * * * * * * * * * * * * */
function saveAjaxSubject($request_values) {
global $conn;
$subject_name = esc(filter_var($request_values['subject_name'], FILTER_SANITIZE_STRING));
$subject_def = esc(filter_var($request_values['subject_def'], FILTER_SANITIZE_STRING));
if (empty($subject_name)){exit();}
$subject_check = "SELECT * FROM subject WHERE name='$subject_name' LIMIT 1";
    $result = mysqli_query($conn, $subject_check);
if (mysqli_num_rows($result)>0) {// if topic exists
      //  array_push($errors, "A post already exists with that title.");
        echo json_encode(array('msg' => 'exist'));
        exit();
    }elseif (mysqli_num_rows($result) == 0) { // if topic doesn't exists
        $query = "INSERT INTO subject (name, def) VALUES('$subject_name', '$subject_def')";
        $result = mysqli_query($conn, $query);
        if ($result) {
     //       $inserted_topic_id = mysqli_insert_id($conn);
            echo json_encode(array('msg' => 'saved'));
            exit();
        }else {
        echo json_encode(array('msg' => 'notsaved'));
        exit();
    } 
    }else {
        echo json_encode(array('msg' => 'failed'));
        exit();
        }
}
/* * * * * * * * * * * *
 *  Update Subject
 * * * * * * * * * * * * */
function updateAjaxSubject($request_values) {
global $conn;
$subject_id = esc(filter_var($request_values['subject_id'], FILTER_SANITIZE_NUMBER_INT));
$subject_name = esc(filter_var($request_values['subject_name'], FILTER_SANITIZE_STRING));
$subject_def = esc(filter_var($request_values['subject_def'], FILTER_SANITIZE_STRING));
if (!empty($subject_name) && $subject_id != 0){
    $sql = "UPDATE subject SET name='$subject_name', def='$subject_def', updated_at=now() WHERE id=$subject_id";
    $result = mysqli_query($conn, $sql);
        if ($result) {
            echo json_encode(array('msg' => 'updated'));
            exit();
        }else {
        echo json_encode(array('msg' => 'notupdated'));
        exit();
    } 
    }else {
        echo json_encode(array('msg' => 'inputerror'));
        exit();
        }
}
/* * * * * * * * * * * *
 *  Update Topic in respect to subject
 * * * * * * * * * * * * */
function updateAjaxTopic($request_values) {
     global $conn;
    $topic_name = esc(filter_var($request_values['topic_name'], FILTER_SANITIZE_STRING));
    $topic_def = esc(filter_var($request_values['topic_def'], FILTER_SANITIZE_STRING));
    $subject_id = esc(filter_var($request_values['subject_id'], FILTER_SANITIZE_NUMBER_INT)); 
    $topic_id = esc(filter_var($request_values['topic_id'], FILTER_SANITIZE_NUMBER_INT)); 
    $active = esc(filter_var($request_values['topic_active'], FILTER_SANITIZE_NUMBER_INT)); 
    if (!empty($topic_name) && $subject_id != 0 && $topic_id !=0) { 
    $sql = "UPDATE topics SET title='$topic_name', def='$topic_def', subject_id='$subject_id', active='$active', updated_at=now() WHERE id=$topic_id";
    $result = mysqli_query($conn, $sql);
        if ($result) {
            echo json_encode(array('msg' => 'updated'));
            exit();
        }else {
        echo json_encode(array('msg' => 'notupdated'));
        exit();
    } 
    }else {
        echo json_encode(array('msg' => 'inputerror'));
        exit();
        }
}



/* * * * * * * * * * * *
 *  Create Topic in respect to subject
 * * * * * * * * * * * * */

function createAjaxTopic($request_values) {
    global $conn, $errors, $topic_name, $topic_def, $subject_id;
    $topic_name = esc(filter_var($request_values['topic_name'], FILTER_SANITIZE_STRING));
    $topic_def = esc(filter_var($request_values['topic_def'], FILTER_SANITIZE_STRING));
    $subject_id = esc(filter_var($request_values['subject_id'], FILTER_SANITIZE_NUMBER_INT));
    // validate form
    if (empty($topic_name) || $subject_id == 0) {array_push($errors, "Ensure Topic and Subject are filled"); exit(); }
    // Ensure that no topic is saved twice under a subject. 
    $topic_check_query = "SELECT * FROM topics WHERE title='$topic_name' AND subject_id=$subject_id LIMIT 1";
    $result = mysqli_query($conn, $topic_check_query);
        
    if (mysqli_num_rows($result)>0) {// if topic exists
      //  array_push($errors, "A post already exists with that title.");
        echo json_encode(array('msg' => 'exist'));
        exit();
    }elseif (mysqli_num_rows($result) == 0) { // if topic doesn't exists
        $query = "INSERT INTO topics (title, def, subject_id) VALUES('$topic_name', '$topic_def', '$subject_id')";
        $result = mysqli_query($conn, $query);
        if ($result) {
     //       $inserted_topic_id = mysqli_insert_id($conn);
            echo json_encode(array('msg' => 'saved'));
            exit();
        }else {
        echo json_encode(array('msg' => 'notsaved'));
        exit();
    } 
    }else {
        echo json_encode(array('msg' => 'failed'));
        exit();
        }
}


/* * * * * * * * * * * *
 *  Ajax call: Returns all topics with corresponding subject
 * * * * * * * * * * * * */

function getAllTopicsSubject($query) {
    global $conn;
    $query='%'.$query.'%';
    $sql = "SELECT t.id AS topic_id,t.title AS topic_title,t.def AS topic_def,t.active AS topic_active, s.id AS subject_id, s.name AS subject FROM topics t JOIN subject s ON t.subject_id=s.id Where t.title like '".$query."' ORDER BY t.title ASC";
    $result = mysqli_query($conn, $sql);
    if($result){
    $topicsub = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $topicsub;
}
}

/* * * * * * * * * * * * * * *
 * Returns Subject Topics
 * * * * * * * * * * * * * * */

function getSubject_topics($subject) {
    global $conn, $topics_count;
    // Get single subject topics
    $sql = "SELECT * FROM topics WHERE subject_id = (SELECT id FROM subject WHERE id = '$subject') ORDER BY title ASC";
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
    global $subj_res_count;
    global $res_row;
    // Get single subject topics
 //   $topic = getSubject_topics($subject);
    $sql = "SELECT r.id,r.title,r.icon,r.info,r.multilink,r.subpage_show,r.active FROM resources r LEFT JOIN resource_topic rt ON rt.resource_id = r.id LEFT JOIN topics t ON t.id = rt.topic_id WHERE subject_id=$subject";
    $result = mysqli_query($conn, $sql);
    $resources = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $res_row= mysqli_fetch_assoc($result);
    $subj_res_count = mysqli_num_rows($result);
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
   // $resources_count = mysqli_num_rows($result);
    $resources_count = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM links"));
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

/* * * * * * * * * * * * * * *
* Returns Topics
* * * * * * * * * * * * * * */
function getResourceTopics($resource){
    global $conn;
    $sql = "SELECT DISTINCT t.id, t.title, t.subject_id AS subject FROM `topics` t LEFT JOIN resource_topic rt ON rt.topic_id = t.id WHERE rt.resource_id= $resource ORDER BY t.title ASC";
    $result = mysqli_query($conn, $sql);
    $rtopics = mysqli_fetch_all($result,MYSQLI_ASSOC);
    return $rtopics;
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
            if ($item == $value){return true;}
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
//function to filter user's input.
function mres($var){
    if (get_magic_quotes_gpc()){
        $var = stripslashes(trim($var));
    }
    return mysql_real_escape_string(trim($var));
}


// Function to make url slug from the topic title.
function makeSlug($string){
	$string = strtolower($string);
	$slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
	return $slug;
}

//function to get file extension
function findexts($filenameext) {
    $filename = strtolower($filenameext);
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    return $ext;
}

//function to generate random alphanumeric string
function generateRandomString($length = 20) {
    do{$y = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);}
    while (is_numeric($y[0]));
    return $y;
    }

// Function to xtract keywords from a sentence
function extractKeyWords($strings) {
  mb_internal_encoding('UTF-8');
  $stopwords = array();
  $string = preg_replace('/[\pP]/u', '', trim(preg_replace('/\s\s+/iu', '', mb_strtolower($strings))));
  $matchWords = array_filter(explode(' ',$string) , function ($item) use ($stopwords) { return !($item == '' || in_array($item, $stopwords) || mb_strlen($item) <= 2 || is_numeric($item));});
  $wordCountArr = array_count_values($matchWords);
  arsort($wordCountArr);
  return array_keys(array_slice($wordCountArr, 0, 10));
}

// Function to unset session if set
function unsetSession($var){
    if(isset($_SESSION[$var])) {unset($_SESSION[$var]);}
}

//Function to return permited files
function file_contains($dir){
     if(!is_array($dir) && is_dir($dir)){$dir=scandir($dir);} // if $dir inot array and confirmed directory then scandir to array
     if(is_array($dir)){
   $xy = preg_grep('~\.(mp4|swf|html|htm)$~', $dir); // $dir can only be an array
     }
   return array_values($xy);
 }
 
 
 // Function to ensure there is no folder containing another folder without files permited done by Adeyinka on Feb 27 to 28, 2020. 
 function dir_filter ($dir){
     if(is_dir($dir)){ // if $dir is directory
        $dir_root =  dirname($dir); // Get the parent folder
    $subdir= array_diff(scandir($dir),array('.','..')); // remove . and .. from the scandir output and return the actual content of $dir
    if(count($subdir)===1 && is_dir($dir.'/'.$subdir[2]) ){  // if the content of the directory is one and it is a directory 
       $randName = filter_var(generateRandomString(), FILTER_SANITIZE_STRING); // create a random file names
        $target = $dir_root.'/'.$randName;
        rename($dir.'/'.$subdir[2],$target); // move the single sub-directory to the root folder with the autogenerated new name.
       rmdir($dir); // remove the directory
     return dir_filter($target); //check the directory again to ensure that there is no single sub-directory content
    } 
    if(count($subdir)>=1){
    $randName = filter_var(generateRandomString(), FILTER_SANITIZE_STRING);
    $target = $dir_root.'/'.$randName;
    rename($dir,$target);
    return $randName;
    }
     }
     } 

// Function to capture file upload and keep till they are saved so any unsaved upload in form can be cleaned off the server.  
     function tempfiledbsave($file,$path){ //$file should be full directory plus the file and $type can only be file, dir or icon as string
       global $conn;
       $query = "INSERT INTO tempfileuploaded (file,path,time) VALUES('$file','".esc($path.$file)."',now())";  
        mysqli_query($conn, $query);
       }
   //  Delete temp file records from db  
   function tempfiledbdelete($file){
       global $conn;
   $sql = "SELECT * FROM `tempfileuploaded` WHERE file ='".esc($file)."'";
    $result= mysqli_query($conn, $sql);
    if (mysqli_num_rows($result)>0){
        $sql="DELETE FROM `tempfileuploaded` WHERE file ='".esc($file)."'";
        mysqli_query($conn, $sql);
    }
   }
   
 // General Delete temp files from the directories   
 function tempfilesclr(){
    global $conn;
    $sql = "SELECT `path`,`file` FROM `tempfileuploaded`";
$result= mysqli_query($conn,$sql);
    if ($result){
       $files = mysqli_fetch_all($result, MYSQLI_ASSOC);
        foreach ($files as $file){
     if(deleteFileDir($file['path'])){
       tempfiledbdelete($file['file']);  
     }
}
}
}
     
   // Function to delete any file or directory recursively.
     function deleteFileDir($dirfile){
    if(file_exists($dirfile)){
         if(!is_dir($dirfile)){
           if (unlink($dirfile)){return true;}
         }else{
         if($dirfile[strlen($dirfile) -1] !='/'){
          $dirfile.='/';   
         }
         $files = glob($dirfile.'*', GLOB_MARK);
         foreach ($files as $file){
             if(is_dir($file)){
                 deleteFileDir($file);
             }else{
                 unlink($file);
             }
         }
        if(rmdir($dirfile)){return true;}
     }
    }else{return false;}
     }
