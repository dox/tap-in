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
		
		<footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
			<span class="text-muted">© 2025 <a href="https://github.com/dox/tap-in" class="link-secondary">github/dox/tap-in</a>.  All rights reserved.</span>
		</footer>
	</div>
	
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
	<script src="js/main.js"></script>
</body>
</html>

<script src="https://help.seh.ox.ac.uk/assets/chat/chat-no-jquery.min.js"></script>
<script>
(function() {
new ZammadChat({
		title: 'Need IT Support?',
		fontSize: '12px',
		background: '#6b7889',
		chatId: 1
	});
})();

</script>