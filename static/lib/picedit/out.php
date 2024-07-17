<?php
$errors = [];

function findexts ($filename)
{
$filename = strtolower($filename) ;
$exts = split("[/\\.]", $filename) ;
$n = count($exts)-1;
$exts = $exts[$n];
return $exts;
}
function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

echo  generateRandomString();
//This applies the function to our file
//$ext = findexts ($_FILES['uploaded']['name']) ; 
if (isset($_POST['submit'])) {
$r_iconname = $_FILES['thefile']['name'];
//if (empty($r_title)) { array_push($errors, "Resource title is required"); }
if (empty($r_iconname)) {array_push($errors, "Icon image is required"); } 
if (count($errors) == 0) {
echo "** Post Array **\n";
print_r($_POST);
echo "** Files Array **\n";
print_r($_FILES);
//if(is_array($_FILES)) {
if(is_uploaded_file($_FILES['thefile']['tmp_name'])) {
$ext = findexts ($r_iconname) ; 
$sourcePath = $_FILES['thefile']['tmp_name'];
$filename = generateRandomString().'.'. $ext;
// rename($filename, $filename .'.'. $ext);
$targetPath = 'img/'.$filename;
if(move_uploaded_file($sourcePath,$targetPath)) {
	unlink($filename);
	echo $filename;
	echo $ext;
}
}
}else{array_push($errors, "Icon image fails to upload"); }
//}
}
print_r($errors);
    
    if (count($errors) > 0) {
        foreach ($errors as $error) {
           echo json_encode($error);
        }
    }

?>