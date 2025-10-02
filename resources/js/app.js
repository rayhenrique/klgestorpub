import './bootstrap';

// Import FontAwesome CSS
import '@fortawesome/fontawesome-free/css/all.css';

// Import SweetAlert2
import Swal from 'sweetalert2';
window.Swal = Swal;

// Import Chart.js
import Chart from 'chart.js/auto';
window.Chart = Chart;

// Import jQuery
import $ from 'jquery';
window.$ = window.jQuery = $;

// Import jQuery Mask Plugin
import 'jquery-mask-plugin';

// Add modern interactivity
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips if Bootstrap is available
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Add smooth scrolling for anchor links (robust against href="#")
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = (this.getAttribute('href') || '').trim();
            // Ignore empty or just '#'
            if (!href || href === '#') {
                return; // let default or do nothing
            }
            // Resolve target by id to avoid invalid querySelector
            const id = href.startsWith('#') ? href.slice(1) : href;
            const target = document.getElementById(id);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    // Add loading states for forms
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processando...';
            }
        });
    });
});