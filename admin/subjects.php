<?php include('../config.php'); ?>
<?php include(ROOT_PATH . '/admin/includes/admin_functions.php'); ?>
<?php include(ROOT_PATH . '/admin/includes/res_functions.php'); ?>
<?php include(ROOT_PATH . '/admin/includes/head_section.php'); ?>

<title>Polawa Interactive e-Library Administration | Manage Subjects</title>
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
<?php $subjects = getAllSubject(); ?>
            <div class="admin-res">
                <div style="text-align: center;"> <button type="submit" id="create-new-subject" class="res-main-btn">Create New Subject</button>  </div>
                <hr />
                <!--form id="topic-search" style="display: none;">
                    <input type="text" id="topic-query" placeholder="Search subject to edit..." name="query">
                    <button id="search_Topics" type="submit">Search</button>
                </form-->
                
                
            <div id="grid-wrap">
            <?php  foreach ($subjects as $subject): ?>
                <div class="subjectContainer">
                        <!-- Start CSS Image -->
                        <div class="iconImage">
                            <div class="screenSubject monitor">
                                <div class="content">
                                    <span class="pg" <?php getSubject_topics($subject['id']); if($topics_count===0){ echo "style='color:#5c8ac6'";} echo " >". $subject['name'] ?></span></div>
                                <div class="base baseSubject">
                                    <div class="grey-shadow"></div>
                                    <div class="foot top"></div>
                                    <div class="foot bottom"></div>
                                </div>
                                <div class='work-item-overlay'><div class='inner'><ul>
                         <?php 
                         echo "<li><a data-id='". $subject['id'] ."' data-title='".$subject['name']."' data-def='".$subject['def']."' data-active='".$subject['active']."' class='subject-edit fa fa-pencil res-btn'>Edit</a></li>";
                           if($topics_count!==0){echo "<li><form class='subject-topic-view' target='newsubtop' action='". BASE_URL . "/admin/topics.php' method='post'><input type='hidden' name='subject-view-edit' class='subject-view-input' value='".$subject['id']."' /> <a class='subject-view fa fa-link res-btn' target='_blank'>View</a></form></li>";}      
                           if($topics_count===0){echo "<li><a data-id='". $subject['id'] ."' data-title='".$subject['name']."' class='subject-delete fa res-btn fa-times'>Delete</a></li>";}
                           ?> 
                              </ul></div>
                            </div>
                        </div>
                </div>
                         </div>
            <?php endforeach ?>
<span style="display: block; clear: both; height: 0px; overflow: hidden;"></span>

        </div>
