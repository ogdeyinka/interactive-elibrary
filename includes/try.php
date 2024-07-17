<?php
include_once 'stringInflector.php';
$query = "add body leaf wife wolf";
$words = array();
foreach(explode(" ", $query)  as $word){
   /*     if (in_array($word, $list)){
            continue;
        } */
    $c = 0;
      $singular_word = Inflector::singularize($word); // Convert plural word to singular
      $plural_word = Inflector::pluralize($word); // Convert plural word to singular
      if ($singular_word != $word) { 
        $words[] = $singular_word;
      }if($plural_word !=$word){
          $words[] = $plural_word;
      }
        $words[] = $word; 
    }
  //  array_map(function($val) { return $val+1; }, $a);
    print_r(array_map(function($val) { return $val.'*'; },array_unique($words)));
    ?>
