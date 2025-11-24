// Tenant Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Navigation functionality
    const navLinks = document.querySelectorAll('.nav-link');
    const contentSections = document.querySelectorAll('.content-section');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all links and sections
            navLinks.forEach(l => l.classList.remove('active'));
            contentSections.forEach(s => s.classList.remove('active'));
            
            // Add active class to clicked link
            this.classList.add('active');
            
            // Show corresponding section
            const sectionId = this.getAttribute('data-section');
            const targetSection = document.getElementById(sectionId);
            if (targetSection) {
                targetSection.classList.add('active');
            }
        });
    });
    
    // Mobile menu toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
        });
    }
    
    // Profile menu toggle
    const profile = document.querySelector('.profile');
    const profileMenu = document.querySelector('.profile-menu');
    
    if (profile && profileMenu) {
        profile.addEventListener('click', function() {
            profileMenu.classList.toggle('show');
        });
        
        // Close profile menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!profile.contains(e.target)) {
                profileMenu.classList.remove('show');
            }
        });
    }
    
    // Maintenance form submission
    const maintenanceForm = document.querySelector('.maintenance-form');
    if (maintenanceForm) {
        maintenanceForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const issueType = document.getElementById('issue-type').value;
            const description = document.getElementById('description').value;
            const urgency = document.getElementById('urgency').value;
            
            // In a real app, you would send this to your backend
            console.log('Maintenance request submitted:', {
                issueType,
                description,
                urgency
            });
            
            // Show success message (in a real app)
            alert('Maintenance request submitted successfully!');
            
            // Reset form
            maintenanceForm.reset();
        });
    }
    
    // Logout functionality
    const logoutLink = document.querySelector('.logout-link');
    if (logoutLink) {
        logoutLink.addEventListener('click', function(e) {
            e.preventDefault();
            // In a real app, this would trigger your logout process
            if (confirm('Are you sure you want to logout?')) {
                // Redirect to logout or perform logout action
                console.log('Logging out...');
            }
        });
    }
});