<?php include('../config.php'); ?>
<?php include(ROOT_PATH . '/admin/includes/admin_functions.php'); ?>
<?php include(ROOT_PATH . '/admin/includes/res_functions.php'); ?>
<?php include(ROOT_PATH . '/admin/includes/head_section.php'); ?>

<title>Polawa Interactive e-Library Administration | Manage Topics</title>
</head>
<body>
    <div id="wrapper">
        <div id="header">
            <div class="wsite-header"></div>
        </div>

        <!-- Page content -->
        <div class="adm_container">
            <?php include(ROOT_PATH . '/includes/noscript.php') ?> 
            <!-- Left side menu -->
            <?php include(ROOT_PATH . '/admin/includes/menu.php') ?>
            <!-- Display notification message -->
            <?php include(ROOT_PATH . '/includes/messages.php') ?>
            <?php
            $resources_count = 0;
            $allSubjects = getAllSubject();
           
            /*
            if(filter_has_var(INPUT_GET, 'subject')){
            $getsubject = filter_input(INPUT_GET, 'subject', FILTER_SANITIZE_NUMBER_INT);
            $topics = getSubject_topics($subject);
            $resources = getSubResources($subject);
            } */
            ?>
            <div class="admin-res">
                <div style="text-align: center;"> <button type="submit" id="create-new-topic" class="res-main-btn">Create New Topic</button>  <button type="submit" id="get-topic-search-btn" class="res-main-btn">Search Topic(s)</button>  <button type="submit" id="get-topic-subtop-btn" class="res-main-btn">Get Topics by Subject</button> </div>
                <hr />
                <form id="topic-search" style="display: none;">
                    <input type="text" id="topic-query" placeholder="Search Topic(s)..." name="query">
                    <button id="search_Topics" type="submit">Search</button>
                </form>
                <form id="subject-select" style="display: none;">
                    <select name="subject" id="ddsubjects">
                    <?php $subject_selected=false;  if(filter_has_var(INPUT_POST, 'subject-view-edit')){
             $subject_view = filter_input(INPUT_POST, 'subject-view-edit', FILTER_SANITIZE_NUMBER_INT); 
                    }
                    ?>
                        <option value="" <?php if(!isset($subject_view)){echo 'selected';} ?> disabled>Choose Resource Subject</option>
                <?php foreach ($allSubjects as $ddsubject): ?>
                            <option <?php echo "value='".$ddsubject['id']."' "; if(isset($subject_view) && $subject_view===$ddsubject['id']){echo 'selected '; $subject_selected=true;} ?>>
                <?php echo $ddsubject['name']; ?>
                            </option>
                <?php endforeach ?>
                    </select>
                    <br/>
                    <div style="text-align: center;">
                        <button type="submit" id="get_Topics">Get Topics</button>
                    </div>
                </form>
                <hr/>
                <h2 class="wsite-content-title" style="text-align: left;padding: 20px 20px 0;font-size: 22px;">
                  </h2>
                <div id="topicGallery"></div>
            </div>
        </div>

        <script type="text/javascript">  
            $(document).ready(function () {
<?php if ($subject_selected === TRUE): ?>
setTimeout(function () { $('#get_Topics').click();},0);
                         //   topicsAjaxLoad(); 
                   /*if($("#subject-select").find('#ddsubjects :selected').val().length){
                     topicsAjaxLoad();  
                    } */
<?php endif ?>
<?php if (isset($query)) : ?>
                    $('#topic-search,.topic-search').show();
                    $('#get-topic-search-btn').addClass('buttonDisable');
<?php endif ?>
<?php if (!isset($query)) : ?>
                    $('#subject-select,.subject-select').show();
                    $('#get-topic-subtop-btn').addClass('buttonDisable');
<?php endif ?>   
            $('#get-topic-search-btn').on('click touch', function () {
                $('#topic-search,.topic-search').show();
                $('#subject-select,.subject-select').hide();
                $('#get-topic-subtop-btn').removeClass('buttonDisable');
                $('#get-topic-search-btn').addClass('buttonDisable');
            });
            $('#get-topic-subtop-btn').on('click touch', function () {
                $('#topic-search,.topic-search').hide();
                $('#subject-select,.subject-select').show();
                $('#get-topic-subtop-btn').addClass('buttonDisable');
                $('#get-topic-search-btn').removeClass('buttonDisable');
            });
                $("#ddsubjects").select2({
                    searchInputPlaceholder: 'Search to select subject'
                });
            //    $('#subject-select button').addClass('buttonDisable');
                $("#topicGallery").hide();
            });
            $('#subject-select, #topic-search').on('submit',function(){return false;});
            
