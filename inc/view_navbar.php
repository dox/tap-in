<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
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
			
			<ul class="navbar-nav flex-row flex-wrap ms-md-auto">
				<li class="nav-item col-6 col-lg-auto">
					<a class="btn btn-sm btn-success my-1 px-lg-2" href="kiosk.php" target="_blank" rel="noopener">
						Kiosk
					</a>
				</li>
				<li class="nav-item py-2 py-lg-1 col-12 col-lg-auto">
					<div class="vr d-none d-lg-flex h-100 mx-lg-2 text-white"></div>
					<hr class="d-lg-none my-2 text-white-50">
				</li>
				<li class="nav-item dropdown">
				  <button class="btn btn-link nav-link dropdown-toggle d-flex align-items-center my-1" id="bd-theme" type="button"
						  aria-expanded="false" data-bs-toggle="dropdown" data-bs-display="static" aria-label="Toggle theme (auto)">
					<svg class="bi me-2 theme-icon-active" width="1em" height="1em"><use href="#circle-half"/></svg>
				  </button>
				  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="bd-theme">
					<li>
					  <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light" aria-pressed="false">
						<svg class="bi me-2" width="1em" height="1em"><use href="#sun-fill"/></svg>
						Light
					  </button>
					</li>
					<li>
					  <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark" aria-pressed="false">
						<svg class="bi me-2" width="1em" height="1em"><use href="#moon-stars-fill"/></svg>
						Dark
					  </button>
					</li>
					<li>
					  <button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="auto" aria-pressed="true">
						<svg class="bi me-2" width="1em" height="1em"><use href="#circle-half"/></svg>
						Auto
					  </button>
					</li>
				  </ul>
				</li>
				<li class="nav-item py-2 py-lg-1 col-12 col-lg-auto">
					<div class="vr d-none d-lg-flex h-100 mx-lg-2 text-white"></div>
					<hr class="d-lg-none my-2 text-white-50">
				</li>
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
</nav>




<!-- SVG icons -->
  <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
	<symbol id="sun-fill" fill="currentColor" viewBox="0 0 16 16">
	  <path d="M8 4a4 4 0 1 1 0 8A4 4 0 0 1 8 4Z"/>
	  <path d="M8 0a.5.5 0 0 1 .5.5V2a.5.5 0 0 1-1 0V.5A.5.5 0 0 1 8 0Zm0 14a.5.5 0 0 1 .5.5v1.5a.5.5 0 0 1-1 0V14.5A.5.5 0 0 1 8 14Zm7-6a.5.5 0 0 1 .5.5H16a.5.5 0 0 1 0 1h-1.5a.5.5 0 0 1-.5-.5ZM1.5 8A.5.5 0 0 1 2 7.5H.5a.5.5 0 0 1 0-1H2a.5.5 0 0 1-.5.5ZM13.657 2.343a.5.5 0 0 1 .707.707L13.207 4.207a.5.5 0 1 1-.707-.707l1.157-1.157Zm-11.314 0a.5.5 0 0 1 .707.707L1.893 4.207a.5.5 0 1 1-.707-.707l1.157-1.157Zm11.314 11.314a.5.5 0 0 1 .707.707l-1.157 1.157a.5.5 0 1 1-.707-.707l1.157-1.157Zm-11.314 0a.5.5 0 0 1 .707.707l-1.157 1.157a.5.5 0 1 1-.707-.707l1.157-1.157Z"/>
	</symbol>
	<symbol id="moon-stars-fill" fill="currentColor" viewBox="0 0 16 16">
	  <path d="M6 0a7 7 0 1 0 6.293 10.707c-.184.045-.37.075-.56.093A7.002 7.002 0 0 1 6 0Zm8 8a.5.5 0 0 1-.5.5H13v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1V6a.5.5 0 0 1 1 0v1h1a.5.5 0 0 1 .5.5Zm-2-6a.5.5 0 0 1-.5.5H11v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1V0a.5.5 0 0 1 1 0v1h1a.5.5 0 0 1 .5.5Z"/>
	</symbol>
	<symbol id="circle-half" fill="currentColor" viewBox="0 0 16 16">
	  <path d="M8 15V1a7 7 0 1 1 0 14Z"/>
	</symbol>
  </svg>