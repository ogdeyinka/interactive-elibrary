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
$mediatypes = 0;
$modalviews = "";
$restypes = "";
$rlevels = "";
$ddtopic = false;
$r_inserted = false;
$clicks = 0;
$link_urls = "";
$link_names = "";
$errors = [];
$tags = $tag = "";
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$imageyesupload = false;
/* - - - - - - - - - - 
  -  Post functions
  - - - - - - - - - - - */
/*
  function getAllPosts()
  {
  // get all posts from DB
  global $conn;

  // Admin can view all posts
  // Author can only view their posts
  if ($_SESSION['user']['role'] == "Admin") {
  $sql = "SELECT * FROM posts";
  } elseif ($_SESSION['user']['role'] == "Author") {
  $user_id = $_SESSION['user']['id'];
  $sql = "SELECT * FROM posts WHERE user_id=$user_id";
  }
  $result = mysqli_query($conn, $sql);
  $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

  $final_posts = array();
  foreach ($posts as $post) {
  $post['author'] = getPostAuthorById($post['user_id']);
  array_push($final_posts, $post);
  }
  return $final_posts;
  }
  // get the author/username of a post
  function getPostAuthorById($user_id)
  {
  global $conn;
  $sql = "SELECT username FROM users WHERE id=$user_id";
  $result = mysqli_query($conn, $sql);
  if ($result) {
  // return username
  return mysqli_fetch_assoc($result)['username'];
  } else {
  return null;
  }
  }

 */

/* - - - - - - - - - - 
  -  Post actions
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


/* - - - - - - - - - - 
  -  Post functions
  - - - - - - - - - - - */