<?php require_once(ROOT_PATH . '/admin/includes/delekey.php'); ?>
       // Ajax function to load topics into the dom     
    function topicsAjaxLoad (){ 
                var subject_id = $('#ddsubjects').val();
                var subject_name = $.trim($('#ddsubjects').find('option:selected').text());
                $("#topicGallery").show();
                if (subject_id) {
                    $.ajax({
                        type: 'POST',
                        url: 'remotajax.php',
                        data: {
                            'subject_id': subject_id,
                            'subject_name': subject_name,
                            'display_mode':'grid'
                            
                        },
                        beforeSend: function () {
                            $('#topicGallery').html('<span class="spinner"></span>');
                        },
                        success: function (data) {
                            $('#topicGallery').html(data);
                            $('#subject-select button').removeClass('buttonDisable');
                            $('.content').textfill({
                            maxFontPixels: 22
                                });
                        },
                         complete: function(){
                             topicEdit(); // Load this function to enable topic edit.
                         }
                    });
                } else {
                 //   $('#topicGallery').html('<select><option value="">Select Subject First</option></select>');
                }
            }
            //Function to get the topics when the button is clicked
        $('#get_Topics').on('click touch', function () {
            topicsAjaxLoad ();
              });
          //Topic Search     
             $('#search_Topics').on('click touch', function () {
            var topic_query = $('#topic-query').val();
            $("#topicGallery").show();
            if (topic_query) {
                    $.ajax({
                        type: 'POST',
                        url: 'remotajax.php',
                        data: {
                            'topic_query_by_search': topic_query
                        },
                        beforeSend: function () {
                            $('#topicGallery').html('<span class="spinner"></span>');
                        },
                        success: function (data) {
                            $('#topicGallery').html(data);
                            $('#subject-select button').removeClass('buttonDisable');
                            $('.content').textfill({
                                maxFontPixels: 22
                            });
                        },
                         complete: function(){
                             topicEdit(); // Load this function to enable topic edit.
                         }
                    });
                } else {
                    //   $('#topicGallery').html('<select><option value="">Select Subject First</option></select>');
                }

            });
            // Function to edit topic.
            function topicEdit() {
                $('.topic-edit').on('click touch', function () {
                    event.preventDefault();
                    var topic_id = topic_title = topic_def = 0;
                    $this = $(this);
                    var topic_id = $this.data('id');
                    var topic_title = $this.data('title');
                    var topic_def = $this.data('def');
                    var subjt = $this.data('subject');
                    var t_active = $this.data('active');
                    $.confirm({
                        boxWidth: '50%',
                        useBootstrap: false,
                        title: 'Edit and Update Topic!',
                        content: '' +
                                '<form>' +
                                '<input type="text" name="topic" id="addtopic" value="' + topic_title + '" placeholder="* Enter topic\'s title" required />' +
                                '<textarea name="tdef" id="topicDef" class="info-edit"  cols="30" rows="3" placeholder="Enter the Topic\'s Definition">' + topic_def + '</textarea>' +
                                '<select name="dsubjects" id="dsubjects" required></select>' +
                                '<!--br/><div class="topic-active">' +
                                '<input type="checkbox" name="r_active" value="1" class="r-switch-checkbox" id="topic-active-label" >' +
                                '<label class="r-switch-label" for="topic-active-label">' +
                                '<span class="r-switch-inner topic-active-inner"></span>' +
                                '<span class="r-switch-ctrl r-topic-switch-ctrl"></span>' +
                                '</label></div-->' +
                                '</form>',
                        onOpen: function () {
                            if (t_active === 1) {
                                $("#topic-active-label").attr('checked', true);
                            }
                            return $.ajax({
                                url: 'remotajax.php',
                                dataType: 'json',
                                method: 'POST',
                                data: {
                                    'ajaxsubjects': 1
                                }
                            }).done(function (response) {
                                var len = response.length;
                                $("#dsubjects").empty();
                                for (var i = 0; i < len; i++) {
                                    var id = response[i]['id'];
                                    var name = response[i]['name'];
                                    $("#dsubjects").append('<option value="' + id + '">' + name + '</option>');
                                }
                                $("#dsubjects").val(subjt).find("option[value=" + subjt + "]").attr('selected', true);
                            }).fail(function () {
                                $("#dsubjects").append('<option value="" selected disabled>Error loading subjects</option>');
                            });
                        },
                        buttons: {
                            formSubmit: {
                                text: 'Submit',
                                btnClass: 'btn-blue',
                                action: function () {
                                    var topic_name = this.$content.find('#addtopic').val();
                                    var topic_def = this.$content.find('#topicDef').val();
                                    var topicSub = this.$content.find('#dsubjects :selected');
                                    var topicSub_id = topicSub.val();
                                    var topicSub_name = topicSub.text();
                                    var isTopicActive = $("#topic-active-label").is(":checked");
                                    if (isTopicActive) {
                                        var topic_active = 1;
                                    } else {
                                        var topic_active = 0;
                                    }
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
                                                ajaxtopicupdate: 1,
                                                topic_id: topic_id,
                                                topic_name: topic_name,
                                                topic_def: topic_def,
                                                subject_id: topicSub_id,
                                                topic_active: topic_active
                                            }
                                        }).done(function (data) {
                                            var res = $.parseJSON(data);
                                            if (res.msg === 'updated') {
                                                $.confirm({
                                                        boxWidth: '400px',
                                                        content: '<span style="font-size: 20px;">You just successfully updated to \"' + topic_name + '\" under \"' + topicSub_name + '\".</span>',
                                                        buttons: {
                                                            Proceed: function () {
                                                                location.reload();
                                                            }
                                                        }
                                                    });
                                            } else if (res.msg === 'notupdated') {
                                                $.alert('Error occurs while updating the topic \"' + topic_name + '\" under \"' + topicSub_name + '\".');
                                            } else if (res.msg === 'inputerror') {
                                                $.alert('The Topic Update failed!<br/> Kindly check if your inputs are valid as required.');
                                            } else {
                                                $.alert("Sorry, the server failed to process your request.");
                                            }
                                        });
                                    }
                                }
                            },
                            cancel: function () {
                            }
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
                });
                $('.topic-delete').on('click touch', function () {
                    event.preventDefault();
                    $this = $(this);
                    var topic_id = $this.data('id');
                    var topic_title = $this.data('title');
                    var subjt = $this.data('subject');
                    setdelekey('topic_del');
                    $.confirm({
                        boxWidth: '400px',
                        useBootstrap: false,
                        autoClose: 'No|8000',
                        //     title: 'Delete this Resource!',
                        content: '<span style="font-size: 20px;">Delete this topic: "' + topic_title + '" under ' + subjt + '?! </span>',
                        buttons: {
                            Yes: function () {
                                $.confirm({
                                    boxWidth: '400px',
                                    autoClose: 'No|8000',
                                    useBootstrap: false,
                                    //     title: 'Delete this Resource!',
                                    content: '<span style="font-size: 20px;">Are you sure you want to delete the topic?! </span>',
                                    buttons: {
                                        Yes: function () {
                                            var delekey = $("#delekey").val();
                                            $.ajax({
                                                method: "POST",
                                                url: 'remotajax.php',
                                                data: {
                                                    topic_id: topic_id,
                                                    'delekey': delekey
                                                },
                                                success: function () {
                                                    delekey_reset('topic_del');
                                                    $.confirm({
                                                        boxWidth: '400px',
                                                        content: '<span style="font-size: 20px;">The Topic "' + topic_title + '" has successfully been deleted under ' + subjt + '</span>',
                                                        buttons: {
                                                            Proceed: function () {
                                                                $this.closest('.topicContainer').remove();
                                                                location.reload();
                                                            }
                                                        }
                                                    });
                                                },
                                                error: function () {
                                                    console.log('Due to an error, this resource has not been disabled ');
                                                    $.alert('The topic failed to be deleted')
                                                }
                                            });

                                        },
                                        No: function () {
                                            delekey_reset('topic_del');
                                        }
                                    }
                                });
                            },
                            No: function () {
                                delekey_reset('topic_del');
                            }
                        }
                    });
                });
            }
