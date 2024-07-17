<?php
//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
?>
       <script id="r_link_repeatable" type="text/template">
            <div class="repeatable-container" draggable="true">
            <table style="width: 100%">
            <tr>
            <td style="width: 75%">
            <input type="url" name="link_url[]" class="link_urls" placeholder="* Enter Resource URL" required oninvalid="this.setCustomValidity('Enter a valid url')" oninput="this.setCustomValidity('')"/>
            <input type="text" name="link_name[]" placeholder="* Enter URL Name (Not more than 20 Characters)" maxlength="20" /> 
            <label for="link_modalview[]">Open Link in Modal view? <input type="checkbox" value="1" checked name="link_modalview[]"/></label>
            </td>
            <td style="width: 25%">
            <input type="button" value="Delete this Link" class="r_link_delete" />
            </td>
            </tr>
            </table>
            </div>
        </script>
        <script type="text/javascript">
           $('document').ready(function(){
//Functions to disable & enable button
  function buttonEnabled(button_name){
       $('button[name="'+button_name+'"]').removeClass('buttonDisable'); 
            }
     function buttonDisabled(button_name){
          $('button[name="'+button_name+'"]').addClass('buttonDisable');
      }
  
function isUrl(s) {
   var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
   return regexp.test(s);
}
  
       // Function for generic input checking. 
        var inputs_empty = [];
function inputCheck (input,scrollup=0,affected=0){
              if($(input).attr('type')=='checkbox'){var inputs = $(input+":checked").length< 1} else if($(input).attr('type')=='url'){var inputs =$(input).val().length==0 || !isUrl($(input).val())} else {var inputs =$(input).val().length==0}	// Conditions inputs.    
                    if(inputs) {
               //    event.preventDefault();
               inputs_empty.push(1);
                     <?php if($page=="editResource"):?>
                     buttonEnabled("update_resource");
                     <?php endif ?>
                     <?php if ($page== "addResource"): ?>
                    buttonEnabled("create_resource");
                    <?php endif ?>
                      if(scrollup !=0) {$('html, body').animate({ scrollTop: $(scrollup).offset().top }, 1);}
                    if(affected !=0) {$(affected).addClass('required-border');}
                   }else{if($(affected).hasClass('required-border')){$(affected).removeClass('required-border');} }
             }
             
             
  <?php if ($page== "addResource"): ?>
 // Function to check if a link is already saved in the database
  $('.link-container').on('focusin', function() {
      $('[name="link_url[]"]').off('blur'); 
    $('[name="link_url[]"]').on('blur', function(){ 
        $this = $(this); 
        if($this.val().length >0){
           $.ajax({
             url: '<?php echo BASE_URL . '/admin/remotajax.php'?>',
             method: 'POST',
             data: {'linkcheck': $this.val()}
          }).done(function (res) { 
          if(res['msg']==='yes'){
                $.confirm({
                    boxWidth: '400px',
                    useBootstrap: false,
                //    autoClose: 'No|8000',
                  title: '',
                    content: '<span style="font-size: 20px;">This link address is already attached to a resource! </span>',
                     buttons: {
                            Close: {
                                 text: 'Close & Try Another Link',
                                 btnClass: 'btn-red',
                                 action: function(){$this.val("");}
                              },
                               Resource: {
                                text: 'Go to the Resource', // With spaces and symbols
                                btnClass: 'btn-blue',
                                action: function () {
                                  var  url = "<?php echo BASE_URL . '/admin/edit.php?resource='?>" + res['res_id'] +"<?php echo '&editok='.md5(rand(1000, 99999)); ?>";
                                        window.open(url,'popupwindow','width=1000, height=600, scrollbars, resizable');
                                        $this.val("");
                                    }
                                }
                            }
                            });
                            }
                });
                }
        });
        });
<?php endif ?>
function inputsCheck(){
        inputCheck ("#ddtopic","#rTopicSub > .token-input-list","#rTopicSub > .token-input-list");  //Function to check if at least a resource topic is added.
        inputCheck ("[name='rlevels[]']","#rLevel","#rLevel");  //Function to check if at least a resource level checkbox is checked
        inputCheck ("[name='restypes[]']","#rTypeGroup","#rTypeGroup");  //Function to check if at least a resource type checkbox is checked.
       $("[name='link_url[]']").each(function(){ var link_url = $(this); inputCheck (link_url,link_url,link_url);});  //Function to check if at least a resource link is not empty.
        inputCheck ("#r_tags","#rdata","#rdata > .token-input-list");  //Function to check if at least a resource tag is added.
        inputCheck ("input[name='r_title']","input[name='r_title']","input[name='r_title']");  //Function to check if at least a resource topic is added.
        inputCheck ("#r_img","fieldset[name='rImage']","#r_icon");   //Function to check if resource image is attached.           
}

<?php if($page=="editResource"):?>
        $("button[name='update_resource']").on('click', function(){     // When the button named update_resource is clicked
        buttonDisabled("update_resource");
<?php endif ?>
<?php if ($page== "addResource"): ?>
         $("button[name='create_resource']").on('click', function(){     // When the button named create_resource is clicked
         buttonDisabled("create_resource");
 <?php endif ?>
         inputs_empty.length=0;
        inputsCheck();
        console.log(inputs_empty);
        if(inputs_empty.length==0){return true;}else{return false;}
        });

   //   Function to prevent closure of windows when there is ongoing form filling                 
                var isSubmitting =false;
                $('form').submit(function(){
                    isSubmitting = true;
                });
                $('form').data('initial-state',$('form').serialize());
                $(window).on('beforeunload',function(){
                    if(!isSubmitting && $('form').serialize() !== $('form').data('initial-state')){
                        return "You have unsaved changes which will not be saved.";
                    }
                });
// Function to clear unsaved data
/*
if(($("span#res-file-uploaded").text().length>1 || $("#resfile-done").val().length>1 || $("r_img").val().length>1) && !isSubmitting){
var inputs_del = [];
if($("span#res-file-uploaded").text().length>1){inputs_del.push($("span#res-file-uploaded").text());}
if($("#resfile-done").val().length>1){inputs_del.push($("#resfile-done").val());}
if($("#r_img").val().length>1){inputs_del.push($("#r_img").val());}
const data = JSON.stringify({inputs_delclr:inputs_del});
var url = '<?php echo BASE_URL . '/admin/remotajax.php'?>';
 $(window).on('unload'function(){
    navigator.sendBeacon(url,data);
});
}
*/
                // Function to display loading when there is ajax loading
            $('#loading')
            .hide()
            .ajaxStart(function() {
                $(this).show();
            })
            .ajaxStop(function() {
                $(this).hide();
            });
            
            $('.message').delay(2000).fadeOut();  // Function to auto hide popup session message div after display. 
     
     <?php unsetSession('resImageUploaded');?>
          <?php require_once(ROOT_PATH . '/admin/includes/delekey.php'); ?> 
              
 //Function to clean up uploaded files that are no longer needed
      function delUploadedfile(){ 
        var resfile = $.trim($("span#res-file-uploaded").text());  // get the name of the image file if one already existing so as to delete
        var delekey = $("#delekey").val();
        $.ajax({
        method: 'POST',
        url: '<?php echo BASE_URL . '/admin/remotajax.php'?>',
        data: {
        'delekey': delekey,
        'resfiletodel': resfile
        }
        });    
        } 
        
// Function to handle the uploading of files such as swf,mp4,zip using confirm jquery plugin  
    $('#r_file').confirm({ 
            boxWidth: '50%',
            useBootstrap: false,
            title: 'Upload Resource File!',
            content: '' +
            '<form id="rFilesUpload" method="POST" style="width:80%" enctype="multipart/form-data" action="">' +
            '<div class="input-group" style="width:90%"><label class="input-group-btn"><span class="btn btn-primary">'+
            '<span id="text-browse">Browse&hellip;</span> <input id="rFileInput" type="file" name="file" data-name="" data-end="" style="display:none;" accept=".zip,.mp4,.swf">' +
            '</span></label><span><input type="text" class="form-control" readonly="" style="height:32px;margin-top:0;padding:0;float:none;">'+
            '<div id="progress-bar-outer">'+
            '<div id="progress-bar" class="progress-bar" style="height:32px;position:relative;margin:-33px 1px;z-index:5;"></div></div></span>'+
            '</div><div id="resource-files-link"></div><div style="color:red" id="res-files-warning"></div><span id="res-file-uploaded" style="display:none;"></span>'+
            '<input type="submit" id="rFileUploadSubmit" value="Submit" style="display:none;" class="btnSubmit" />'+
            '</form>',
            onOpenBefore: function () {
                // We can attach the `fileselect` event to all file inputs on the page
                $("#rFilesUpload").on('change', ':file', function () {
               setdelekey('resfile_del'); 
                    var input = $(this),
                            numFiles = input.get(0).files ? input.get(0).files.length : 1,
                            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
                    input.trigger('fileselect', [numFiles, label]);
                     $('div#progress-bar').width("");
                     $('.btn-file-upload').removeAttr("disabled");
                });
                // We can watch for our custom `fileselect` event like this
                    $(':file').on('fileselect', function (event, numFiles, label) {
                        var input = $(this).parents('.input-group').find(':text'),
                                log = numFiles > 1 ? numFiles + ' files selected' : label;
                        if (input.length) {
                            input.val(log);
                        } else {
                            if (log)
                                alert(log);
                        }
                    }); 
                   },
      onOpen:function (){
          if($('#resfile-done').length>0 && $('#resfile-done').val().length>1){
          $("span#res-file-uploaded").text($('#resfile-done').val());    
        }
         function fileLinkChecked(){ // function to enable or disable insert button when the link checkbox(es) is ticked or unticked
           $("#resource-files-link input[name='filelinks[]']").change(function(){
                      if($("#resource-files-link input[name='filelinks[]']:checked").length>0){
                        $('.btn-link-insert').removeAttr("disabled");  
                      }else{
                        $('.btn-link-insert').prop("disabled", true);
                      }
                    });
                }
         function _$_(id) {return document.getElementById(id);}
          var form=0;
          var index = 0;
          var data_name;
          if(_$_('rFileInput').getAttribute('type') == 'file'){
          var BYTES_PER_CHUNK = 1024 * 1024 * 0.5;
          var _files_data = [];      
          var form = _$_('rFilesUpload');
            var inputs = _$_('rFileInput');
            for (i in inputs)
            {
                if (typeof inputs == 'object')
                {
                   // if (inputs.getAttribute('type') == 'file'){
                        var file_input = inputs;
                        break;
                  //  }
                }
            }
            form.reset();
            form.onsubmit = function (e)
            {
                e.preventDefault();
                sendRequest(file_input, this);
            };
            file_input.onchange = function (e)
            {
                var target = e.target ? e.target : e.srcElement;
                var files = target.files;
                for (var i in files)
                {
                    if (typeof files[i] == 'object')
                    {
                        var found = false;
                        var lis = _$_('rFileInput');
                        lis.setAttribute('data-name', files[i]['name']);
                        lis.setAttribute('data-end', getSlicesCount(files[i]));
                        if (typeof lis.getAttribute == 'function')
                        {
                            data_name = lis.getAttribute('data-name');
                            console.log("data_name:: "+data_name);
                            Data_name = data_name;
                            var slices = getSlicesCount(files[i]);
                            if (data_name == files[i]['name'] && slices == lis.getAttribute('data-end'))
                            {
                                var tmp =
                                        {
                                            'data-start': lis.getAttribute('data-start'),
                                            'data-end': lis.getAttribute('data-end')
                                        };
                                _files_data = tmp;

                                found = true;
                            }
                        }
                        if (found === false)
                        {
                            var tmp =
                                    {
                                        'data-start': 0,
                                        'data-end': getSlicesCount(files[i])
                                    };
                            _files_data = tmp;
                        }
                    }
                }
            }; 
            function getSlicesCount(blob){
            var slices = Math.ceil(blob.size / BYTES_PER_CHUNK);
            return slices;
                 }
            function sendRequest(input){
            var blobs = input.files;
            async(blobs, 0, blobs.length);
                }
            function async(blobs, i, length) {
            if (i >= length)
            {
                form.reset();
                _files_data = [];
                return false;
            }

            var index = _files_data['data-start'];
            if (typeof index === 'undefined') {
                index = 0;
                //console.log("index:: " + index);
            }
            if (index > 0)
                index++;

            var start = 0;

            for (var j = 0; j < index; j++)
            {
                var start = start + BYTES_PER_CHUNK;
                if (start > blobs[i].size)
                    start = blobs[i].size;
            }

            uploadFile(blobs[i], index, start, _files_data['data-end'], function ()
            {
                i++;
                async(blobs, i, length);
            });
                 }
                 var str2ab_blobreader = function (str, callback)
        {
            var blob;
            var BlobBuilder = window.MozBlobBuilder || window.WebKitBlobBuilder || window.BlobBuilder;
            if (typeof (BlobBuilder) !== 'undefined')
            {
                var bb = new BlobBuilder();
                bb.append(str);
                blob = bb.getBlob();
            } else
                blob = new Blob([str]);

            var f = new FileReader();
            f.onload = function (e)
            {
                var target = e.target ? e.target : e.srcElement;
                callback(target.result);
            };
            f.readAsArrayBuffer(blob);
        };
        function uploadFile(blob, index, start, slicesTotal, callback){
            if (typeof blob == 'undefined') {
                console.log('stop');
                return;
            }

            if (typeof index == 'undefined') {
                console.log("index:: " + index);
                index = 0;
                console.log("index:: " + index);
            }
            var end = start + BYTES_PER_CHUNK;
            if (end > blob.size)
                end = blob.size;

            getChunk(blob, start, end, function (zati)
            {
                // hash md5
                var reader = new FileReader();
                reader.onload = function (e)
                {
                    var target = e.target ? e.target : e.srcElement;

                    var binary = "";
                    var bytes = new Uint8Array(target.result);
                    var length = bytes.byteLength;
                    for (var i = 0; i < length; i++)
                        binary += String.fromCharCode(bytes[i]);

                    var hash = md5(binary);
                    binary = undefined;

                    var xhr = new XMLHttpRequest();
                    xhr.onreadystatechange = function ()
                    {
                        if (xhr.readyState == 4)
                        {
                            console.log(xhr.response);
                            var j = JSON.parse(xhr.response);

                            if (typeof j['error'] !== undefined && j['error'] === 'E_HASH')
                            {
                                window.setTimeout(function ()
                                {
                                    uploadFile(blob, index, start, slicesTotal, callback);
                                }, 100);
                            } else
                            {
                                if (data_name == j['filename'])
                                {
                                    var progress_bar = _$_('progress-bar');
                                    progress_bar.style.width = j['percent'] + "%";
                                    
                                    if (j['percent'] == 100)
                                    {
                                        _$_('text-browse').textContent='Upload Done!';
                                        _$_('res-file-uploaded').textContent = j['filecontent'];
                                        _$_('rFileInput').removeAttribute("disabled");
                                        document.getElementsByClassName("btn-link-cancel")[0].removeAttribute("disabled");
                                        var i;
                                        var files = j['targetcontent'];
                                        var link = j['targetdir']+"/";
                                        if(typeof files != 'undefined'){
                                        for(i=0; i<files.length; ++i){                                
                                           _$_('resource-files-link').innerHTML += '<div><input type="checkbox" name="filelinks[]" value="'+link+files[i]+'"/><a href="'+link+files[i]+'" target="_blank">'+link+files[i]+'</a></div>';     
                                        }
                                        }else{
                                         setTimeout(function () {
                                        _$_('text-browse').textContent='Browse...';
                                        }, 1000);
                                        _$_('res-files-warning').innerHTML ='Sorry, there is an error with your uploaded file!<br>Kindly ensure your zip file contains valid files.';
                                        delUploadedfile();
                                        }
                                        fileLinkChecked();
                                        // CODE ON FINISH
                                        console.log('Finished');
                                       // _$_('rtargetdir').value = j['filecontent'];
                                     // 	hideSub();
                                     //   zipOptions(data_name);
                                    }
                                }
                                index++;
                                if (index < slicesTotal)
                                {
                                    window.setTimeout(function ()
                                    {
                                        uploadFile(blob, index, end, slicesTotal, callback);
                                    }, 100);
                                } else
                                    callback();
                            }
                        }
                    };

                    if (typeof index == 'undefined' || index == null) {
                        console.log("index:: " + index);
                        index = 0;
                        console.log("index:: " + index);
                    }
                    
                    xhr.open("post", "<?php echo BASE_URL . '/admin/fileupload.php'?>", true);
                    xhr.setRequestHeader("X-File-Name", blob.name);
                    xhr.setRequestHeader("X-Index", index);
                    xhr.setRequestHeader("X-Total", slicesTotal);
                    xhr.setRequestHeader("X-Hash", hash);
                    xhr.send(zati);
                };
                reader.readAsArrayBuffer(zati);
            });
        }
        function getChunk(blob, start, end, callback)
        {
            var chunk;

            if (blob.webkitSlice)
                chunk = blob.webkitSlice(start, end);
            else if (blob.mozSlice)
                chunk = blob.mozSlice(start, end);
            else
                chunk = blob.slice(start, end);

            // android default browser in version 4.0.4 has webkitSlice instead of slice()
            if (blob.webkitSlice)
            {
                str2ab_blobreader(chunk, function (buf)
                {
                    callback(buf);
                });
            } else
                callback(chunk);
        }
                        }
                   },
                    buttons: {
                    fileUploadSubmit: {
                      text: 'Upload',
                      btnClass: 'btn-blue btn-file-upload',
                      isDisabled: true,
                      action: function () {
                       if($('#rFileInput').val()){  // If the input is filled that is when it should work
                            if($("span#res-file-uploaded").text().length>1 && $("#delekey").val().length>1) { // check if there is an existing image #rimageShow for the resource
                            delUploadedfile();
                            if($('#resfile-done').length>0){
                             $(".link-container").html("");
                          $(".link-container").append(''+
                           '<div class="repeatable-container" draggable="true">'+
                           '<table style="width: 100%"><tr><td style="width: 75%">'+
                           '<input type="url" name="link_url[]" class="link_urls"/>'+
                           '<input type="text" name="link_name[]" placeholder="* Enter URL Name (Not more than 20 Characters)" maxlength="20" />'+
                           '<label for="link_modalview[]">Open Link in Modal view? <input type="checkbox" value="1" checked name="link_modalview[]"/></label>'+
                           '</td><td style="width: 25%"><input type="button" value="Delete this Link" class="r_link_delete"/>'+
                           '</td></tr></table></div>'+''
                            );
                            }
                            } 
                       $('#rFileUploadSubmit').trigger('click');
                       $("div#res-files-warning").html("");
                       $('div#resource-files-link').html("");
                       $('span#text-browse').text('Uploading...');
                       this.$$fileUploadSubmit.prop('disabled', true);
                       this.$$fileuploadcancel.prop('disabled', true);
                       $("#rFileInput").prop('disabled', true);
                       return false;
                     }
                     } 
                    },
                    linksInsert:{
                      text: 'Insert Link(s)',
                      btnClass: 'btn-blue btn-link-insert',
                      isDisabled: true,
                      action: function () {
                        if(!$('div#resource-files-link').is(':empty')){
                       if($("div#resource-files-link input:checked").length>0){
                           $(".link-container").html("");
                           $(".link-container").html('<input type="hidden" name="fileupload" id="resfile-done" readonly="" value="'+$.trim($("span#res-file-uploaded").text())+'"/>');
                           var i;
                          var filelinks = $("#resource-files-link input[name='filelinks[]']:checked").map(function(){return $(this).val();}).get();
                            for (i = 0; i < filelinks.length; ++i) {
                           $(".link-container").append(''+
                           '<div class="repeatable-container" draggable="true">'+
                           '<table style="width: 100%"><tr><td style="width: 75%">'+
                           '<input type="url" name="link_url[]" class="link_urls" readonly="" value="'+$.trim(filelinks[i])+'"/>'+
                           '<input type="text" name="link_name[]" placeholder="* Enter URL Name (Not more than 20 Characters)" maxlength="20" />'+
                           '<label for="link_modalview[]">Open Link in Modal view? <input type="checkbox" value="1" checked name="link_modalview[]"/></label>'+
                           '</td><td style="width:25%;padding-top:10px" valign="top"><a class="various" href="'+$.trim(filelinks[i])+'" target="_blank">Preview this link</a><input type="button" value="Delete this Link" class="r_link_delete" disabled=""/>'+
                           '</td></tr></table></div>'+''
                            );
                            }
                            delekey_reset('resfile_del');
                           return true;
                            }else{
                           $("div#res-files-warning").html("Kindly select atleast a link above!")
                           return false;
                       }
                     }else{return false;}  
                      }
                    },
        fileuploadcancel:{
              text: 'Cancel',
              btnClass: 'btn-default btn-link-cancel',
              action: function () {
              if($("#delekey").length>0){
                    if($("span#res-file-uploaded").text().length>1 && $("#delekey").val().length>1) { // check if there is an existing image #rimageShow for the resource
                            delUploadedfile();
                     }
                    delekey_reset('resfile_del');
                    }
                    }
                    }
                    }
        });
           // Function to handle the uploading of image icons using confirm jquery plugin
        $('#r_icon').confirm({  // Using Jquery confirm plugin.
                    boxWidth: '50%',
                    useBootstrap: false,
                    title: 'Add Resource Icon Image!',
                    content: '' +
                    '<form name="r_imgedit" method="POST" style="width:auto" enctype="multipart/form-data" action="<?php echo BASE_URL . '/admin/remotajax.php'?>">' +
                    '<input type="file" name="r_image" id="r_image" placeholder="Upload Resource Preview Image Icon" >' + '<input type="hidden" name="imagefileupload" value="1">' + '<button type="submit" style="display: none;" class="btn" id="save_image">Save Image</button>' +
                    '</form>',
                    onOpenBefore: function () {
                    <?php if($page=="editResource"):?>
                    if ($("#editedoutResImg").length===0){
                    $("fieldset[name='rImage']").append('<input name="editedoutResImg" id="editedoutResImg" type="text" value=\''+ $("#r_img").val() +'\' style="display: none" />');
                    }
                    <?php endif ?>
                    if ($("#rimageShow").has("img") && $("#r_img").val()) {setdelekey('res_icon_del');} 
                    $('#r_image').picEdit({   // The start of picEdit plugin
                    imageUpdated: function () {},
                    formSubmitted: function (res) { 
                          //  var imagedone = $("#imagedone").val();
                            if ($("#rimageShow").has("img") && $("#delekey").val()) { // check if there is an existing image #rimageShow for the resource
                            var r_img = $("#r_img").val();  // get the name of the image file if one already existing so as to delete
                            var delekey = $("#delekey").val();
                            $.ajax({
                                    method: 'POST',
                                    url: '<?php echo BASE_URL . '/admin/remotajax.php'?>',
                                    data: {
                                    'delekey': delekey,
                                    'iconfiletodel': r_img
                                    }
                            });
                            }
                            $("#rimageShow").empty();
                            <?php if($page=="editResource"):?>
                            $('#r_img').removeAttr('value');
                            <?php endif ?>
                            $('#r_img').val("");
                            if (res.readyState === 4 && res.status === 200) {  // If the ajax is successful
                               if(res.responseText){ 
                            var res = JSON.parse(res.responseText);
                            if (res['filename']) {
                            var filename = res['filename'];
                            var imgDir = "<?php echo IMAGE_URL; ?>" + filename;
                            $('#r_img').val(filename);
                            var imgr = $("<img/>").attr("src", imgDir)
                                    .on('load', function () {
                                    if (!this.complete || typeof this.naturalWidth === "undefined" || this.naturalWidth === 0) {
                                    $.alert("the image failed to load");
                                    } else {
                                    $("#rimageShow").append(imgr);
                                    $("#r_icon").val("Change Resource Image Icon")
                                    }
                                    });
                            } else if (res['status']) {
                            var status = res['status'];
                            alert('The image failed to save to the server');
                            } else {
                            alert('The server is not responding');
                            }
                            }
                            }
                            },
                            fileNameChanged: function (filename) {},
                          //  fileLoaded: function (file) {},
                            redirectUrl: false
                    });
                    },
                    buttons: {
                    formSubmit: {
                    text: 'Upload',
                            btnClass: 'btn-blue',
                            action: function () {
                            var imgcanvas = this.$content.find(".picedit_canvas > canvas");
                            var attr = $(imgcanvas).attr("width");
                            if (attr === undefined || attr === false) {
                            $.alert({
                            boxWidth: '30%',
                                    title: 'Error Alert!',
                                    content: 'No image is loaded! Try and load an image'
                            });
                            return false;
                            } else {
                            $('#save_image').trigger('click');
                            $('#r_icon').removeClass('required-border'); 
                            }
                            }
                    },
                            cancel: function () {delekey_reset('res_icon_del');}
                    }
            });

            //Function for the repeatable link fields
            $(function () {
            $("#rLinkGroup .link-container").repeatable({
            itemContainer: ".repeatable-container",
                    deleteTrigger: ".r_link_delete",
                    addTrigger: ".r_link_add",
                    template: "#r_link_repeatable",
                    startWith: 1,
                    max: 6,
                    min: 1
                 //   afterAdd:function(){inputsCheck();}
            });
            });
            
            // Topics token function using token-input plugin
            $("#ddtopic").tokenInput("<?php echo BASE_URL . '/admin/remotajax.php'?>", {
                    queryParam: "topic_q",
                    enableHTML: true,
                    allowFreeTagging: true,
                    preventDuplicates: true,
                    searchingText: "Searching Resource Topics...",
                    placeholder: "Insert Resource's Topic(s)",
                    placeholderMore: "Insert more Resource's Topic(s)",
                    noResultsText: "No Topic matches <a id='newtopic' href='#divpop'>Add New Topic</a>",
                     <?php if($page=="editResource"): ?>
                    prePopulate:<?php if(isset($r_topics)){ echo json_encode($r_topics);} ?>,
                    processPrePopulate: true,
                    <?php endif ?>
                    onResult: function (results) {
                    $.each(results, function (index, topic) {
                    topic.name = topic.title + "under " + topic.subject;
                    });
                    return results;
                    },
                    onAdd: function(){
                    if ($('#rTopicSub > .token-input-list').hasClass('required-border')){$('#rTopicSub > .token-input-list').removeClass('required-border');}  
                    },
                    onFreeTaggingAdd: function () {
                    $('#token_hidden').val($("#token-input-ddtopic").val()); // Capture the text of the input with id token-input-ddtopic and temporarily store in the ddtopi_hidden input
                    setTimeout(function () {
                     $("#rTopicSub .token-input-token").last().hide();
                    }, 0);
                    $.confirm({
                    boxWidth: '30%',
                            useBootstrap: false,
                            title: 'Confirm!',
                            content: '<span style="font-size: 20px;"> Do you want to add <strong id="token-input-dtext" style="font-style: italic;">' + $("#token_hidden").val() + '</strong> to the list of topics? </span>',
                            buttons: {
                            Yes: function () {
                            //Start of yes
                            $.confirm({
                            boxWidth: '50%',
                                    useBootstrap: false,
                                    title: 'Add New Topic!',
                                    content: '' +
                                    '<form>' +
                                    '<input type="text" name="topic" id="addtopic" value="' + $("#token_hidden").val() + '" placeholder="* Enter topic\'s title" required />' +
                                    '<textarea name="tdef" id="topicDef" class="info-edit"  cols="30" rows="3" placeholder="Enter the Topic\'s Definition"/>' +
                                    '<select name="ddsubjects" id="ddsubjects" required>' +
                                    '</select>' +
                                    '</form>',
                                    onOpen: function () {
                                    return $.ajax({
                                    url: '<?php echo BASE_URL . '/admin/remotajax.php'?>',
                                            dataType: 'json',
                                            method: 'POST',
                                            data: {
                                            'ajaxsubjects': 1
                                            }
                                    }).done(function (response) {
                                    var len = response.length;
                                    $("#ddsubjects").empty();
                                    $("#ddsubjects").append('<option value="" selected disabled>* Choose Topic\'s Subject</option>');
                                    for (var i = 0; i < len; i++) {
                                    var id = response[i]['id'];
                                    var name = response[i]['name'];
                                    $("#ddsubjects").append('<option value="' + id + '">' + name + '</option>');
                                    }
                                    }).fail(function () {
                                    $("#ddsubjects").append('<option value="" selected disabled>Error loading subjects</option>');
                                    });
                                    },
                                    buttons: {
                                    formSubmit: {
                                    text: 'Submit',
                                            btnClass: 'btn-blue',
                                            action: function () {
                                            var topic_name = this.$content.find('#addtopic').val();
                                            var topic_def = this.$content.find('#topicDef').val();
                                            var topicSub = this.$content.find('#ddsubjects :selected');
                                            var topicSub_id = topicSub.val();
                                            var topicSub_name = topicSub.text();
                                            if (topicSub_id === "") {
                                            $.alert('Ensure you select the subject for the new topic');
                                            return false;
                                            }
                                            if (topic_name === "") {
                                            $.alert('Ensure you type in a topic');
                                            return false;
                                            }
                                            if (topic_name !== "" && topicSub_id !== "") {
                                            $.ajax({
                                            url: '<?php echo BASE_URL . '/admin/remotajax.php'?>',
                                                    method: 'POST',
                                                    data: {
                                                    ajaxtopicsave: 1,
                                                    topic_name: topic_name,
                                                    topic_def: topic_def,
                                                    subject_id: topicSub_id
                                                    }
                                            }).done(function (res) {
                                                res = JSON.parse(res);
                                                if(res['msg']=='saved'){
                                            $.alert('You just successfully saved \"' + topic_name + '\" under \"' + topicSub_name + '\".');
                                                }else if(res['msg']=='exist'){
                                            $.alert('"' + topic_name + '" already existing as a topic under "' + topicSub_name + '".');
                                                }else if(res['msg']=='notsaved'){
                                                    $.alert('Error occurs while saving the topic \"' + topic_name + '\". <br/> Try Again if \"' + topic_name + '\" under \"' + topicSub_name + '\" does not exist.');
                                                }else{
                                                    $.alert('Sorry, the server failed to process your request.');
                                                }
                                            });
                                            }
                                            $('#tag_hidden').val("");
                                            }
                                    },
                                            cancel: function () {
                                            if ($("#rTopicSub .token-input-token p").last().text().search(/undefined*.*undefined/i) === 0){
                                            $(".token-input-token").last().hide();
                                            }
                                            $('#token_hidden').val("");
                                            },
                                    },
                                    onContentReady: function () {
                                    // bind to events
                                    var jc = this;
                                    this.$content.find('form').on('submit', function (e) {
                                    // if the user submits the form by pressing enter in the field.
                                    e.preventDefault();
                                    jc.$$formSubmit.trigger('click'); // reference the button and click it
                                    });
                                    }
                            });
                            //End of yes
                            },
                                    No: function () {
                                    if ($("#rTopicSub .token-input-token p").last().text().search(/undefined*.*undefined/i) === 0 ){
                                    $(".token-input-token").last().hide();
                                    }
                                    $('#token_hidden').val("");
                                    //       $(".subject_display").last().remove();
                                    }
                            }
                    });
                    },
                    resultsFormatter: function (item) {
                    return "<li><p>" + item.title + " <b style='font-style: italic;font-weight: normal;'> under " + item.subject + "</b></p></li>"
                    },
                    tokenFormatter: function (item) {
                    return "<li><p>" + item.title + " <b style='font-style: italic;font-weight: normal;'> under " + item.subject + "</b></p></li>"
                    }
            });
            
               // Tags token function using token-input plugin
            $("#r_tags").tokenInput("<?php echo BASE_URL . '/admin/remotajax.php'?>", {
            queryParam: "tag_q",
                    allowFreeTagging: true,
                    enableHTML: true,
                    minChars: 1,
                    preventDuplicates: true,
                    searchingText: "Searching Tags...",
                    placeholder: "Insert resource's tag(s)/ keyword(s)",
                    placeholderMore: "Insert more resource's tag(s)/ keyword(s)",
                    noResultsText: "This is a new tag! <a id='newtopic' href='#divpop'>Add this to Tag list</a>",
                     <?php if($page=="editResource"): ?>
                    prePopulate:<?php if(isset($restags)){ echo json_encode($restags);} ?>,
                    processPrePopulate: true,
                    <?php endif ?>
                    onResult: function (results) {
                    $.each(results, function (index, tag) {
                    tag.name = tag.tag;
                    });
                    return results;
                    },
                    onAdd: function(){
                    if ($('#rdata > .token-input-list').hasClass('required-border')){$('#rdata > .token-input-list').removeClass('required-border');}   // Required css border when the inout is not empty
                    },
                    onFreeTaggingAdd:function () {
                    $('#token_hidden').val($("#token-input-r_tags").val()); // Capture the text of the input with id token-input-ddtopic and temporarily store in the ddtopi_hidden input
                    setTimeout(function(){
                    $("#rdata .token-input-token").last().remove();
                    }, 0);
                    $.confirm({
                    boxWidth: '50%',
                            useBootstrap: false,
                            title: 'Do you want to add a new tag to system tags list?',
                            content: '<form>'+'<input type="text" id="addtag" value="' + $("#token_hidden").val() + '" placeholder="* Enter new\'s tag" /> <br/>'+'<textarea  id="tagDef" class="info-edit"  cols="30" rows="3" placeholder="Enter the Tag\'s Definition"/>'+ '</form>',
                            buttons: {
                            Yes: function () {
                            //Start of yes
                            var tag = $('#addtag').val();
                            var tag_def = this.$content.find('#tagDef').val();
                            $.ajax({
                            url: '<?php echo BASE_URL . '/admin/remotajax.php'?>',
                                    method: 'POST',
                                    data: {
                                    ajaxtagsave: tag,
                                    tagdef: tag_def
                                    }
                            }).done(function (res) {
                            $.alert('You just successfully added "' + tag + '" to system tags list');
                            }).fail(function () {
                            $.alert('Error occurs while saving the tag "' + tag + '. <br/> Try Again but check if "' + tag  + '" already existing in the system tags list.');
                            });
                            $('#tag_hidden').val("");
                                },
                                    No: function () {
                                    if ($("#rdata .token-input-token p").last().text() === "undefined"){
                                    $(".token-input-token").last().remove();
                                    }
                                    $('#tag_hidden').val("");
                                    }
                            }
                    });
                    }
            });
            $("a#newtopics").fancybox({
            'hideOnContentClick': true
            });
            $("a#newtopic").click(function () {
            alert("Handler for .click() called.");
            }); 
        <?php if($page=="editResource"): ?>

        // Delete resources
             $('.res-delete').on('click touch', function(){
     event.preventDefault();
     setdelekey('resource_del');
           $.confirm({
                    boxWidth: '400px',
                    useBootstrap: false,
                    autoClose: 'No|8000',
               //     title: 'Delete this Resource!',
                    content: '<span style="font-size: 20px;">Delete this resource?! </span>',
                     buttons: {
                            Yes: function () {
                  $.confirm({
                    boxWidth: '400px',
                    autoClose: 'No|8000',
                    useBootstrap: false,
               //     title: 'Delete this Resource!',
                    content: '<span style="font-size: 20px;">Are you sure you want to delete this resource?! </span>',
                     buttons: {
                            Yes: function () {
                                var delekey = $("#delekey").val();
                                 $.ajax({
                                    method: "POST",
                                    url: "<?php echo BASE_URL . '/admin/remotajax.php'?>",
                                    data:{
                                    "res_id": <?php if(isset($res_id)){echo $res_id;} ?>,
                                    'delekey': delekey,
                                    "res_image": '<?php if(isset($r_icon)){echo $r_icon;} ?>'
                                      }
                                      }).done(function (res) {
                                        if(res){ 
                                          var msg = JSON.parse(res);
                                       if(msg['msg']=='deleted'){
                                        delekey_reset('resource_del');
                                        $.confirm({
                                         boxWidth: '400px',
                                         content: '<span style="font-size: 20px;">The Resource has been succesfully Deleted?! </span>',
                                         buttons: {
                                         Proceed: function () {
                                       // window.location.href='http://interactive.pow/admin/resources.php';
                                       window.close();
                                        }
                                         }
                                         });
                                         }
                                        if(msg['msg']=='failed'){
                                        $.alert('The resource failed to be deleted');
                                        }
                                     }else{
                                $.alert('The Server is not responding')
                                }
                                });
                                
                            },
                             No: function () {delekey_reset('resource_del');}
                     }
                 });
                 },
                 No: function () {delekey_reset('resource_del');}
                 }
                });      
                 
            });
            <?php endif ?>
});
       </script>
