<!doctype html>
<html>

<head>
<?php
$errors = [];

function findexts ($filenameext)
{
$filename = strtolower($filenameext) ;
$exts = split("[/\\.]", $filename) ;
$n = count($exts)-1;
$ext = $exts[$n];
return $ext;
}
function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

// echo  generateRandomString();
//This applies the function to our file
//$ext = findexts ($_FILES['uploaded']['name']) ; 
if (isset($_POST['submit']) ) {
$r_iconname = $_FILES['thefile']['name'];
$url = $_POST['url'];
$url= parse_url($url, PHP_URL_PATH);
// if (empty($r_title)) { array_push($errors, "Resource title is required"); }
if (empty($r_iconname)) {array_push($errors, "Icon image is required"); } 
if (count($errors) == 0) { /*
echo "** Post Array **\n";
print_r($_POST);
echo "** Files Array **\n";
print_r($_FILES); */
//if(is_array($_FILES)) {
//if(is_uploaded_file($_FILES['thefile']['tmp_name'])) {
$ext = findexts ($url) ; 
 $urlext = pathinfo($url, PATHINFO_EXTENSION);
$sourcePath = $_FILES['thefile']['tmp_name'];
$filename = generateRandomString().'.'. $ext;
// rename($filename, $filename .'.'. $ext);
$targetPath = 'img/'.$filename;
//if(move_uploaded_file($sourcePath,$targetPath)) {
//	unlink($filename);
//	header('Content-Type: application/json');
        echo json_encode(array('generate filename' =>$filename, '  extension' => $ext, '  original filename' =>$r_iconname, 'url'=> $url, 'url extention'=> $urlext) );
	exit();
	echo $filename;
	echo $ext;
//}

}else{array_push($errors, "Icon image fails to upload"); }
//}
}
print_r($errors);
    
    if (count($errors) > 0) {
        foreach ($errors as $error) {
         header("Content-Type","application/jason");
           echo json_encode($error);
        }
    }

?>
<meta charset="UTF-8">
<title>Untitled Document</title>

<link rel="stylesheet" type="text/css" href="css/picedit.min.css" />

</head>
<body>

<div style="margin:10% auto 0 auto; display: table;">

<form action="index.php" method="post" enctype="multipart/form-data" id="thefile_form">
<?php //if (count($errors) > 0) : ?>
  <div class="message error validation_errors" >
  	<?php //foreach ($errors as $error) : ?>
  	  <p><?php // echo json_encode($error); ?></p>
  	<?php// endforeach ?>
  </div>
<?php // endif ?>
<div id="msgdisplay"></div>
<input type="text" name="r_title" value="" placeholder="Title">
<input type="text" name="url" value="" placeholder="Url">
<input type="file" name="thefile" id="thebox">
<input type="hidden" name="submit" value="1">
	<div style="margin-top:30px; text-align: center;">
		<button type="submit" name="submit" id="submit_btn">Submit</button>
    </div>
</form>

</div>

<script type="text/javascript" src="http://localhost/interactive/files/js/jquery.min.js"></script>
<script type="text/javascript" src="js/picedit.js"></script>
<script type="text/javascript">
	$(function() {
		$('#thebox2').picEdit({
imageUpdated: function(img){},
formSubmitted: function(res){ 
/*
	 function clearCanvas(){
							var canvas = $(document).find(".picedit_canvas > canvas")[0];
	    					var ctx = canvas.getContext("2d");
							ctx.clearRect(0, 0, canvas.width, canvas.height);
							}
						    clearCanvas();
						    */
// alert(console.log(res));
/*
	alert(res.responseText);
	$('#msgdisplay').html(res.responseText);
	var len = res.length;
    $("#msgdisplay").empty();
     for( var i = 0; i<len; i++){
	    var error = res[i][response];
	    alert(error);
       $("#msgdisplay").append('<span>' +error+ '</span>');
       }  */
},
fileNameChanged: function(filename){},
fileLoaded: function(file){},
redirectUrl: true
            });
	});
/*
var allowed_file_size 	= "1048576"; //1 MB allowed file size
var allowed_file_types 	= [ 'image/png', 'image/gif', 'image/jpeg', 'image/pjpeg']; //Allowed file types
var border_color 		= "#C2C2C2"; //initial input border color
var maximum_files 		= 1; //Maximum number of files allowed

$("#thefile_form").submit(function(e){
    e.preventDefault(); //prevent default action 
	proceed = true;
	
	//simple input validation
	$($(this).find("input[data-required=true], textarea[data-required=true]")).each(function(){
            if(!$.trim($(this).val())){ //if this field is empty 
                $(this).css('border-color','red'); //change border color to red   
                proceed = false; //set do not proceed flag
            }
            //check invalid email
            var email_reg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/; 
            if($(this).attr("type")=="email" && !email_reg.test($.trim($(this).val()))){
                $(this).css('border-color','red'); //change border color to red   
                proceed = false; //set do not proceed flag              
            }   
	}).on("input", function(){ //change border color to original
		 $(this).css('border-color', border_color);
	});
	
	//check file size and type before upload, works in all modern browsers
	if(window.File && window.FileReader && window.FileList && window.Blob){
		var total_files_size = 0;
		if(this.elements['thefile'].files.length > maximum_files){
            alert( "Can not select more than "+maximum_files+" file(s)");
            proceed = false;			
		}
		$(this.elements['thefile'].files).each(function(i, ifile){
			if(ifile.value !== ""){ //continue only if file(s) are selected
                if(allowed_file_types.indexOf(ifile.type) === -1){ //check unsupported file
                    alert( ifile.name + " is not allowed!");
                    proceed = false;
                }
             total_files_size = total_files_size + ifile.size; //add file size to total size
			}
		}); 
       if(total_files_size > allowed_file_size){ 
            alert( "Make sure total file size is less than 1 MB!");
            proceed = false;
        }
	}
	
	var post_url = $(this).attr("action"); //get form action url
	var request_method = $(this).attr("method"); //get form GET/POST method
	var form_data = new FormData(this); //Creates new FormData object
/*	
	//if everything's ok, continue with Ajax form submit
	if(proceed){ 
		$.ajax({ //ajax form submit
			url : post_url,
			type: request_method,
			data : form_data,
			dataType : "json",
			contentType: false,
			cache: false,
			processData:false
		}).done(function(res){ //fetch server "json" messages when done
			if(res.type == "error"){
				$("#msgdisplay").html('<div class="error">'+ res.text +"</div>");
			}
			if(res.type == "done"){
				$("#msgdisplay").html('<div class="success">'+ res.text +"</div>");
			}
		});
	}
});
*/
</script>

</body>
</html>