</div>
</div>		<!-- footer -->
  <script type="text/javascript">
      $('.subject-topic-view').on('click touch', function () {
      event.preventDefault();
       $this = $(this);
       $this.closest('.work-item-overlay').find('form').submit();
    });
 //   Create new subject
    $('#create-new-subject').confirm({
                boxWidth: '50%',
                useBootstrap: false,
                title: 'Add New Subject!',
                content: '' +
                        '<form>' +
                                '<input type="text" name="subject" id="addsubject" placeholder="* Enter Subject\'s title" required />' +
                                '<textarea name="sdef" id="subjectDef" class="info-edit"  cols="30" rows="3" placeholder="Enter the Subject\'s Information"/>' +
                                '</form>',
                        buttons: {
                            formSubmit: {
                                text: 'Submit',
                                btnClass: 'btn-blue',
                                action: function () {
                                    var subject_name = this.$content.find('#addsubject').val();
                                    var subject_def = this.$content.find('#subjectDef').val();
                                    if (subject_name === "") {
                                        $.alert('Ensure you define a name for your subject');
                                        return false;
                                    }
                                    if (subject_name !== "") {
                                        $.ajax({
                                            url: 'remotajax.php',
                                            method: 'POST',
                                            data: {
                                                ajaxsubjectsave: 1,
                                                subject_name: subject_name,
                                                subject_def: subject_def
                                            }
                                        }).done(function (data) {
                                    var res = $.parseJSON(data);
                                    if (res.msg === 'saved') {
                                        $.confirm({
                                                boxWidth: '400px',
                                                content: '<span style="font-size: 20px;">You just successfully saved \"' + subject_name + '\" as a new subject.</span>',
                                                buttons: {
                                                    Proceed: function () {
                                                        location.reload();
                                                    }
                                                }
                                                    });
                                    } else if (res.msg === 'exist') {
                                        $.alert('"' + subject_name + '" is already existing as a subject.');
                                    } else if (res.msg === 'notsaved') {
                                        $.alert('Error occurs while saving the topic \"' + subject_name + '\". <br/> Try Again if \"' + subject_name + '\" does not exist as as subject.');
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
    // Edit & update subject
    $('.subject-edit').on('click touch', function () {
                    event.preventDefault();
                    var subject_id = subject_title = subject_def = 0;
                    $this = $(this);
                    var subject_id = $this.data('id');
                    var subject_title = $this.data('title');
                    var subject_def = $this.data('def');
                 //   var s_active = $this.data('active');
                    $.confirm({
                        boxWidth: '50%',
                        useBootstrap: false,
                        title: 'Edit and Update Subject!',
                        content: '' +
                                '<form>' +
                                '<input type="text" name="subject" id="addsubject" value="' + subject_title + '" placeholder="* Enter Subject\'s title" required />' +
                                '<textarea name="sdef" id="subjectDef" class="info-edit"  cols="30" rows="3" placeholder="Enter the Subject\'s Information">' + subject_def + '</textarea>' +
                                '</form>',
                         buttons: {
                            formSubmit: {
                                text: 'Submit',
                                btnClass: 'btn-blue',
                                action: function () {
                                    var subject_name = this.$content.find('#addsubject').val();
                                    var subject_def = this.$content.find('#subjectDef').val();
                                    if (subject_name === "") {
                                        $.alert('Ensure you define a name for your subject');
                                        return false;
                                    }
                                    if (subject_name !== "") {
                                        $.ajax({
                                            url: 'remotajax.php',
                                            method: 'POST',
                                            data: {
                                                ajaxsubjectupdate: 1,
                                                subject_id: subject_id,
                                                subject_name: subject_name,
                                                subject_def: subject_def
                                            }
                                        }).done(function (data) {
                                            var res = $.parseJSON(data);
                                            if (res.msg === 'updated') {
                                                $.confirm({
                                                        boxWidth: '400px',
                                                        content: '<span style="font-size: 20px;">You just successfully updated the subject to \"' + subject_name + '\".</span>',
                                                        buttons: {
                                                            Proceed: function () {
                                                                location.reload();
                                                            }
                                                        }
                                                    });
                                            } else if (res.msg === 'notupdated') {
                                                $.alert('Error occurs while updating the subject \"' + subject_name + '\".');
                                            } else if (res.msg === 'inputerror') {
                                                $.alert('The subject update failed!<br/> Kindly check if your inputs are valid as required.');
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
         <?php require_once(ROOT_PATH . '/admin/includes/delekey.php'); ?>       
                $('.subject-delete').on('click touch', function () {
                    event.preventDefault();
                    $this = $(this);
                    var subject_id = $this.data('id');
                    var subject_title = $this.data('title');
                    setdelekey('subject_del');
                    $.confirm({
                        boxWidth: '400px',
                        useBootstrap: false,
                        autoClose: 'No|8000',
                        content: '<span style="font-size: 20px;">Delete this subject: "' + subject_title + '?! </span>',
                        buttons: {
                            Yes: function () {
                                $.confirm({
                                    boxWidth: '400px',
                                    autoClose: 'No|8000',
                                    useBootstrap: false,
                                    content: '<span style="font-size: 20px;">Are you sure you want to delete the subject?!! </span>',
                                    buttons: {
                                        Yes: function () {
                                            var delekey = $("#delekey").val();
                                            $.ajax({
                                                method: "POST",
                                                url: 'remotajax.php',
                                                data: {
                                                    subject_id: subject_id,
                                                    'delekey': delekey
                                                },
                                                success: function () {
                                                    delekey_reset('subject_del');
                                                    $.confirm({
                                                        boxWidth: '400px',
                                                        content: '<span style="font-size: 20px;">The Subject "' + subject_title + '" has successfully been deleted.</span>',
                                                        buttons: {
                                                            Proceed: function () {
                                                                $this.closest('.subjectContainer').remove();
                                                                location.reload();
                                                            }
                                                        }
                                                    });
                                                },
                                                error: function () {
                                                    $.alert('The Subject failed to be deleted')
                                                }
                                            });

                                        },
                                        No: function () {
                                            delekey_reset('subject_del');
                                        }
                                    }
                                });
                            },
                            No: function () {
                                delekey_reset('subject_del');
                            }
                        }
                    });
                });
      </script>
<?php include( ROOT_PATH . '/includes/footer.php') ?>
		<!-- // footer -->
