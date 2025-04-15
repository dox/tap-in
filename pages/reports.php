<h1><?php echo icon('cloud-download', '1em'); ?> Reports</h1>

<?php
$reports = [
	[
		"title" => "Report Test 1",
		"text" => "An export of all shifts",
		"links" => [
			["href" => "#", "text" => "Run Report"]
		]
	],
	[
		"title" => "Card Title 2",
		"text" => "Another description goes here.",
		"items" => ["Item A", "Item B", "Item C"],
		"links" => [
			["href" => "#", "text" => "More info"],
			["href" => "#", "text" => "Details"]
		]
	]
];
?>
<div class="row row-cols-1 row-cols-md-2 g-4">
  <?php
  foreach ($reports as $card) {
	  echo '<div class="col">
		  <div class="card">
			  <div class="card-body">
				  <h5 class="card-title">' . htmlspecialchars($card["title"]) . '</h5>
				  <p class="card-text">' . htmlspecialchars($card["text"]) . '</p>
			  </div>
			  <div class="card-body">';
	  
	  foreach ($card["links"] as $link) {
		  echo '<a href="' . htmlspecialchars($link["href"]) . '" class="card-link">' . htmlspecialchars($link["text"]) . '</a> ';
	  }
  
	  echo '</div>
		  </div>
	  </div>';
  }
  ?>
</div>