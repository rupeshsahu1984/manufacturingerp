// Main ERP Application JavaScript

document.addEventListener('DOMContentLoaded', function() {
    
    // Sidebar Accordion Functionality
    const accordionHeaders = document.querySelectorAll('.sidebar-accordion-header');
    
    // Load saved accordion states
    accordionHeaders.forEach(function(header) {
        const target = header.getAttribute('data-target');
        const collapse = document.querySelector(target);
        const savedState = localStorage.getItem('accordion_' + target.replace('#', ''));
        
        if (savedState === 'true' && collapse) {
            header.setAttribute('aria-expanded', 'true');
            collapse.classList.add('show');
            const icon = header.querySelector('.accordion-icon');
            if (icon) icon.style.transform = 'rotate(180deg)';
        }
    });
    
    accordionHeaders.forEach(function(header) {
        header.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const target = this.getAttribute('data-target');
            const collapse = document.querySelector(target);
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            
            if (!collapse) {
                console.error('Target collapse element not found:', target);
                return;
            }
            
            // Toggle current accordion
            this.setAttribute('aria-expanded', !isExpanded);
            const icon = this.querySelector('.accordion-icon');
            
            if (isExpanded) {
                collapse.classList.remove('show');
                if (icon) icon.style.transform = 'rotate(0deg)';
                localStorage.setItem('accordion_' + target.replace('#', ''), 'false');
            } else {
                collapse.classList.add('show');
                if (icon) icon.style.transform = 'rotate(180deg)';
                localStorage.setItem('accordion_' + target.replace('#', ''), 'true');
            }
            
            // Force reflow to ensure animation works
            collapse.offsetHeight;
        });
    });
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert.parentNode) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(function() {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 500);
            }
        }, 5000);
    });
    
    // Form validation enhancement
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                event.preventDefault();
                showNotification('Please fill in all required fields.', 'error');
            }
        });
    });
    
    // Search functionality
    const searchInputs = document.querySelectorAll('.search-input');
    searchInputs.forEach(function(input) {
        let searchTimeout;
        input.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                performSearch(input.value, input.dataset.target);
            }, 300);
        });
    });
    
    // Table row selection
    const selectableRows = document.querySelectorAll('.table tbody tr[data-selectable]');
    selectableRows.forEach(function(row) {
        row.addEventListener('click', function() {
            this.classList.toggle('selected');
        });
    });
    
    // Bulk actions
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });
    }
});

// Utility Functions
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(function() {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

function performSearch(query, target) {
    // This function can be customized based on the search requirements
    console.log('Searching for:', query, 'in target:', target);
    
    // Example: Filter table rows
    if (target) {
        const table = document.querySelector(target);
        if (table) {
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(function(row) {
                const text = row.textContent.toLowerCase();
                if (text.includes(query.toLowerCase())) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    }
}

function toggleStatus(itemId, type) {
    if (confirm('Are you sure you want to toggle the status?')) {
        console.log('Toggling status for:', type, itemId);
        fetch(`${baseUrl}/${type}/toggle-status/${itemId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                location.reload();
            } else {
                showNotification('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while updating the status.', 'error');
        });
    }
}

function deleteItem(itemId, type) {
    if (confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
        console.log('Deleting item:', type, itemId);
        fetch(`${baseUrl}/${type}/delete/${itemId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                showNotification('Item deleted successfully.', 'success');
                location.reload();
            } else {
                showNotification('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while deleting the item.', 'error');
        });
    }
}

function exportData(type, format = 'csv') {
    const filters = new URLSearchParams(window.location.search);
    const exportUrl = `${baseUrl}/${type}/export?${filters.toString()}&format=${format}`;
    window.location.href = exportUrl;
}



// Global variables
const baseUrl = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, ''); 