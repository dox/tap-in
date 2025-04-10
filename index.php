<?php
include_once("inc/autoload.php");
requireLogin(); // Redirects if not logged in

?>

<!doctype html>
<html lang="en">
	<head>
		<?php include_once("inc/html_head.php"); ?>
	</head>
	<body>
	<?php include_once("inc/view_navbar.php"); ?>
	
	<div class="container my-5">
		<?php
		if ($user->isLoggedIn()) {
			$requestedPage = isset($_GET['page']) ? $_GET['page'] : 'index';
		} else {
			$requestedPage = 'logon';
		}
		
		$pagePath = __DIR__ . "/pages/{$requestedPage}.php";
		
		// Fallback if file doesn’t exist
		if (!file_exists($pagePath)) {
			$pagePath = __DIR__ . "/pages/404.php";
		}
		
		include_once($pagePath);
		?>
	</div>
	
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
	<script src="js/main.js"></script>
</body>
</html>