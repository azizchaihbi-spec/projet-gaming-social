// Shared utilities and functions
document.addEventListener('DOMContentLoaded', () => {
    // Add any shared JavaScript functionality here
    console.log('Dashboard loaded');
});

// Animation for stat cards
const animateStatCards = () => {
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        card.style.transition = 'transform 0.3s ease, box-shadow 0.3s ease';
        card.style.transform = 'translateY(0)';
        
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-5px)';
            card.style.boxShadow = '0 10px 15px rgba(16, 185, 129, 0.3)';
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0)';
            card.style.boxShadow = '0 0 10px rgba(16, 185, 129, 0.2)';
        });
    });
};

// Initialize animations when page loads
window.addEventListener('load', () => {
    animateStatCards();
});
