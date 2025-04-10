document.addEventListener("DOMContentLoaded", function () {
	// Get all numpad buttons
	const numButtons = document.querySelectorAll('.num');
	const userNumber = document.getElementById('userNumber');
	const submitBtn = document.getElementById('submitBtn');

	// Iterate over each button
	numButtons.forEach(function(button) {
		button.addEventListener('click', function() {
			const text = button.textContent.trim();
			
			if (text === "Clear") {
				// Clear the input field when "C" is clicked
				userNumber.value = '';
			} else {
				// Append the clicked number to the input field
				userNumber.value += text;
			}
		});
	});

	// Handle submit button click
	submitBtn.addEventListener('click', function() {
		const userNumberValue = userNumber.value;
		
		// Create an object to send with the POST request
		const data = { userNumber: userNumberValue };

		// Send the data using the Fetch API
		fetch('actions/kiosk_submit.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify(data)  // Send data as JSON
		})
		.then(response => response.json())  // Parse JSON response
		.then(data => {
			userNumber.value = '';
			
			// Show the modal with the appropriate message
			const modalMessage = document.getElementById('modalMessage');
			if (data.success) {
				modalMessage.innerHTML  = '<h1><span class="badge bg-success">SUCCESS</span></h1>';
			} else {
				modalMessage.innerHTML  = '<h1><span class="badge bg-danger">ERROR</span></h1>';
			}
			
			modalMessage.innerHTML += '<p>' + data.message + '</p>';

			// Show the modal
			const responseModal = new bootstrap.Modal(document.getElementById('responseModal'));
			responseModal.show();

			// Auto-close the modal after 3 seconds
			setTimeout(() => {
				responseModal.hide();
			}, 5000);
		})
		.catch(error => {
			userNumber.value = '';
			
			console.error('Error:', error);
			const modalMessage = document.getElementById('modalMessage');
			modalMessage.textContent = 'There was an error submitting the code. Please try again later.';

			// Show the modal
			const responseModal = new bootstrap.Modal(document.getElementById('responseModal'));
			responseModal.show();

			// Auto-close the modal after 3 seconds
			setTimeout(() => {
				responseModal.hide();
			}, 3000);
		});
	});
});