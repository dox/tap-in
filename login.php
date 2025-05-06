<?php
require_once 'inc/autoload.php';
?>

<!doctype html>
<html lang="en">
	<head>
		<?php include_once("inc/html_head.php"); ?>
	</head>
	<body>
	<div class="container text-center">
		<div class="row justify-content-center pt-5">
			<div class="col-12 col-sm-8 col-md-6 col-lg-4 mx-auto">
				<?php
				if ($_SERVER['REQUEST_METHOD'] === 'POST') {
					if ($user->login($_POST['username'], $_POST['password'])) {
						header('Location: index.php');
						exit;
					} else {
						echo "<div class=\"alert alert-warning\" role=\"alert\">" . $user->getLastError() . "</div>";
					}
				}
				?>
				
				<form method="post" class="form-signin">
					<?php echo icon('stopwatch', '5em'); ?>
					  <h1 class="h3 mb-3 fw-normal">Please sign in</h1>
				  
					  <div class="form-floating">
						<input type="text" class="form-control" id="username" name="username" required>
						<label for="username">Username</label>
					  </div>
					  <div class="form-floating">
						<input type="password" class="form-control" id="password" name="password" required>
						
						<label for="floatingPassword">Password</label>
					  </div>
					  <div class="form-floating text-end">
						  <?php
						  if (reset_url) {
							  echo "<span class=\"form-label-description\">";
							  echo "<a href=\"" . reset_url . "\" class=\"text-muted\">Forgot Password?</a>";
							  echo "</span>";
						  }
						  ?>
					  </div>
				  
					  <!--<div class="form-check text-start my-3">
						<input class="form-check-input" type="checkbox" value="remember-me" id="checkDefault">
						<label class="form-check-label" for="checkDefault">
						  Remember me
						</label>
					  </div>-->
					  <button class="btn btn-primary w-100 py-2 my-3" type="submit">Sign in</button>
					  <a href="kiosk.php" class="btn btn-success w-50 py-2 my-3" type="submit">Kiosk</a>
					  
					  <footer class="text-center py-3 my-4 border-top">
						  <span class="text-muted">© 2025 <a href="https://github.com/dox/tap-in" class="link-secondary">github/dox/tap-in</a></span>
					  </footer>
					  
					</form>
			</div>
		</div>
	</div>
	<script src="js/main.js"></script>
	</body>
</html>