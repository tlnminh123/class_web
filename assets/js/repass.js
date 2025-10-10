// Enhanced Password Validation
document.addEventListener('DOMContentLoaded', function() {
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    const strengthBar = document.querySelector('.strength-bar');
    const strengthText = document.querySelector('.strength-text');
    const submitButton = document.querySelector('.submitButton');
    
    // Password strength checker
    newPassword.addEventListener('input', function() {
        const password = this.value;
        const strength = calculatePasswordStrength(password);
        
        strengthBar.className = 'strength-bar ' + strength.level;
        strengthBar.style.width = strength.percentage + '%';
        strengthText.textContent = strength.text;
        strengthText.style.color = strength.color;
    });
    
    // Password confirmation validation
    confirmPassword.addEventListener('input', function() {
        validatePasswordMatch();
    });
    
    function calculatePasswordStrength(password) {
        let score = 0;
        const requirements = {
            length: password.length >= 8,
            lowercase: /[a-z]/.test(password),
            uppercase: /[A-Z]/.test(password),
            numbers: /\d/.test(password),
            special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
        };
        
        // Calculate score
        if (requirements.length) score += 20;
        if (requirements.lowercase) score += 20;
        if (requirements.uppercase) score += 20;
        if (requirements.numbers) score += 20;
        if (requirements.special) score += 20;
        
        // Determine strength level
        if (score >= 80) {
            return {
                level: 'strong',
                percentage: 100,
                text: 'Mật khẩu mạnh',
                color: 'var(--success-color)'
            };
        } else if (score >= 60) {
            return {
                level: 'medium',
                percentage: 66,
                text: 'Mật khẩu trung bình',
                color: 'var(--warning-color)'
            };
        } else if (score >= 40) {
            return {
                level: 'weak',
                percentage: 33,
                text: 'Mật khẩu yếu',
                color: 'var(--error-color)'
            };
        } else {
            return {
                level: '',
                percentage: 0,
                text: 'Nhập mật khẩu',
                color: 'var(--text-muted)'
            };
        }
    }
    
    function validatePasswordMatch() {
        if (newPassword.value !== confirmPassword.value && confirmPassword.value !== '') {
            confirmPassword.style.borderColor = 'var(--error-color)';
            confirmPassword.setCustomValidity('Mật khẩu xác nhận không khớp');
        } else {
            confirmPassword.style.borderColor = 'var(--success-color)';
            confirmPassword.setCustomValidity('');
        }
    }
    
    // Theme toggle functionality
    const themeToggle = document.querySelector('.theme-toggle');
    themeToggle.addEventListener('click', function() {
        this.classList.toggle('active');
        const isDark = this.classList.contains('active');
        document.documentElement.setAttribute('data-theme', isDark ? 'dark' : 'light');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    });
    
    // Initialize theme
    const savedTheme = localStorage.getItem('theme') || 
                      (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    if (savedTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        themeToggle.classList.add('active');
    }
});