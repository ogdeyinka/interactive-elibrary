<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
/* * * * * * * * * * * *
* Starts of Interactive Functions
* * * * * * * * * * * * */

/* * * * * * * * * * * *
*  Returns all topics
* * * * * * * * * * * * */
function getAllTopics()
{
	global $conn;
	$sql = "SELECT * FROM topics";
	$result = mysqli_query($conn, $sql);
	$topics = mysqli_fetch_all($result, MYSQLI_ASSOC);
	return $topics;
}


/* * * * * * * * * * * *
*  Returns all subjects
* * * * * * * * * * * * */
function getAllSubject()
{
	global $conn;
	$sql = "SELECT * FROM subject ORDER BY name";
	$result = mysqli_query($conn, $sql);
	$subjects = mysqli_fetch_all($result, MYSQLI_ASSOC);
	return $subjects;
}
/* * * * * * * * * * * * * * * *
* Returns subject name by id
* * * * * * * * * * * * * * * * */
function getSubjectNameById($id)
{
    global $conn;
    $sql = "SELECT name FROM subject WHERE id=$id";
    $result = mysqli_query($conn, $sql);
    $subject = mysqli_fetch_assoc($result);
    return $subject['name'];
}

/* * * * * * * * * * * * * * * *
* Returns topic name by topic id
* * * * * * * * * * * * * * * * */
function getTopicNameById($id)
{
    global $conn;
    $sql = "SELECT title FROM topics WHERE id=$id LIMIT 1";
    $result = mysqli_query($conn, $sql);
   $topic = mysqli_fetch_assoc($result);
   return $topic['title'];
}

/* * * * * * * * * * * * * * * *
* Returns Topic Subject name by topic id
* * * * * * * * * * * * * * * * */
function getTopicSubjectNameById($topic_id)
{
    global $conn;
    $sql = "SELECT t.title AS topic_title, s.id AS subject_id, s.name AS subject FROM topics t JOIN subject s ON t.subject_id=s.id WHERE t.id=$topic_id";
    $result = mysqli_query($conn, $sql);
   $topic = mysqli_fetch_assoc($result);
   return $topic;
}

/* * * * * * * * * * * * * * * *
* Returns tag name by id
* * * * * * * * * * * * * * * * */
function getTagNameById($id)
{
    global $conn;
    $sql = "SELECT tag, def FROM tags WHERE id=$id LIMIT 1";
    $result = mysqli_query($conn, $sql);
   $tag = mysqli_fetch_assoc($result);
   return $tag;
}

/* * * * * * * * * * * * * * *
 * Returns resource count as word or number
 * * * * * * * * * * * * * * */

function resourceCount($form='') { //$form is the format of the expected number, either as words or numeral
    
    global $conn, $resources_count;
    $resources_count = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM links"));
    if($form=='word'){
    return numberTowords ($resources_count);
    }if($form=='number'|| is_numeric($form) || !empty($form)){
        return number_format($resources_count);
    }
}


/* * * * * * * * * * * * * * *
* Returns Subject Topics Name
* * * * * * * * * * * * * * */
function getSubject_topics($subject){
	global $conn;
	global $topics_count;
	// Get single subject topics
	$sql = "SELECT * FROM topics WHERE subject_id = (SELECT id FROM subject WHERE id = '$subject') ORDER BY title ASC";
	$result = mysqli_query($conn, $sql);
	$topics_count = mysqli_num_rows($result);
	$topics = mysqli_fetch_all($result, MYSQLI_ASSOC); 
	return $topics;
}

