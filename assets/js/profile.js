// Theme Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
    const themeSwitch = document.getElementById('theme-switch');
    const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
    
    // Check for saved theme preference or use OS preference
    const currentTheme = localStorage.getItem('theme') || 
                        (prefersDarkScheme.matches ? 'dark' : 'light');
    
    if (currentTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        themeSwitch.checked = true;
    }
    
    // Theme switch event listener
    themeSwitch.addEventListener('change', function() {
        if (this.checked) {
            document.documentElement.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.setAttribute('data-theme', 'light');
            localStorage.setItem('theme', 'light');
        }
    });
    
    // Add loading animation to cards
    const cards = document.querySelectorAll('.profile-card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });
    
    // Add hover effects to interactive elements
    const interactiveElements = document.querySelectorAll('.btn, .menu-item, .info-item');
    interactiveElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        element.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Print functionality
    const printButton = document.createElement('button');
    printButton.className = 'btn btn-outline';
    printButton.innerHTML = '<i class="fas fa-print"></i> In hồ sơ';
    printButton.style.marginLeft = '1rem';
    printButton.addEventListener('click', function() {
        window.print();
    });
    
    const cardFooter = document.querySelector('.personal-info .card-footer');
    if (cardFooter) {
        cardFooter.querySelector('.action-form').appendChild(printButton);
    }
});