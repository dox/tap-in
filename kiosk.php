<?php
include_once("inc/autoload.php");
?>

<!doctype html>
<html lang="en" data-bs-theme="dark">
	<head>
		<?php include_once("inc/html_head.php"); ?>
		<link href="css/kiosk.css" rel="stylesheet">
	</head>
	<body>
	<div class="container text-center">
		<div class="row justify-content-center">
			<div class="col-4">
				<div class="my-3">
					<input type="text" id="userNumber" class="form-control form-control-lg text-center" readonly placeholder="Enter number">
				</div>
				
				<!-- Numpad buttons -->
				<div class="numpad">
					<div class="num btn btn-secondary">1</div>
					<div class="num btn btn-secondary">2</div>
					<div class="num btn btn-secondary">3</div>
					<div class="num btn btn-secondary">4</div>
					<div class="num btn btn-secondary">5</div>
					<div class="num btn btn-secondary">6</div>
					<div class="num btn btn-secondary">7</div>
					<div class="num btn btn-secondary">8</div>
					<div class="num btn btn-secondary">9</div>
					<div class="num btn btn-secondary">0</div>
					<div class="num btn btn-dark disabled"></div> <!-- Clear button -->
					<div class="num btn btn-dark">Clear</div> <!-- Clear button -->
				</div>
				
				<!-- Submit button -->
				<button class="btn btn-success submit-btn" id="submitBtn">Submit</button>
			</div>
		</div>
	</div>
	
	<!-- Bootstrap Modal for Response -->
	<div class="modal fade" id="responseModal" tabindex="-1" aria-labelledby="responseModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="responseModalLabel">Submission Response</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body text-center" id="modalMessage">
					<!-- Message will be displayed here -->
				</div>
			</div>
		</div>
	</div>
	
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
	<script src="js/main.js"></script>
	<script src="js/kiosk.js"></script>
</body>
</html>