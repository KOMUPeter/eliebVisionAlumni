function togglePasswordVisibility(passwordFieldId, toggleIconId) {
    console.log('togglePasswordVisibility called');
    var passwordField = document.getElementById(passwordFieldId);
    var toggleIcon = document.getElementById(toggleIconId);
    
    if (passwordField) {
        var passwordType = passwordField.type === 'password' ? 'text' : 'password';
        passwordField.type = passwordType;
        toggleIcon.classList.toggle('fa-eye');
        toggleIcon.classList.toggle('fa-eye-slash');
    } else {
        console.error(`Element with ID ${passwordFieldId} not found.`);
    }
}

// hide changing password fields in my account
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('passwordForm');
    var changePasswordLink = document.getElementById('changePasswordLink');
    var passwordHeading = document.getElementById('passwordHeading');

    // Toggle visibility of the password change form
    function togglePasswordForm(event) {
        event.preventDefault(); // Prevent the default link behavior
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
            passwordHeading.innerHTML = 'Enter the new password';
        } else {
            form.style.display = 'none';
            passwordHeading.innerHTML = '<a href="#" id="changePasswordLink">Click to Change Password?</a>';
        }
    }

    changePasswordLink.addEventListener('click', togglePasswordForm);

    // Ensure the form is validated before submission
    form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            // You can also add custom error handling here
        }
        form.classList.add('was-validated'); // Bootstrap example
    });
});

// Toggle visibility of the password input field
function togglePasswordVisibility(inputId, iconId) {
    var input = document.getElementById(inputId);
    var icon = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
