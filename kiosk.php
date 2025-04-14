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
		<div class="d-flex align-items-center justify-content-center min-vh-100">
			<div class="container text-center p-3">
				<div class="row">
					<div class="col-12 col-sm-8 col-md-6 col-lg-4 mx-auto">
						<!-- Your dial pad here -->
						<input type="text" id="userNumber" class="form-control form-control-lg text-center my-3" readonly placeholder="Enter number">
						
						<div class="row g-2 gx-3">
							<div class="col-4"><button class="num btn btn-secondary btn-lg w-100">1</button></div>
							<div class="col-4"><button class="num btn btn-secondary btn-lg w-100">2</button></div>
							<div class="col-4"><button class="num btn btn-secondary btn-lg w-100">3</button></div>
							<div class="col-4"><button class="num btn btn-secondary btn-lg w-100">4</button></div>
							<div class="col-4"><button class="num btn btn-secondary btn-lg w-100">5</button></div>
							<div class="col-4"><button class="num btn btn-secondary btn-lg w-100">6</button></div>
							<div class="col-4"><button class="num btn btn-secondary btn-lg w-100">7</button></div>
							<div class="col-4"><button class="num btn btn-secondary btn-lg w-100">8</button></div>
							<div class="col-4"><button class="num btn btn-secondary btn-lg w-100">9</button></div>
							<div class="col-4"><button class="num btn btn-dark btn-lg w-100" disabled></button></div>
							<div class="col-4"><button class="num btn btn-secondary btn-lg w-100">0</button></div>
							<div class="col-4"><button class="num btn btn-link btn-lg w-100" id="clearBtn">Clear</button></div>
						</div>
						
						<button class="btn btn-success btn-lg w-100 my-3" id="submitBtn">Submit</button>
					</div>
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
		
		<script>
		// set the modal timeout to a global variable
		window.kioskTimeout = <?php echo (setting('kiosk_modal_timeout')* 1000); ?>
		</script>
		
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
		<script src="js/main.js"></script>
	</body>
</html>

<script>
function performShiftAction(userId, action) {
	const formData = new FormData();
	formData.append('userId', userId);
	formData.append('action', action);

	fetch('actions/kiosk_action.php', {
		method: 'POST',
		body: formData
	})
	.then(response => response.text())
	.then(result => {
		const modalMessage = document.getElementById('modalMessage');
		modalMessage.innerHTML = result;

		// Auto-close after kiosk_modal_fail_timeout seconds
		setTimeout(() => {
			const modalEl = document.getElementById('responseModal');
			const modalInstance = bootstrap.Modal.getInstance(modalEl);
			modalInstance.hide();
		}, <?php echo (setting('kiosk_modal_timeout')* 1000); ?>);
	})
	.catch(error => {
		console.error('Error:', error);
		const modalMessage = document.getElementById('modalMessage');
		modalMessage.textContent = 'Error performing shift action.';
	});
}
</script>