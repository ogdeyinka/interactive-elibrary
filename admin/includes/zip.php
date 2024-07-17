<?php

/*
 *
 * This script will backup your web site by remotely archiving all files on the root FTP directory.
 * It will work even if your web server is memory limited buy splitting zips in several arhive files it they are too many files.
 * All zip files will be stored in a directory called temporary which must be writable.
 *
 * How to use it:
 * - Place the script at the root of your FTP.
 * - Call http://yoursite.com/zip.php
 * - In your FTP client go to temporary folder and download all backup_xxxxxx_x.zip files locally
 * - Unzip everything with this command: for i in *.zip; do unzip $i; done;
 * - Finally to avoid security issues, remove the script from the FTP.
 * 
 */

// increase script timeout value
ini_set('max_execution_time', 50000000);

class DirFilter extends RecursiveFilterIterator
{
    protected $exclude;
    public function __construct($iterator, array $exclude)
    {
        parent::__construct($iterator);
        $this->exclude = $exclude;
    }
    public function accept()
    {
        return !($this->isDir() && in_array($this->getFilename(), $this->exclude));
    }
    public function getChildren()
    {
        return new DirFilter($this->getInnerIterator()->getChildren(), $this->exclude);
    }
}

function show($str){
   echo $str . "<br/>\n";
   flush();
   ob_flush();
}

$date = getdate();
$splitNum = 0;

// Directory where zip files will be created
$archiveDir = "temp";
if (!file_exists($archiveDir)) {
    mkdir($archiveDir, 0777, true);
}
$archive = $archiveDir . "/backup_" . $date[0];
$currentArchive = $archive . "_" . $splitNum . ".zip";

// Excludes this directories for ziping
$dirsIgnore = array($archiveDir,"public_html");

$zip = new ZipArchive();
if ($zip->open($currentArchive, ZIPARCHIVE::CREATE) !== TRUE) {
    die ("Could not open archive");
}

$numFilesTotal = 0;
$iterator = new RecursiveIteratorIterator(new DirFilter(new RecursiveDirectoryIterator("./"),$dirsIgnore));
foreach ($iterator as $key=>$value){
   $numFilesTotal += 1;
}
show( "Will backup $numFilesTotal to $archive.zip" );

$iterator = new RecursiveIteratorIterator(new DirFilter(new RecursiveDirectoryIterator("./"),$dirsIgnore));
$numFiles = 0;
$counter = 0;
// Use file size to split archives
$maxFilePerArchive = 4000000;	// Just less than 4MB
foreach ($iterator as $key=>$value){
   
   if ((filesize(realpath($key))+$counter) >= $maxFilePerArchive) {
      $currentArchive = $archive . "_" . $splitNum++ . ".zip";
      show( "Splitting archive, new archive is $currentArchive" ); 
      $zip->close();
	  ob_flush();flush();
	  $zip = null;
      $zip = new ZipArchive();
      if ($zip->open($currentArchive, ZIPARCHIVE::CREATE) !== TRUE) {
          die ("Could not open archive");
      }
      $counter = 0;
   }
   
   if (! preg_match('/temporary\/backup_' . $date[0] . '/', $key)){
      $zip->addFile(realpath($key), $key) or die ("ERROR: Could not add file: $key");
      $numFiles += 1;
      if ($numFiles % 300 == 0) {
         show( number_format(($numFiles/$numFilesTotal)*100,0) . "% completed..");
      }
	  $counter += filesize(realpath($key));
   } else {
      show( "Not backuping this file -> $key" );
   }
}
// close and save archive
$zip->close();
show( "Archive created successfully with $numFiles files." );

?>
