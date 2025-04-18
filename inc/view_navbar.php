<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
	<div class="container">
		<a class="navbar-brand" href="index.php"><?php echo icon('stopwatch') . " " . site_name; ?></a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		
		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav me-auto mb-2 mb-lg-0">
				<li class="nav-item">
					<a class="nav-link" href="index.php?page=staff"><?php echo icon('person'); ?> Staff</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="index.php?page=shifts"><?php echo icon('hourglass-split'); ?> Shifts</a>
				</li>
			</ul>
			
			<div class="d-flex">
				<a href="kiosk.php" class="btn btn-success me-2">Kiosk</a>
				
				<ul class="navbar-nav me-auto mb-2 mb-lg-0">
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Admin</a>
						<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
							<li><a class="dropdown-item" href="index.php?page=reports"><?php echo icon('cloud-download'); ?> Reports</a></li>
							<li><a class="dropdown-item" href="index.php?page=settings"><?php echo icon('gear'); ?> Settings</a></li>
							<li><a class="dropdown-item" href="index.php?page=logs"><?php echo icon('search'); ?> Logs</a></li>
							<li><hr class="dropdown-divider"></li>
							<li><a class="dropdown-item" href="logout.php"><?php echo icon('box-arrow-left'); ?> Logout</a></li>
						</ul>
					</li>
				</ul>
			</div>
			
		</div>
	</div>
</nav>