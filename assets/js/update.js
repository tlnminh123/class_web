document.addEventListener('DOMContentLoaded', function() {
    // Form elements
    const form = document.getElementById('updateForm');
    const submitBtn = document.getElementById('submitBtn');
    const notification = document.getElementById('notification');
    
    // Form validation
    function validateForm() {
        let isValid = true;
        
        // Validate name
        const nameInput = document.getElementById('name');
        const nameError = document.getElementById('nameError');
        if (!nameInput.value.trim()) {
            showError(nameInput, nameError, 'Vui lòng nhập họ và tên');
            isValid = false;
        } else if (nameInput.value.trim().length < 2) {
            showError(nameInput, nameError, 'Họ và tên phải có ít nhất 2 ký tự');
            isValid = false;
        } else {
            clearError(nameInput, nameError);
        }
        
        // Validate date of birth
        const dobInput = document.getElementById('dob');
        const dobError = document.getElementById('dobError');
        if (!dobInput.value) {
            showError(dobInput, dobError, 'Vui lòng chọn ngày sinh');
            isValid = false;
        } else {
            const dob = new Date(dobInput.value);
            const today = new Date();
            const minDate = new Date();
            minDate.setFullYear(today.getFullYear() - 100);
            const maxDate = new Date();
            maxDate.setFullYear(today.getFullYear() - 10);
            
            if (dob < minDate || dob > maxDate) {
                showError(dobInput, dobError, 'Ngày sinh không hợp lệ');
                isValid = false;
            } else {
                clearError(dobInput, dobError);
            }
        }
        
        // Validate email
        const emailInput = document.getElementById('email');
        const emailError = document.getElementById('emailError');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!emailInput.value.trim()) {
            showError(emailInput, emailError, 'Vui lòng nhập email');
            isValid = false;
        } else if (!emailRegex.test(emailInput.value.trim())) {
            showError(emailInput, emailError, 'Email không hợp lệ');
            isValid = false;
        } else {
            clearError(emailInput, emailError);
        }
        
        return isValid;
    }
    
    function showError(input, errorElement, message) {
        input.classList.add('invalid');
        input.classList.remove('valid');
        errorElement.textContent = message;
        errorElement.classList.add('show');
    }
    
    function clearError(input, errorElement) {
        input.classList.remove('invalid');
        input.classList.add('valid');
        errorElement.textContent = '';
        errorElement.classList.remove('show');
    }
    
    // Real-time validation
    const inputs = document.querySelectorAll('.form-input');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateForm();
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('invalid')) {
                const fieldName = this.id;
                const errorElement = document.getElementById(fieldName + 'Error');
                clearError(this, errorElement);
            }
        });
    });
    
    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validateForm()) {
            // Show loading state
            submitBtn.classList.add('loading');
            
            // Simulate form submission (replace with actual AJAX if needed)
            setTimeout(() => {
                // Submit the form normally
                form.submit();
            }, 1500);
        } else {
            // Show error notification
            showNotification('Vui lòng kiểm tra lại thông tin', 'error');
            
            // Shake animation for invalid form
            form.classList.add('shake');
            setTimeout(() => {
                form.classList.remove('shake');
            }, 500);
        }
    });
    
    // Notification system
    function showNotification(message, type = 'info') {
        const notificationIcon = notification.querySelector('.notification-icon');
        const notificationMessage = notification.querySelector('.notification-message');
        
        // Set message
        notificationMessage.textContent = message;
        
        // Set type (color)
        notification.className = 'notification';
        notification.classList.add(type);
        
        // Show notification
        notification.classList.add('show');
        
        // Auto hide after 5 seconds
        setTimeout(() => {
            notification.classList.remove('show');
        }, 5000);
    }
    
    // Close notification when clicked
    notification.addEventListener('click', function() {
        this.classList.remove('show');
    });
    
    // Date input formatting (for better UX)
    const dobInput = document.getElementById('dob');
    const today = new Date();
    const maxDate = new Date(today.getFullYear() - 10, today.getMonth(), today.getDate());
    const minDate = new Date(today.getFullYear() - 100, today.getMonth(), today.getDate());
    
    dobInput.max = maxDate.toISOString().split('T')[0];
    dobInput.min = minDate.toISOString().split('T')[0];
    
   
});