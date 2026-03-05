/**
 * Main JavaScript File
 * Jasaku - Platform Manajemen Bisnis Jasa
 */

(function($) {
    'use strict';

    // Initialize tooltips
    function initTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Initialize popovers
    function initPopovers() {
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function(popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    }

    // Format currency input
    function formatCurrencyInput(element) {
        let value = element.value.replace(/[^\d]/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
            element.value = 'Rp ' + value;
        }
    }

    // Parse currency value
    function parseCurrency(value) {
        return parseFloat(value.replace(/[^\d]/g, '')) || 0;
    }

    // Confirm delete action
    window.confirmDelete = function(message) {
        return confirm(message || 'Apakah Anda yakin ingin menghapus item ini?');
    };

    // Show loading spinner
    window.showLoading = function(button) {
        const originalText = button.innerHTML;
        button.setAttribute('data-original-text', originalText);
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
        button.disabled = true;
    };

    // Hide loading spinner
    window.hideLoading = function(button) {
        const originalText = button.getAttribute('data-original-text');
        if (originalText) {
            button.innerHTML = originalText;
        }
        button.disabled = false;
    };

    // Auto-dismiss alerts
    function autoDismissAlerts() {
        $('.alert').each(function() {
            const alert = $(this);
            if (!alert.hasClass('alert-permanent')) {
                setTimeout(function() {
                    alert.fadeOut('slow', function() {
                        $(this).remove();
                    });
                }, 5000);
            }
        });
    }

    // Search/Filter functionality
    window.filterTable = function(input, tableId) {
        const filter = input.value.toUpperCase();
        const table = document.getElementById(tableId);
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            let showRow = false;
            const cells = rows[i].getElementsByTagName('td');
            
            for (let j = 0; j < cells.length; j++) {
                const cell = cells[j];
                if (cell) {
                    const textValue = cell.textContent || cell.innerText;
                    if (textValue.toUpperCase().indexOf(filter) > -1) {
                        showRow = true;
                        break;
                    }
                }
            }
            
            rows[i].style.display = showRow ? '' : 'none';
        }
    };

    // Sort table
    window.sortTable = function(columnIndex, tableId) {
        const table = document.getElementById(tableId);
        const tbody = table.getElementsByTagName('tbody')[0];
        const rows = Array.from(tbody.getElementsByTagName('tr'));
        
        const isAscending = table.getAttribute('data-sort-order') !== 'asc';
        table.setAttribute('data-sort-order', isAscending ? 'asc' : 'desc');
        
        rows.sort((a, b) => {
            const aValue = a.getElementsByTagName('td')[columnIndex].textContent.trim();
            const bValue = b.getElementsByTagName('td')[columnIndex].textContent.trim();
            
            // Try to parse as number
            const aNum = parseFloat(aValue.replace(/[^\d.-]/g, ''));
            const bNum = parseFloat(bValue.replace(/[^\d.-]/g, ''));
            
            if (!isNaN(aNum) && !isNaN(bNum)) {
                return isAscending ? aNum - bNum : bNum - aNum;
            }
            
            // Sort as string
            return isAscending 
                ? aValue.localeCompare(bValue)
                : bValue.localeCompare(aValue);
        });
        
        // Rebuild table
        rows.forEach(row => tbody.appendChild(row));
    };

    // Copy to clipboard
    window.copyToClipboard = function(text, button) {
        navigator.clipboard.writeText(text).then(function() {
            const originalHtml = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
            button.classList.remove('btn-outline-secondary');
            button.classList.add('btn-success');
            
            setTimeout(function() {
                button.innerHTML = originalHtml;
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-secondary');
            }, 2000);
        }).catch(function(err) {
            console.error('Failed to copy:', err);
        });
    };

    // Print function
    window.printPage = function() {
        window.print();
    };

    // Export to CSV
    window.exportTableToCSV = function(tableId, filename) {
        const table = document.getElementById(tableId);
        const rows = table.querySelectorAll('tr');
        let csv = [];
        
        for (let i = 0; i < rows.length; i++) {
            const row = [];
            const cols = rows[i].querySelectorAll('td, th');
            
            for (let j = 0; j < cols.length; j++) {
                let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').replace(/(\s\s)/gm, ' ');
                data = data.replace(/"/g, '""');
                row.push('"' + data + '"');
            }
            
            csv.push(row.join(','));
        }
        
        downloadCSV(csv.join('\n'), filename);
    };

    function downloadCSV(csv, filename) {
        const csvFile = new Blob([csv], { type: 'text/csv' });
        const downloadLink = document.createElement('a');
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = 'none';
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }

    // Number animation
    window.animateNumber = function(element, start, end, duration) {
        const range = end - start;
        const increment = range / (duration / 16);
        let current = start;
        
        const timer = setInterval(function() {
            current += increment;
            if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                current = end;
                clearInterval(timer);
            }
            element.textContent = Math.round(current).toLocaleString('id-ID');
        }, 16);
    };

    // Form validation
    function validateForm(form) {
        let isValid = true;
        
        $(form).find('[required]').each(function() {
            const field = $(this);
            const value = field.val().trim();
            
            if (!value) {
                isValid = false;
                field.addClass('is-invalid');
                
                if (!field.next('.invalid-feedback').length) {
                    field.after('<div class="invalid-feedback">Field ini wajib diisi.</div>');
                }
            } else {
                field.removeClass('is-invalid');
                field.next('.invalid-feedback').remove();
            }
        });
        
        return isValid;
    }

    // Initialize on document ready
    $(document).ready(function() {
        // Initialize Bootstrap components
        initTooltips();
        initPopovers();
        
        // Auto-dismiss alerts
        autoDismissAlerts();
        
        // Currency input formatting
        $('.currency-input').on('blur', function() {
            formatCurrencyInput(this);
        });
        
        // Form validation
        $('form').on('submit', function(e) {
            if ($(this).hasClass('needs-validation')) {
                if (!validateForm(this)) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            }
        });
        
        // Remove invalid class on input
        $('.form-control, .form-select').on('input change', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        });
        
        // Confirm delete links
        $('.delete-link, .btn-delete').on('click', function(e) {
            if (!confirm('Apakah Anda yakin ingin menghapus item ini?')) {
                e.preventDefault();
                return false;
            }
        });
        
        // Auto-focus first input in modals
        $('.modal').on('shown.bs.modal', function() {
            $(this).find('input:first').focus();
        });
        
        // Print button
        $('.btn-print').on('click', function() {
            window.print();
        });
        
        // Back button
        $('.btn-back').on('click', function() {
            window.history.back();
        });
    });

    // Chart utilities
    window.ChartUtils = {
        // Chart colors
        colors: {
            primary: '#4F46E5',
            success: '#10B981',
            danger: '#EF4444',
            warning: '#F59E0B',
            info: '#3B82F6',
            secondary: '#6B7280'
        },
        
        // Default chart options
        defaultOptions: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 13,
                        weight: '600'
                    },
                    bodyFont: {
                        size: 12
                    },
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1
                }
            }
        },
        
        // Format currency for charts
        formatCurrency: function(value) {
            return 'Rp ' + value.toLocaleString('id-ID');
        },
        
        // Format large numbers
        formatNumber: function(value) {
            if (value >= 1000000) {
                return (value / 1000000).toFixed(1) + 'M';
            } else if (value >= 1000) {
                return (value / 1000).toFixed(1) + 'K';
            }
            return value.toString();
        }
    };

})(jQuery);
