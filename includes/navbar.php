	<?php $nav_lists = getAllSubject(); ?>
    <script>
        function openNav() {
            document.getElementById("navigation").style.width = "auto";
            document.getElementById("navigation").style.minWidth = "200px";
        }

        function closeNav() {
            document.getElementById("navigation").style.width = "0";
            document.getElementById("navigation").style.minWidth = "0";
        }
    </script>
    <div id="navigation" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <ul class="wsite-menu-default">
            <li class="sub-menu-header">Subjects List</li>
            <?php foreach ($nav_lists as $nav_list): ?>
                <li > <a href="<?php echo BASE_URL. '/subject.php?subject=' . $nav_list['id'] ?>" ><?php echo $nav_list['name']?></a></li>
            <?php endforeach ?>
        </ul>
    </div>
    <div class="header-menu"><span id="home"> <a href="<?php echo BASE_URL ?>" >&#127968;Home</a> </span> <span id="sub_menu" onclick="openNav()">&#9776;Subjects Menu</span></div>