// Function to create new topic.
            $('#create-new-topic').confirm({
                boxWidth: '50%',
                useBootstrap: false,
                title: 'Add New Topic!',
                content: '' +
                        '<form>' +
                        '<input type="text" name="topic" id="addtopic" placeholder="* Enter topic\'s title" required />' +
                        '<textarea name="tdef" id="topicDef" class="info-edit"  cols="30" rows="3" placeholder="Enter the Topic\'s Definition"/>' +
                        '<select name="dsubjects" id="dsubjects" required>' +
                        '</select>' +
                        '</form>',
                onOpen: function () {
                    return $.ajax({
                        url: 'remotajax.php',
                        dataType: 'json',
                        method: 'POST',
                        data: {
                            'ajaxsubjects': 1
                        }
                    }).done(function (response) {
                        var len = response.length;
                        $("#dsubjects").empty();
                        $("#dsubjects").append('<option value="" selected disabled>* Choose Topic\'s Subject</option>');
                        for (var i = 0; i < len; i++) {
                            var id = response[i]['id'];
                            var name = response[i]['name'];
                            $("#dsubjects").append('<option value="' + id + '">' + name + '</option>');
                        }
                    }).fail(function () {
                        $("#dsubjects").append('<option value="" selected disabled>Error loading subjects</option>');
                    });
                },
                buttons: {
                    formSubmit: {
                        text: 'Submit',
                        btnClass: 'btn-blue',
                        action: function () {
                            var topic_name = this.$content.find('#addtopic').val();
                            var topic_def = this.$content.find('#topicDef').val();
                            var topicSub = this.$content.find('#dsubjects :selected');
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
                                }).done(function (data) {
                                    var res = $.parseJSON(data);
                                    if (res.msg === 'saved') {
                                         $.confirm({
                                                        boxWidth: '400px',
                                                        content: '<span style="font-size: 20px;">You just successfully saved \"' + topic_name + '\" under \"' + topicSub_name + '\".</span>',
                                                        buttons: {
                                                            Proceed: function () {
                                                                location.reload();
                                                            }
                                                        }
                                                    });
                                    } else if (res.msg === 'exist') {
                                        $.alert('"' + topic_name + '" already existing as a topic under "' + topicSub_name + '".');
                                    } else if (res.msg === 'notsaved') {
                                        $.alert('Error occurs while saving the topic \"' + topic_name + '\". <br/> Try Again if \"' + topic_name + '\" under \"' + topicSub_name + '\" does not exist.');
                                    } else {
                                        $.alert("Sorry, the server failed to process your request.");
                                    }
                                });
                            }
                        }
                    },
                    cancel: function () {
                    }
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

        </script>
        <!-- // Page content -->
        <!-- footer -->
<?php include( ROOT_PATH . '/includes/footer.php') ?>
        <!-- // footer -->
