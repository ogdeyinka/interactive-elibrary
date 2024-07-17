<?php require_once('config.php') ?>
<?php require_once( ROOT_PATH . '/includes/public_functions.php') ?>

<?php // require_once( ROOT_PATH . '/includes/registration_login.php') ?>

<!-- Retrieve all posts from database  -->
<?php $subjects = getAllSubject(); ?>

<?php  require_once( ROOT_PATH . '/includes/head_section.php') ?>
	<title>Polawa Interactive e-Library | Subjects Home Page </title>
</head>
<body>

<div id="wrapper">
    <div id="header">
        <?php // include( ROOT_PATH . '/includes/navbar.php') ?>
        <div class="wsite-header"></div>
    </div>
    <div id="content-wrapper">
         <?php include(ROOT_PATH . '/includes/noscript.php') ?>
        <h1 class="paragraph" style="text-align: center;">
            All the interactive educational games, videos and
                    simulations&nbsp;in one place!&nbsp;&nbsp</h1>
<?php  require_once( ROOT_PATH . '/includes/search-form.php') ?>
        <div id="grid-wrap">
        <!--div style="height: 20px; overflow: hidden;"></div>
        <div id="pageGallery" class="imageGallery"-->
            <?php foreach ($subjects as $subject): ?>
                <div class="subjectContainer">
                    <a 	href="<?php echo BASE_URL . '/subject.php?subject=' . $subject['id'] ?>" alt="<?php echo $subject['name'] ?>">
                        <!-- Start CSS Image -->
                        <div class="iconImage">
                            <div class="screenSubject monitor">
                                <div class="content">
                                    <span class="pg"><?php echo $subject['name'] ?></span></div>
                                <div class="base baseSubject">
                                    <div class="grey-shadow"></div>
                                    <div class="foot top"></div>
                                    <div class="foot bottom"></div>
                                </div>
                            </div>
                        </div>

                        <!--img class="galleryImage" src="<?php echo BASE_URL . '/files/images/'.$subject['icon']; ?>" style="position: absolute; border: 0; width: 88.29%; top: 0%; left: 5.86%" /-->
                    </a>

                </div>
            <?php endforeach ?>
<span style="display: block; clear: both; height: 0px; overflow: hidden;"></span>

        </div>
    </div>
<?php include( ROOT_PATH . '/includes/footer.php') ?>


