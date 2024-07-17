<?php
$res_search_count=0;
// Remove unnecessary words from the search term and return them as an array
function filterSearchKeys($querys){
    require_once( ROOT_PATH . '/includes/stringInflector.php');
    $query = trim(preg_replace("/(\s+)+/", " ", $querys));
    $words = array();
    // expand this list with your words.
    $list = array("in","it","a","the","of","or","I","you","he","me","us","they","she","to","but","that","this","those","then","and","&","be","into","unto","their");
    foreach(explode(" ", $query) as $word){
        if (in_array($word, $list)){
            continue;
        }
      $singular_word = Inflector::singularize($word); // Convert plural word to singular
      $plural_word = Inflector::pluralize($word); // Convert plural word to singular
      if ($singular_word != $word) { 
        $words[] = $singular_word;
      }if($plural_word !=$word){
          $words[] = $plural_word;
      }
        $words[] = $word;
    }
    return array_map(function($val) { return $val.'*'; },array_unique($words));
}

function qKeywords($query){
   $kwds = filterSearchKeys($query);
if(is_array($kwds) && count($kwds)>=2){
 //   $kwds = array_unique($kwds)
$nolast=array_slice(array_unique($kwds),0,-1);
 $keywords= " are <b>".implode(", ", $nolast). "</b> and <b>". end($kwds)."</b>";
   }
 if(is_array($kwds) && count($kwds)==1){
$keywords= " is <b>". current($kwds)."</b>";
}
return $keywords;
}

// limit words number of characters
function limitChars($query, $limit = 100){
    return substr($query, 0,$limit);
}

function getResource_tag_query($q_array){
	global $conn;
        if (is_array($q_array) && !empty($q_array)){
   $query = implode(",", $q_array);
        if (!empty($query)) {
         $sql = "SELECT r.* FROM resources r LEFT JOIN resource_tags rtg ON rtg.resource_id = r.id LEFT JOIN tags tg ON tg.id = rtg.tag_id WHERE MATCH (tg.tag) AGAINST ('".$query."' IN BOOLEAN MODE)";
        }
	$result = mysqli_query($conn, $sql);
        if($result){
	return mysqli_fetch_all($result, MYSQLI_ASSOC);  
}
}
}

function getResourceQuery($q_array){
	global $conn;
        if (is_array($q_array) && !empty($q_array)){
   $query = implode(",", $q_array);
        if (!empty($query)) {
         $sql = "SELECT * FROM resources WHERE MATCH(`title`) AGAINST ('".$query."' IN BOOLEAN MODE)";
        // $sql="MATCH(`title`) AGAINST ('".$query."' IN BOOLEAN MODE)";
        // $sql = "SELECT *, $sql as score FROM resources WHERE $sql>0 ORDER BY score DESC;";
        }
	$result = mysqli_query($conn, $sql);
        if($result){
	return mysqli_fetch_all($result, MYSQLI_ASSOC);  
}
}
}

function ResourceSearch($squery){
	global $res_search_count;
        $ssquery = strip_tags(limitChars($squery)); // strip tags against system abuse and limit the numbers of words to process
        $searchArray = filterSearchKeys($ssquery);
        $res_tag = getResource_tag_query($searchArray); // Get resources by tags query
        $res_dir = getResourceQuery($searchArray); // Get resources by resource's title query
        $final_resources = array();
        if($res_tag || $res_dir ){
       $resources = array_unique(array_merge($res_dir,$res_tag), SORT_REGULAR); //Merge the resourses from tag and title
    //    $res_search_count = count($resources);
    foreach ($resources as $resource) {
        $resource['link'] = getResourceLink($resource['id']);
        $resource ['resourcetype'] = getResourcesType($resource['id']);
        $resource['topics'] = getResourceTopics($resource['id']);
        array_push($final_resources, $resource);
    }
 //   $count_link=0;
    foreach ($final_resources as $res){
         $res_search_count+= count($res['link']);
    }
    return $final_resources;
        }else{ $final_resources=0;return $final_resources;}
}

/*
function getResource_tag_query($squery){
	global $conn, $res_search_count;
        $ssquery = strip_tags(limitChars($squery)); // strip tags against system abuse and limit the numbers of words to process
        $searchArray = filterSearchKeys($ssquery);
      //  $searchArray = explode(" ", strip_tags($ssquery));
        $query = "";
        foreach($searchArray as $val) {
        $search = esc($val);
        $search.="*";
        if (!empty($query)) {
        $query = $query . " OR "; // or AND, depends on what you want
        }
    //   $query = $query . "`title` LIKE '%{$search}%'";
         $query = $query . "MATCH(`title`) AGAINST ('".$search."' IN BOOLEAN MODE)";
        } 
        if (!empty($query)) {
         $sql = "SELECT * FROM resources WHERE $query";
        }
//$sql = "SELECT r.* FROM resources r WHERE r.title LIKE '%". $qquery ."%' ";
     //   $sql = 'SELECT * FROM resources WHERE MATCH(title) AGAINST ("'.$query.'" IN BOOLEAN MODE)';
	$result = mysqli_query($conn, $sql);
        $final_resources = array();
        if($result){
	$res_search_count = mysqli_num_rows($result);
	$resources = mysqli_fetch_all($result, MYSQLI_ASSOC);
    foreach ($resources as $resource) {
        $resource['link'] = getResourceLink($resource['id']);
        $resource ['resourcetype'] = getResourcesType($resource['id']);
        $resource['topics'] = getResourceTopics($resource['id']);
        array_push($final_resources, $resource);
    }
    return $final_resources;
        }else{
            $final_resources=0;
          return $final_resources;  
        }
}
*/
?>

<?php if (!isset($query)): ?>
    <style type="text/css">
        #res-search {
          /*  margin-top: 20vh !important; */
        }
    </style>
<?php endif ?> 
