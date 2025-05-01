<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// Gather the POST data into an array
	$data = [
		'value' => $_POST['value']
	];
	
	// Update the shift record in the database
	$updateSuccess = $db->update('settings', $data, 'uid', $_POST['uid']);

	if ($updateSuccess) {
		$logData = [
			'category' => 'setting',
			'result'   => 'success',
			'description' => 'Setting record for ' . $uid . ' updated with ' . implode(", ", $_POST)
		];
		$log->create($logData);
		
		echo alert('success', "Success!", "Setting updated successfully!");
	} else {
		$logData = [
			'category' => 'setting',
			'result'   => 'warning',
			'description' => 'Setting record for ' . $uid . ' failed to update with ' . implode(", ", $_POST)
		];
		$log->create($logData);
		
		echo alert('danger', "Error!", "Failed to update setting.");
	}
}

$sql = "SELECT * FROM settings ORDER BY name ASC";
$settingsAll = $db->get($sql);
?>
<h1><?php echo icon('gear', '1em'); ?> Settings</h1>

<?php
echo alert('danger', "Warning!", "Making changes to these settings can disrupt the running of this site.  Proceed with caution.");
?>

<div class="accordion" id="accordionExample">
	<?php
	foreach ($settingsAll as $setting) {
		// Determine the show states based on the 'settingUID' parameter
		$isActive = isset($_GET['settingUID']) && $_GET['settingUID'] == $setting['uid'];
		$headingShow = $isActive ? "accordion-button" : "accordion-button collapsed";
		$settingShow = $isActive ? "accordion-collapse show" : "accordion-collapse collapse";
		
		// Generate item name and the start of the output string
		$itemName = "collapse-" . $setting['uid'];
		
		// Handle different setting types
		switch ($setting['type']) {
			case 'numeric':
				$settingForm = "<div class=\"input-group\">
				<input type=\"number\" class=\"form-control\" id=\"value\" name=\"value\" value=\"{$setting['value']}\">
				<button class=\"btn btn-primary\" type=\"submit\" id=\"button-addon2\">Update</button>
				</div>";
			break;
			
			case 'boolean':
				$checked = ($setting['value'] == "true") ? "checked" : "";
				
				$settingForm = "<div class=\"form-check\">
				<input type=\"hidden\" id=\"value\" name=\"value\" value=\"false\">
				<input type=\"checkbox\" class=\"form-check-input\" id=\"value\" name=\"value\" value=\"true\" {$checked}>
				<button class=\"btn btn-primary\" type=\"submit\" id=\"button-addon2\">Update</button>
				</div>";
			break;
			
			case 'json':
				$settingForm = "<textarea rows=\"10\" class=\"form-control\" id=\"value\" name=\"value\">" . json_encode(json_decode($setting['value']), JSON_PRETTY_PRINT) . "</textarea>
				<button class=\"btn btn-primary\" type=\"submit\" id=\"button-addon2\">Update</button>";
			break;
			
			case 'hidden':
				$settingForm = "Setting cannot be changed here";
			break;
			
			default:
				$settingForm = "<div class=\"input-group\">
				<input type=\"text\" class=\"form-control\" id=\"value\" name=\"value\" value=\"{$setting['value']}\">
				<button class=\"btn btn-primary\" type=\"submit\" id=\"button-addon2\">Update</button>
				</div>";
			break;
		}
		
		$output  = "<div class=\"accordion-item\">";
		$output .= "<h2 class=\"accordion-header\">" ;
		$output .= "<button class=\"" . $headingShow . "\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#" . $itemName . "\" aria-expanded=\"true\" aria-controls=\"" . $itemName . "\"><strong>" . $setting['name'] . "</strong></button>";
		$output .= "</h2>";
		
		$output .= "<div id=\"" . $itemName . "\" class=\"" . $settingShow . "\" data-bs-parent=\"#accordionExample\">";
		$output .= "<div class=\"accordion-body\">";
		$output .= "<form method=\"post\" id=\"form-" . $setting['uid'] . "\" action=\"" . $_SERVER['REQUEST_URI'] . "\">";
		$output .= $settingForm;
		$output .= "<div id=\"" . $itemName . "Help\" class=\"form-text\">" . $setting['description'] . "</div>";
		$output .= "<input type=\"hidden\" id=\"uid\" name=\"uid\" value=\"" . $setting['uid'] . "\">";
		$output .= "</form>";
		$output .= "</div>";
		$output .= "</div>";
		$output .= "</div>";
		
		echo $output;
	}
	?>
</div>

<h2 class="m-3">Icons Available in <code>/icons/</code></h2>
<?php
$iconsArray = scandir("icons");

echo "<div class=\"row text-center\">";
foreach ($iconsArray AS $icon) {
  $iconName = str_replace('.svg', '', $icon);
  
  if (is_file("icons/" . $icon)) {
	  $output  = "<div class=\"col-sm-3 mb-3\">";
	  $output .= "<div class=\"card\">";
	  $output .= "<div class=\"card-body\">";
	  $output .= "<h5 class=\"card-title text-truncate\"><code>" . $iconName . "</code></h5>";
	  $output .= icon($iconName, "3em");
	  $output .= "<p class=\"card-text pt-3 text-truncate text-muted\">" . $icon . "</p>";
	  $output .= "</div>";
	  $output .= "</div>";
	  $output .= "</div>";
	  
	  echo $output;
  }
  
  
}

echo "</div>";
?>