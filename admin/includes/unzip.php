<?php

function show($str){
   echo $str . "<br/>\n";
   flush();
   ob_flush();
}

$archiveDir = "temp";

$files = array_diff(scandir($archiveDir), array('..', '.'));

show ( "Total files to be extracted : " . sizeof($files) );

$counter = 1;

foreach($files as $file) {

	$zip = new ZipArchive;
	$res = $zip->open($archiveDir . "/" . $file);
	if ($res === TRUE) {
		$zip->extractTo('./');
		$zip->close();
		show( "$counter : File $file extracted..");
	} else {
		show( "ERROR: Trouble extracting file : $file");
	}
	
	$counter++;
}

show ("Completed !")

?>