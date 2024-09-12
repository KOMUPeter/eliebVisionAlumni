// Ensure the form is hidden initially
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('passwordForm');
    if (form) {
        form.style.display = 'none';
    }
});


// Function to toggle the visibility of the password change form
function togglePasswordForm(event) {
    event.preventDefault();
    var form = document.getElementById('passwordForm');
    if (form) {
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    } else {
        console.error('Password form not found.');
    }
}

// Function to toggle the visibility of the password input fields
function togglePasswordVisibility(fieldId, iconId) {
    var passwordField = document.getElementById(fieldId);
    var icon = document.getElementById(iconId);

    if (passwordField && icon) {
        if (passwordField.type === "password") {
            passwordField.type = "text";
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = "password";
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    } else {
        console.error('Password field or icon not found.');
    }
}

// myaccount password change message show delay time
document.addEventListener("DOMContentLoaded", function() {
    setTimeout(function() {
        var alerts = document.querySelectorAll('.myaccount .alert');
        alerts.forEach(function(alert) {
            alert.style.opacity = 0;
            setTimeout(function() {
                alert.style.display = 'none';
            }, 1000); 
        });
    }, 5000); 
});
