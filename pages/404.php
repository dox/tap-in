<?php
$logData = [
	'category' => 'system',
	'result'   => 'error',
	'description' => '404 for ' . $_SERVER['REQUEST_URI']
];
$log->create($logData);
?>

<div class="container my-5">
  <div class="position-relative p-5 text-center bg-body border border-dashed rounded-5">
	<?php echo icon('exclamation-circle-fill', '5em'); ?>
	
	<h1 class="text-body-emphasis mb-5">404: Page Not Found</h1>
	<p class="mb-4">It appears the page you have attempted to access does not exist.</p>
	<div class="mb-4 alert alert-danger" role="alert">
		<span class=" font-monospace"><?php echo $_SERVER['REQUEST_URI']; ?></span>
	</div>
	<p class="mb-4">This error has been logged.</p>
	<button class="btn btn-primary px-5 mb-5" type="button" onclick="history.back()">
	  Go back to whence ye came
	</button>
  </div>
</div>