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

  //  $("#rLinkGroup .link-container").sortable();
 //   $("#rLinkGroup .link-container").disableSelection();

  function buttonEnabled(){
        $('button[name="update_resource"]').removeClass('buttonDisable');     
         $('button[name="create_resource"]').removeClass('buttonDisable');
            }
     function buttonDisabled(){
             $('button[name="update_resource"]').addClass('buttonDisable');
             $('button[name="create_resource"]').addClass('buttonDisable');
         }
         
function isUrl(s) {
   var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
   return regexp.test(s);
}
  
       // Function for generic input checking. 
        var input_empty = false;
function inputCheck (input,scrollup=0,affected=0){
              if($(input).attr('type')=='checkbox'){var inputs = $(input+":checked").length< 1} else if($(input).attr('type')=='url'){var inputs =$(input).val().length==0 || !isUrl($(input).val())} else {var inputs =$(input).val().length==0}	// Conditions inputs.    
                    if(inputs) {
                     event.preventDefault();
                        buttonEnabled();  // Enable submit button
                      if(scrollup !=0) {$('html, body').animate({ scrollTop: $(scrollup).offset().top }, 1);}
                    if(affected !=0) {$(affected).addClass('required-border');}
                   // var input_empty = true;
                   event.preventDefault();
                     buttonEnabled();
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
             url: 'remotajax.php',
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
        $("button[name='create_resource'],button[name='update_resource']").on('click', function(){     // When the button named create_resource is clicked
        buttonDisabled();
        inputsCheck();
        //   if(input_empty == true){event.preventDefault();buttonEnabled();return false;}
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
           // Function to handle the uploading of image icons using confirm jquery plugin
        $('#r_icon').confirm({  // Using Jquery confirm plugin.
            boxWidth: '50%',
                    useBootstrap: false,
                    title: 'Add Resource Icon Image!',
                    content: '' +
                    '<form name="r_imgedit" method="POST" style="width:auto" enctype="multipart/form-data" action="remotajax.php">' +
                    '<input type="file" name="r_image" id="r_image" placeholder="Upload Resource Preview Image Icon" >' + '<input type="hidden" name="imagefileupload" value="1">' + '<button type="submit" style="display: none;" class="btn" id="save_image">Save Image</button>' +
                    '</form>',
                    onOpenBefore: function () {
                    <?php if($page=="editResource"):?>
                    if ($("#editedoutResImg").length===0){
                    $("fieldset[name='rImage']").append('<input name="editedoutResImg" id="editedoutResImg" type="text" value=\''+ $("#r_img").val() +'\' style="display: none" />');
                    }
                    <?php endif ?>
                    if ($("#rimageShow").has("img") && $("#r_img").val()) {setdelekey('res_icon_del');} 
                    $('#r_image').picEdit({
                    imageUpdated: function () {
                    },
                    formSubmitted: function (res) { 
                          //  var imagedone = $("#imagedone").val();
                            if ($("#rimageShow").has("img") && $("#delekey").val()) { // check if there is an existing image #rimageShow for the resource
                            var r_img = $("#r_img").val();  // get the name of the image file if one already existing so as to delete
                            var delekey = $("#delekey").val();
                            $.ajax({
                                    method: 'POST',
                                    url: 'remotajax.php',
                                    data: {
                                    'delekey': delekey,
                                    'filetodel': r_img
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
            $("#ddtopic").tokenInput("remotajax.php", {
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
                                    //  alert('onContentReady');
                                    //   var self = #ddsubject;
                                    return $.ajax({
                                    url: 'remotajax.php',
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
                                            url: 'remotajax.php',
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
            $("#r_tags").tokenInput("remotajax.php", {
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
                            url: 'remotajax.php',
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
