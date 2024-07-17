
       <script id="r_link_repeatable" type="text/template">
            <div class="repeatable-container">
            <table style="width: 100%">
            <tr>
            <td style="width: 80%">
            <input type="url" name="link_url[]" class="link_urls" placeholder="* Enter Resource URL" required oninvalid="this.setCustomValidity('Enter a valid url')" oninput="this.setCustomValidity('')"/>
            <input type="text" name="link_name[]" placeholder="* Enter Resource URL Name" maxlength="20" /> 
            <span> <label for="link_modalview[]"> Open the link as pop up modal view? </label><input type="checkbox" value="1" checked name="link_modalview[]"></span>
            </td>
            <td style="width: 20%">
            <input type="button" value="Delete this Link form" class="r_link_delete" />
            </td>
            </tr>
            </table>
            </div>
        </script>
        <script type="text/javascript">  
           $('document').ready(function(){    
  function buttonEnabled(){
        $('button[name="update_resource"]').removeClass('buttonDisable');     
         $('button[name="create_resource"]').removeClass('buttonDisable');
            }

         function buttonDisabled(){
             $('button[name="update_resource"]').addClass('buttonDisable');
             $('button[name="create_resource"]').addClass('buttonDisable');
         }
    buttonEnabled();
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
                                  var  url = "<?php echo BASE_URL . '/admin/edit.php?resource='?>" + res['res_id'];
                                        window.open(url,'_blank');
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
    //   Function to upload image    
                var r_img = $("#r_img").val(); 
                
             $("button[name='create_resource'],button[name='update_resource']").on('click', function(e){     // When the button named create_resource is clicked
           var error = false;
        //    Function to check if resource image is attached.
                    if($("#r_img").val().length===0) {
                    e.preventDefault();
                      buttonEnabled();
                    var error = true;
                       $('html, body').animate({ scrollTop: $("fieldset[name='rImage']").offset().top }, 500);   // Scroll up to the resource image point from submit button at the buttom 
                       $('#r_icon').addClass('required-border');   
                   }
                   // Function to check if at least a resource topic is added.
                  if($("input[name='r_title']").val().length===0) {
                      e.preventDefault();
                       buttonEnabled();
                      var error = true;
                  }
               //    Function to check if at least a resource topic is added.
                  if($("#ddtopic").val().length===0) {
                      e.preventDefault();
                        buttonEnabled();
                       $('#rTopicSub > .token-input-list').addClass('required-border'); 
                       var error = true;
                   }
                    //    Function to check if at least a resource tag is added.
                    if($("#r_tags").val().length===0) {
                       e.preventDefault();
                        buttonEnabled();
                       $('#rdata > .token-input-list').addClass('required-border');
                       var error = true;
                   }
                     //    Function to check if at least a resource type checkbox is checked.
                   if ($("[name='restypes[]']:checked").length < 1) {
                       buttonEnabled();
                       $('#rTypeGroup').addClass('fieldset-border');
                       var error = true;
                        e.preventDefault();
                   }
                    //    Function to check if at least a resource level checkbox is checked
                   if ($("[name='rlevels[]']:checked").length < 1) {
                       buttonEnabled();
                       $('#rLevel').addClass('fieldset-border');
                       var error = true;
                        e.preventDefault();
                   }
                   if(error == false){
                       buttonDisabled();
                   }
               });
     // If any of the checkboxes is checked then remove the error style
               $("[name='restypes[]']").change(function() {
                    if(this.checked) {
        if($('#rTypeGroup').hasClass('fieldset-border')){
            $('#rTypeGroup').removeClass('fieldset-border');
        }
                        }
                      });
                $("[name='rlevels[]']").change(function() {
                    if(this.checked) {
         if($('#rLevel').hasClass('fieldset-border')){
            $('#rLevel').removeClass('fieldset-border');
        }
                        }
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
           
           // Function to handle the uploading of image icons using confirm jquery plugin
        $('#r_icon').confirm({  // Using Jquery confirm plugin.
            boxWidth: '50%',
                    useBootstrap: false,
                    title: 'Add Resource Icon Image!',
                    content: '' +
                    '<form name="r_imgedit" method="POST" enctype="multipart/form-data" action="remotajax.php">' +
                    '<input type="file" name="r_image" id="r_image" placeholder="Upload Resource Preview Image Icon" >' + '<input type="hidden" name="imageupload" value="1">' + '<button type="submit" style="display: none;" class="btn" id="save_image">Save Image</button>' +
                    '</form>',
                    onOpenBefore: function () {
                    $('#r_image').picEdit({
                    imageUpdated: function (img) {},
                            formSubmitted: function (res) { 
                            if ($("img").parents("#rimageShow").length === 1) { // check if there is an existing image #rimageShow for the resource
                            //file duplicate management
                            <?php $token = md5(rand(1000, 9999));
                            $_SESSION['token_token'] = $token;
                                ?>
                            var r_img = $("#r_img").val();  // get the name of the image file if one already existing so as to delete
                            $.ajax({
                            method: 'POST',
                                    url: 'remotajax.php',
                                    data: {
                                    token_token: '<?php echo $token; ?>',
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
                                    }
                                    });
                            } else if (res['status']) {
                            var status = res['status'];
                            alert('The image failed to save to the server');
                            } else {
                            alert('The server is not responding');
                            }
                            }
                            },
                            fileNameChanged: function (filename) {},
                            fileLoaded: function (file) {},
                            redirectUrl: false
                    });
                    },
                    buttons: {
                    formSubmit: {
                    text: 'Submit',
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
                            //   $.alert('No image is loaded! Try and load an image');
                            return false;
                            } else {
                            $('#save_image').trigger('click');
                            $('#r_icon').removeClass('required-border'); 
                            }
                            }
                    },
                            cancel: function () {
                            //Close
                            }
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
            });
            });
            
            // Topics token function using token-input plugin
            $("#ddtopic").tokenInput("remotajax.php", {
            queryParam: "topic_q",
                    enableHTML: true,
                    allowFreeTagging: true,
                    preventDuplicates: true,
                    searchingText: "Searching Resource Topics...",
                    placeholder: "Search and select or insert Resource's Topic(s)",
                    placeholderMore: "Search and select or insert more Resource's Topic(s)",
                    noResultsText: "No Topic matches <a id='newtopic' href='#divpop'>Add New Topic</a>",
                     <?php if($page=="editResource"): ?>
                    prePopulate:<?php echo json_encode($r_topics); ?>,
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
                                            $.alert('You just successfully saved \"' + topic_name + '\" under \"' + topicSub_name + '\". Try type again \"' + topic_name + '\" and to confirm and select it for your resource');
                                            }).fail(function () {
                                            $.alert('Error occurs while saving the topic \"' + topic_name + '\". <br/> Try Again but check if \"' + topic_name + '\" under \"' + topicSub_name + '\" exist.');
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
                    placeholder: "Type and insert resource's tag(s)/ keyword(s)",
                    placeholderMore: "Insert more resource's tag(s)/ keyword(s)",
                    noResultsText: "This is a new tag! <a id='newtopic' href='#divpop'>Add this to Tag list</a>",
                     <?php if($page=="editResource"): ?>
                    prePopulate:<?php echo json_encode($restags); ?>,
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
             $('.res-delete').on('click touch', function(){
     event.preventDefault();
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
                                 $.ajax({
                                    method: "POST",
                                    url: "<?php echo BASE_URL . '/admin/remotajax.php'?>",
                                    data:{
                                    "res_id": <?php echo $res_id; ?>,
                                    "delete" : 1
                                      },
                                    success: function(response){
                                        $.confirm({
                                         boxWidth: '400px',
                                         content: '<span style="font-size: 20px;">The Resource has been succesfully Deleted?! </span>',
                                         buttons: {
                                         Proceed: function () {
                                        window.location.href='http://interactive.pow/admin/resources.php';
                                        }
                                         }
                                         });
                                     },
                                error:function(){
                                console.log('Due to an error, this resource has not been disabled ' );
                                $.alert('The resource failed to be deleted')
                                }
                                });
                                
                            },
                             No: function () {
                             }
                     }
                 });
                 },
                 No: function () { }
                 }
                });      
                 
            });
            <?php endif ?>
});
       </script>
