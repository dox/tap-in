document.addEventListener("DOMContentLoaded", function () {
	// Get all numpad buttons
	const numButtons = document.querySelectorAll('.num');
	const userNumber = document.getElementById('userNumber');
	const submitBtn = document.getElementById('submitBtn');
	
	if (submitBtn && userNumber) {		
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
		submitBtn.addEventListener('click', function () {
			const userNumberValue = userNumber.value;
		
			const formData = new FormData();
			formData.append('userNumber', userNumberValue);
		
			fetch('actions/kiosk_check.php', {
				method: 'POST',
				body: formData
			})
			.then(response => response.text())
			.then(html => {
				userNumber.value = '';
		
				const modalMessage = document.getElementById('modalMessage');
				modalMessage.innerHTML = html;
		
				const responseModal = new bootstrap.Modal(document.getElementById('responseModal'));
				responseModal.show();
		
				// Auto-close after window.kioskTimeout seconds if no action taken
				setTimeout(() => {
					const modalEl = document.getElementById('responseModal');
					const modalInstance = bootstrap.Modal.getInstance(modalEl);
					if (modalInstance) modalInstance.hide();
				}, window.kioskTimeout || 8000);
			})
			.catch(error => {
				userNumber.value = '';
		
				console.error('Error:', error);
				const modalMessage = document.getElementById('modalMessage');
				modalMessage.textContent = 'Error checking shift status. Please try again.';
		
				const responseModal = new bootstrap.Modal(document.getElementById('responseModal'));
				responseModal.show();
		
				setTimeout(() => {
					const modalEl = document.getElementById('responseModal');
					const modalInstance = bootstrap.Modal.getInstance(modalEl);
					if (modalInstance) modalInstance.hide();
				}, window.kioskTimeout || 8000);
			});
		});
	}
	
	// Handle new random tap-in code button 
	const generateCodeBtn = document.getElementById('generateCodeBtn');
	if (generateCodeBtn) {
		document.getElementById('generateCodeBtn').addEventListener('click', function () {
			
			fetch('actions/generate_staff_code.php')
				.then(response => response.json()) // or .json() if you're returning JSON
				.then(data => {
					document.getElementById('code').value = data.code; // or data.code if using JSON
				})
				.catch(error => {
					console.error('Error fetching code:', error);
					alert('Could not generate a new code at this time.');
				});
		});
	}
});