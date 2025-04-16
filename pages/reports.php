<h1><?php echo icon('cloud-download', '1em'); ?> Reports</h1>

<?php
$thisMonthStart = date('Y-m-01', strtotime('first day of this month'));
$thisMonthEnd = date('Y-m-t', strtotime('last day of this month'));
$lastMonthStart = date('Y-m-01', strtotime('first day of last month'));
$lastMonthEnd = date('Y-m-t', strtotime('last day of last month'));

$reports = [
	[
		"title" => "Shifts",
		"text" => "An export of all shifts",
		"items" => [
			["href" => "export.php?page=shifts&from={$thisMonthStart}&to={$thisMonthEnd}", "text" => "This Month (" . date('F', strtotime('first day of this month')) . ")"],
			["href" => "export.php?page=shifts&from={$lastMonthStart}&to={$lastMonthEnd}", "text" => "Last Month (" . date('F', strtotime('first day of last month')) . ")"],
			["href" => "export.php?page=shifts", "text" => "All Shifts"]
		],
		"last_run" => "debug"
	],
	[
		"title" => "Staff",
		"text" => "An export of all staff",
		"items" => [
			["href" => "export.php?page=staff&status=enabled", "text" => "All Staff (Enabled)"],
			["href" => "export.php?page=staff", "text" => "All Staff"]
		],
		"last_run" => "debug"
	]
];
?>
<div class="row row-cols-1 row-cols-md-3 g-4">
  <?php
  foreach ($reports as $card) {
	  echo '<div class="col">
		  <div class="card">
			  <div class="card-body">
				  <h5 class="card-title">' . htmlspecialchars($card["title"]) . '</h5>
				  <p class="card-text">' . htmlspecialchars($card["text"]) . '</p>
			  </div>
			  <ul class="list-group list-group-flush">';
	  
	  foreach ($card["items"] as $item) {
		  echo '<li class="list-group-item"><a href="' . htmlspecialchars($item["href"]) . '">' . htmlspecialchars($item["text"]) . '</a></li>';
	  }
  
	  echo '</ul>
			  <div class="card-body">';
	  
	  echo '<div class="card-link">' . htmlspecialchars($card["last_run"]) . '</div>';
  
	  echo '</div>
		  </div>
	  </div>';
  }
  ?>
</div>