/* * * * * * * * * * * * * * *
* Returns Resources by Subjects
* * * * * * * * * * * * * * */
function getSubResources($subject){
    global $conn;
  //  global $resources_count;
    global $res_row;
    // Get single subject topics
 //   $topic = getSubject_topics($subject);
    $sql = "SELECT r.id,r.title,r.icon,r.info,r.multilink,r.active FROM resources r LEFT JOIN resource_topic rt ON rt.resource_id = r.id LEFT JOIN topics t ON t.id = rt.topic_id WHERE r.subpage_show=1 AND subject_id=$subject";
    $result = mysqli_query($conn, $sql);
    $resources = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $res_row= mysqli_fetch_assoc($result);
//	return $resources;
    $final_resources = array();
    foreach ($resources as $resource) {
        $resource['link'] = getResourceLink($resource['id']);
        array_push($final_resources, $resource);
    }
    return $final_resources;
}
/* * * * * * * * * * * * * * *
* Check if available
* * * * * * * * * * * * * * */
function data_exist ($table,$data) {  // $data is an id of the table $table
    global $conn;
    $query= "SELECT id FROM ".$table." WHERE id='$data' LIMIT 1";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result)>0){return true;}else{return false;}
}
/* * * * * * * * * * * * * * *
* Returns Resources by Topics
* * * * * * * * * * * * * * */
function getTopicResources($topic){
	global $conn;
	global $resources_count;
	global $res_row;
	// Get single subject topics
	$sql = "SELECT r.*, t.title AS topic_title  FROM resources r LEFT JOIN resource_topic rt ON rt.resource_id = r.id LEFT JOIN topics t ON t.id = rt.topic_id WHERE r.subpage_show=0 AND t.id =$topic ORDER BY r.clicks DESC";
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
* Returns Resources by Tag
* * * * * * * * * * * * * * */
function getTagResources($tag){
	global $conn;
	global $resources_count;
	global $res_row;
	// Get single subject topics
	$sql = "SELECT r.*, tg.tag AS tag  FROM resources r LEFT JOIN resource_tags rtg ON rtg.resource_id = r.id LEFT JOIN tags tg ON tg.id = rtg.tag_id WHERE tg.id =$tag ORDER BY r.clicks DESC";
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
* Resources links
* * * * * * * * * * * * * * */

function getResourceLink($link){
	global $conn;
    $sql = "SELECT id, url, name, modalview FROM links WHERE resources_id = (SELECT resources.id FROM resources where id =$link)";
	$result = mysqli_query($conn, $sql);
	$links = mysqli_fetch_all($result, MYSQLI_ASSOC);
	$final_links=array();
    foreach ($links as $link) {
        $link['mediatype'] = getMediatype($link['id']);
        array_push($final_links, $link);
    }
    return $final_links;
}

/* * * * * * * * * * * * * * *
* Returns Link
* * * * * * * * * * * * * * */
function getLink($link){
	global $conn;
    $sql = "SELECT id, url, name, modalview FROM links WHERE id = $link";
	$result = mysqli_query($conn, $sql);
	$rlink = mysqli_fetch_array($result,MYSQLI_ASSOC);
    return $rlink;
}

/* * * * * * * * * * * * * * *
* Returns Topic tags
* * * * * * * * * * * * * * */
function getTopicTags($topic){
    global $conn;
    $sql = "SELECT DISTINCT tag.id, tag.tag, tag.def FROM `tags` tag LEFT JOIN resource_tags rtag ON rtag.tag_id=tag.id LEFT JOIN resources r ON r.id=resource_id LEFT JOIN resource_topic rt ON rt.resource_id = r.id LEFT JOIN topics t ON t.id = rt.topic_id WHERE t.id =$topic ORDER BY tag.tag ASC";
    $result = mysqli_query($conn, $sql);
    $tags = mysqli_fetch_all($result,MYSQLI_ASSOC);
    return $tags;
}

/* * * * * * * * * * * * * * *
* Returns Topic tags
* * * * * * * * * * * * * * */
function getResourceTopics($resource){
    global $conn;
    $sql = "SELECT DISTINCT t.id, t.title, t.subject_id AS subject FROM `topics` t LEFT JOIN resource_topic rt ON rt.topic_id = t.id WHERE rt.resource_id= $resource ORDER BY t.title ASC";
    $result = mysqli_query($conn, $sql);
    $rtopics = mysqli_fetch_all($result,MYSQLI_ASSOC);
    return $rtopics;
}

/* * * * * * * * * * * * * * *
* Returns Mediatype by Link
* * * * * * * * * * * * * * */
function getMediatype($link){
    global $conn;
    $sql = "SELECT name FROM mediatype WHERE id = (SELECT mediatype_id FROM links where id =$link)";
    $result = mysqli_query($conn, $sql);
    $mediatypes = mysqli_fetch_assoc($result);
    return $mediatypes;
}

/* * * * * * * * * * * * * * *
* Returns Resources Types
* * * * * * * * * * * * * * */
function getResourcesType($res){
    global $conn;
    $sql = "SELECT rt.id, rt.name FROM resourcetype rt INNER JOIN resource_type rjt ON rjt.type_id=rt.id INNER JOIN resources r ON rjt.resource_id=r.id WHERE r.id=$res";
    $result = mysqli_query($conn, $sql);
    $types = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $types;
}

/* * * * * * * * * * * * * * *
* Returns Resources Tags
* * * * * * * * * * * * * * */
function getResourcesTags($res){
    global $conn;
    $sql = "SELECT tg.* FROM tags tg INNER JOIN resource_tags rtg ON rtg.tag_id=tg.id INNER JOIN resources r ON rtg.resource_id=r.id WHERE r.id=$res";
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
* Returns Resources by ResType
* * * * * * * * * * * * * * */
function getTypeResources($r_type){
    global $conn;
    global $res_row;
    // Get single subject topics
    $sql = "SELECT r.id,r.title,r.icon,r.info,r.multilink FROM resources r INNER JOIN resource_type rjt ON rjt.resource_id=r.id WHERE rjt.type_id=(SELECT rt.id FROM resourcetype rt WHERE rt.name=$r_type)";
    $result = mysqli_query($conn, $sql);
    $resources = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $res_row= mysqli_fetch_assoc($result);
//	return $resources;
    $final_resources = array();
    foreach ($resources as $resource) {
        $resource['link'] = getResourceLink($resource['id']);
        array_push($final_resources, $resource);
    }
    return $final_resources;
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

//Function to get the domain url name from a web link
function getDomainName($url){
    $host = parse_url($url, PHP_URL_HOST);
    if(!$host){$host=$url;}
    if(substr($host,0,4)=="www."){$host= substr($host, 4);}
    return $host;
}

//Group array by a key value
function array_group_by($data, $key) {
$result = array();

foreach($data as $val) {
if(array_key_exists($key, $val)){
$result[$val[$key]][] = $val;
}else{
$result[""][] = $val;
}
}

return $result;
}


/* * * * * * * * * * * *
*  RConvert number to word
* * * * * * * * * * * * */

function numberTowords($number) {
   
    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ' ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'Zero',
        1                   => 'One',
        2                   => 'Two',
        3                   => 'Three',
        4                   => 'Four',
        5                   => 'Five',
        6                   => 'Six',
        7                   => 'Seven',
        8                   => 'Eight',
        9                   => 'Nine',
        10                  => 'Ten',
        11                  => 'Eleven',
        12                  => 'Twelve',
        13                  => 'Thirteen',
        14                  => 'Fourteen',
        15                  => 'Fifteen',
        16                  => 'Sixteen',
        17                  => 'Seventeen',
        18                  => 'Eighteen',
        19                  => 'Nineteen',
        20                  => 'Twenty',
        30                  => 'Thirty',
        40                  => 'Fourty',
        50                  => 'Fifty',
        60                  => 'Sixty',
        70                  => 'Seventy',
        80                  => 'Eighty',
        90                  => 'Ninety',
        100                 => 'Hundred',
        1000                => 'Thousand',
        1000000             => 'Million',
        1000000000          => 'Billion',
        1000000000000       => 'Trillion',
        1000000000000000    => 'Quadrillion',
        1000000000000000000 => 'Quintillion'
    );
   
    if (!is_numeric($number)) {
        return false;
    }
   
    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'You can only process numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . numberTowords(abs($number));
    }
   
    $string = $fraction = null;
   
    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }
   
    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . numberTowords($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = numberTowords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= numberTowords($remainder);
            }
            break;
    }
   
    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }
   
    return $string;
}

// Function to shorten words if more than the allow string length.
function wordsCut($s, $s_max=0){
$string = strip_tags($s);
if (strlen($string) > $s_max) {
    // truncate string
    $string_short = substr($string, 0, $s_max);
    $endPoint = strrpos($string_short, ' ');

    //if the string doesn't contain any space then it will cut without word basis.
    $string = $endPoint? substr($string_short, 0, $endPoint) : substr($string_short, 0); // Ternary or Elvis Operator
    $string .= '...';
}
return $string;
}
