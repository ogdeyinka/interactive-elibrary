<?php
include('../config.php');
header("Access-Control-Allow-Origin:".BASE_URL);
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 1000");
header("Access-Control-Allow-Headers: x-requested-with, x-file-name, x-index, x-total, x-hash, Content-Type, origin, authorization, accept, client-security-token");
include(ROOT_PATH . '/admin/includes/res_functions.php');
   
$dir = realpath(RES_DIR);
$tempDir= realpath(UPLOAD_TEMP);
if (!isset($_SERVER['HTTP_X_FILE_NAME']))
    throw new Exception('Name required');
if (!isset($_SERVER['HTTP_X_INDEX']))
    throw new Exception('Index required');
if (!isset($_SERVER['HTTP_X_TOTAL']))
    throw new Exception('Total chunks required');

if(!preg_match('/^[0-9]+$/', $_SERVER['HTTP_X_INDEX']))
    throw new Exception('Index error');
if(!preg_match('/^[0-9]+$/', $_SERVER['HTTP_X_TOTAL']))
    throw new Exception('Total error');
 
$filename   = $_SERVER['HTTP_X_FILE_NAME'];
//$filesize   = $_SERVER['HTTP_X_FILE_SIZE'];
$index      = intval($_SERVER['HTTP_X_INDEX']);
$total      = intval($_SERVER['HTTP_X_TOTAL']);
$hash      = $_SERVER['HTTP_X_HASH'];
$ext = pathinfo(strtolower($filename), PATHINFO_EXTENSION);
// save the part to a file and extract the md5
$target = $tempDir."/".$filename."-".$index."-".$total;

$input = fopen("php://input", "r");
file_put_contents($target, $input);
$input = file_get_contents($target);
$hash_file = md5($input);

// if the hashes are the same then the upload was successful
if($hash===$hash_file)
{
	$result = array
	(
		'filename' => $filename,
		'start' => $index,
		'end' => $total,
		'percent' => intval(($index+1) * 100 / $total),
		'hash' => $hash_file
	);
	
	// 54/5000
// we will join the previous parts and put the new name
	if($index>0)
	{
		$target_old = $tempDir."/".$filename."-".($index-1)."-".$total;
		file_put_contents($target_old, $input, FILE_APPEND);
		rename($target_old, $target);	
	}

	if($index===intval($total-1))
	{
        $filename = filter_var(generateRandomString(), FILTER_SANITIZE_STRING);
        $filebname_ext = $filename.'.'. $ext;
        $result['percent'] = 100;
        if($ext!='zip'){
            if(!file_exists($dir."/videos/")){
                mkdir($dir."/videos/",0777);
            }
          if(rename($target,$dir."/videos/".$filebname_ext)){
          $result['targetcontent']= explode(' ', $filebname_ext);
          $result['targetdir']= RES_DIR_URL."videos";
          $result['filecontent']= $filebname_ext;
          $result['filedir']= htmlentities($dir."/videos/".$filebname_ext);
          tempfiledbsave($filebname_ext,$dir."/videos/");
          }
        //  $result['targetlink']=RES_DIR_URL.$filebname_ext;          
      }else{
          $zip = new ZipArchive;
          rename($target, $tempDir."/".$filebname_ext);
          $res = $zip->open($tempDir."/".$filebname_ext);
          if ($res === TRUE) {
           if($zip->extractTo($dir."/".$filename)){
            $zip->close();
           $finalDir = dir_filter($dir."/".$filename);
           deleteFileDir($tempDir."/".$filebname_ext);
            $result['filecontent']= $finalDir;
            $result['targetdir']= RES_DIR_URL.$finalDir;
            if(count(file_contains($dir."/".$finalDir))>=1){
            $result['targetcontent']= file_contains($dir."/".$finalDir);
            }
            $result['unzip'] = "success";
            tempfiledbsave($finalDir,$dir."/");
                } 
            } else {
            $result['unzip'] = "failed";
            }
            }
 	}
}
else
{
	$result = array
	(
		'error' => 'E_HASH'
	);
}
echo json_encode($result);