function createResource($request_values) {
    global $conn, $errors, $r_title, $r_iconname, $r_iconsize, $r_icontype, $topics_id, $r_info, $source, $restypes, $link_names, $link_urls, $subpage_show, $imageyesupload, $rlevels, $tags;
    //Start of Non-Ajax File upload
    if (isset($request_values['r_icon'])) {
        if (is_uploaded_file($_FILES['r_icon']['tmp_name'])) {
// Image Upload 
            $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
            // Get image name
            $r_iconname = $_FILES['r_icon']['name'];
            $r_iconsize = $_FILES['r_icon']['size'];
            $r_icontype = $_FILES['r_icon']['type'];
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
            //   $target = "../static/images/" . basename($r_iconname);
    }
            $imageyesupload = true;
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
    if (empty($r_iconname)) {
        array_push($errors, "Resource icon image is required");
    }
    if (empty($r_title)) {
        array_push($errors, "Resource title is required");
    }
    if (empty($topics_id) && !filter_var($topics_id, FILTER_VALIDATE_INT)) {
        array_push($errors, "Atleast a topic is required for your resource");
    }
    if (empty($link_urls) && !filter_var($link_urls, FILTER_VALIDATE_URL)) {
        array_push($errors, "Resource link URL is required and must be valid URL");
    }
    if (empty($rlevels) && !filter_var($rlevels, FILTER_VALIDATE_INT)) {
        array_push($errors, "Resource School level is required, select atleast one level");
    }
    if (empty($restypes) && !filter_var($restypes, FILTER_VALIDATE_INT)) {
        array_push($errors, "Atleast a resource type is required");
    }

    if ($imageyesupload) {
        // If no error then image can be uploaded
        if (count($errors) == 0) {

            if (file_exists($target)) {
                $r_iconname;
            } elseif (!file_exists($target)) {
                move_uploaded_file($_FILES['r_icon']['tmp_name'], $target);
                echo "Your Resource image was uploaded successfully.";
                header("Content-Type", "application/jason");
                echo json_encode(array('status' => "success"));
            } else {
                array_push($errors, "Upload of Resource image failed.");
                header("Content-Type", "application/jason");
                echo json_encode(array('status' => "failed"));
            }
        }
    }

    if (count($errors) != 0 && !empty($r_iconname)) {
     $target = IMAGE_DIR . $r_iconname;
     if (file_exists($target)) {
        unlink($target);
    }
    }
    // create post if there are no errors in the form
     if (count($errors) == 0) {
         $r_title = filter_var($r_title, FILTER_SANITIZE_STRING);
         $r_iconname = filter_var($r_iconname, FILTER_SANITIZE_STRING);
         $r_info = filter_var ($r_info, FILTER_SANITIZE_STRING);
         $source = filter_var($source, FILTER_SANITIZE_STRING);
      //   $subpage_show = filter_var($subpage_show, FILTER_SANITIZE_BOOLEAN); 
        $r_sql = "INSERT INTO resources (title, icon, info, source, multilink, subpage_show, created_at, updated_at) VALUES('$r_title', '$r_iconname', '$r_info', '$source', $multilink, $subpage_show, now(), now())";
        if (mysqli_query($conn, $r_sql)) { // if resource created successfully
            $inserted_resource_id = mysqli_insert_id($conn);
            $r_inserted = true;
        } else {
            array_push($errors, "Resource failed to save.");
        }
        if ($r_inserted) {
            // create relationship between resource and tags
            for ($i = 0; $i < count($tags); $i++) {
                $tag = filter_var(esc($tags[$i]), FILTER_SANITIZE_STRING);
                //	 echo "Topic table: " . $inserted_resource_id  . ">>" . $topic_id . ">>" . count($topics_id) . ">>" . $i . "<br>" ;
                $tg_sql = "INSERT INTO resource_tags (resource_id, tag_id) VALUES($inserted_resource_id, $tag)";
                mysqli_query($conn, $tg_sql);
                //	if(mysqli_query($conn, $t_sql)){ $_SESSION['message'] = "Topic saved successfully";} else {array_push($errors, "Topic(s) failed to save.");}
            }
            // create relationship between resource and topic
            for ($i = 0; $i < count($topics_id); $i++) {
                $topic_id = filter_var(esc($topics_id[$i]), FILTER_SANITIZE_NUMBER_INT);
                //	 echo "Topic table: " . $inserted_resource_id  . ">>" . $topic_id . ">>" . count($topics_id) . ">>" . $i . "<br>" ;
                $t_sql = "INSERT INTO resource_topic (resource_id, topic_id) VALUES($inserted_resource_id, $topic_id)";
                mysqli_query($conn, $t_sql);
                //	if(mysqli_query($conn, $t_sql)){ $_SESSION['message'] = "Topic saved successfully";} else {array_push($errors, "Topic(s) failed to save.");}
            }
            // create relationship between resource and link
            for ($j = 0; $j < count($link_urls); $j++) {
                $link_url = filter_var(esc($link_urls[$j]), FILTER_SANITIZE_URL);
                $link_name = filter_var(esc($link_names[$j]), FILTER_SANITIZE_STRING) ;
                if (!isset($request_values["link_modalview"])) {
                    $modalview = 0;
                } else {
                    $modalview = 1;
                }
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
                // echo "Links: " . $inserted_resource_id . ">>" . $link_url . ">>" .  $link_name . ">>" .  $mediatype . ">>" .  $modalview . ">>" . count($link_urls) . ">>" . $j . "<br>";
                $l_sql = "INSERT INTO links (resources_id, url, name, mediatype_id, modalview, created_at, updated_at) VALUES($inserted_resource_id, '$link_url', '$link_name', $mediatype, $modalview, now(), now())";
                mysqli_query($conn, $l_sql);
                // if(mysqli_query($conn, $l_sql)){$_SESSION['message'] = "Link saved successfully";}else {array_push($errors, "Link(s) failed to save.");}
            }
            // create relationship between resource and resource type
            for ($k = 0; $k < count($restypes); $k++) {
                $restype = filter_var(esc($restypes[$k]), FILTER_SANITIZE_NUMBER_INT);
                $rtype_sql = "INSERT INTO resource_type (resource_id, type_id) VALUES($inserted_resource_id, $restype)";
                mysqli_query($conn, $rtype_sql);
                // if (mysqli_query($conn, $rtype_sql)) { $_SESSION['message'] = "Resource types saved successfully";} else { array_push($errors, "Resource type(s) failed to save.");}
            }
            for ($k = 0; $k < count($rlevels); $k++) {
                $rlevel = filter_var(esc($rlevels[$k]), FILTER_SANITIZE_NUMBER_INT);
                $rlevel_sql = "INSERT INTO resource_level (resource_id, schl_id) VALUES($inserted_resource_id, $rlevel)";
                mysqli_query($conn, $rlevel_sql);
                // if(mysqli_query($conn, $rlevel_sql)){$_SESSION['message'] = "Resource types saved successfully";} else {array_push($errors, "Resource type(s) failed to save.");}
            }
            $_SESSION['message'] = "Resource saved successfully";
            echo
            header('location: add_resource.php');
            exit(0);
        }
    }
}

/* * * * * * * * * * * * * * * * * * * * *
 * - Takes post id as parameter
 * - Fetches the post from database
 * - sets post fields on form for editing
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

function updateResource($request_values) {
    global $conn, $errors, $post_id, $title, $featured_image, $topic_id, $body, $published;

    $title = esc($request_values['title']);
    $body = esc($request_values['body']);
    $post_id = esc($request_values['post_id']);
    if (isset($request_values['topic_id'])) {
        $topic_id = esc($request_values['topic_id']);
    }
    // create slug: if title is "The Storm Is Over", return "the-storm-is-over" as slug
    $post_slug = makeSlug($title);

    if (empty($title)) {
        array_push($errors, "Post title is required");
    }
    if (empty($body)) {
        array_push($errors, "Post body is required");
    }
    // if new featured image has been provided
    if (isset($_POST['featured_image'])) {
        // Get image name
        $featured_image = $_FILES['featured_image']['name'];
        // image file directory
        $target = "../static/images/" . basename($featured_image);
        if (!move_uploaded_file($_FILES['featured_image']['tmp_name'], $target)) {
            array_push($errors, "Failed to upload image. Please check file settings for your server");
        }
    }

    // register topic if there are no errors in the form
    if (count($errors) == 0) {
        $query = "cc slug='$post_slug', views=0, image='$featured_image', body='$body', published=$published, updated_at=now() WHERE id=$post_id";
        // attach topic to post on post_topic table
        if (mysqli_query($conn, $query)) { // if post created successfully
            if (isset($topic_id)) {
                $inserted_post_id = mysqli_insert_id($conn);
                // create relationship between post and topic
                $sql = "INSERT INTO post_topic (post_id, topic_id) VALUES($inserted_post_id, $topic_id)";
                mysqli_query($conn, $sql);
                $_SESSION['message'] = "Post created successfully";
                header('location: posts.php');
                exit(0);
            }
        }
        $_SESSION['message'] = "Post updated successfully";
        header('location: posts.php');
        exit(0);
    }
}

// delete blog post
function deletePost($post_id) {
    global $conn;
    $sql = "DELETE FROM posts WHERE id=$post_id";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = "Post successfully deleted";
        header("location: posts.php");
        exit(0);
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
    $sql = "SELECT title FROM topics WHERE id=$id";
    $result = mysqli_query($conn, $sql);
    $topic = mysqli_fetch_assoc($result);
    return $topic['title'];
}

/* * * * * * * * * * * *
 *  Resources Click Counts
 * * * * * * * * * * * * */

function resLinkClick($request_values) {
    global $conn, $errors, $resource_id, $clicks;
    $resource_id = $_POST['res_id'];
    if (is_numeric($resource_id)) {
        $sql = "SELECT resources.clicks FROM resources WHERE id=$resource_id";
        $result = mysqli_query($conn, $sql);
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
    global $conn, $tag;
    $tag = filter_var(esc($request_values['ajaxtagsave']), FILTER_SANITIZE_STRING);
    if (strlen($tag)>1){
       // Ensure that no topic is saved twice under a subject. 
    $tag_check = "SELECT * FROM tags WHERE tag='$tag' LIMIT 1";
    $result = mysqli_query($conn, $tag_check); 
    if (mysqli_num_rows($result) == 0) {
       $query = "INSERT INTO tags (tag) VALUES('$tag')";
        $result = mysqli_query($conn, $query);
        if ($result){
            $inserted_tag = mysqli_insert_id($conn);
            header('Content-Type: application/json');
            echo json_encode(array('msg' => 'success', 'tag_id' => $inserted_tag));
            exit();
        } else {
        header('HTTP/1.1 500 Internal Server Booboo');
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(array('msg' => 'failure'));
        exit();
    }
    }
    }
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
    $sql = "SELECT id, url, name, modalview,mediatype_id,resources_id FROM links WHERE resources_id = (SELECT resources.id FROM resources where id =$res_id)";
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

//Resources By type
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
    $exts = split("[/\\.]", $filename);
    $n = count($exts) - 1;
    $ext = $exts[$n];
    return $ext;
}

//function to generate random alphanumeric string
function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
}

?>
