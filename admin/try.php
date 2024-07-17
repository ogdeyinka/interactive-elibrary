<?php 

session_start(); 
?>
<?php
include('../config.php');
include(ROOT_PATH . '/admin/includes/res_functions.php');
//tempfilesclr();
 // print_r($_SESSION);
print_r(getResourcesType(1166));

