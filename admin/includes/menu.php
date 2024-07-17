<div class="menu">
	<div class="card">
		<div class="card-content">
			<a href="<?php echo BASE_URL . '/admin/add_resource.php' ?>">Add Resource</a>
			<a href="<?php echo BASE_URL . '/admin/resources.php' ?>">Manage Resources</a>
                        <a href="<?php echo BASE_URL . '/admin/topics.php' ?>">Manage Topics</a>
                        <a href="<?php echo BASE_URL . '/admin/subjects.php' ?>">Manage Subjects</a>
			<a href="<?php echo BASE_URL . '/admin/users.php' ?>">Manage Users</a>
		</div>
	</div>
    <div style="margin: 20px 8px;font-weight: bold;"> <?php getAllResources();echo "Total resources: " . $resources_count;?></div>
</div>
