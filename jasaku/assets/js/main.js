/**
 * Jasaku - Main JavaScript
 */

$(document).ready(function() {
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Delete confirmation
    $('.btn-delete').on('click', function(e) {
        if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            e.preventDefault();
        }
    });
    
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // Search functionality
    $('.search-input').on('keyup', function() {
        const searchValue = $(this).val().toLowerCase();
        const targetTable = $(this).data('table');
        
        if (targetTable) {
            $(targetTable + ' tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(searchValue) > -1);
            });
        }
    });
    
    // Calculate final value when deal value or discount changes
    $('#deal_value, #discount_percent').on('input', function() {
        const dealValue = parseFloat($('#deal_value').val()) || 0;
        const discount = parseFloat($('#discount_percent').val()) || 0;
        const finalValue = dealValue - (dealValue * discount / 100);
        $('#final_value_display').text(formatCurrency(finalValue));
    });
    
    // Service price auto-fill
    $('#service_package_id').on('change', function() {
        const price = $(this).find(':selected').data('price') || 0;
        const currentValue = $('#deal_value').val();
        if (!currentValue) {
            $('#deal_value').val(price);
            $('#deal_value').trigger('input');
        }
    });
});

// Format currency
function formatCurrency(amount) {
    return 'Rp ' + amount.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// Show notification
function showNotification(message, type = 'success') {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const html = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;
    
    $('.container-fluid').prepend(html);
    
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
}

// Update deal stage via AJAX
function updateDealStage(dealId, newStage) {
    $.ajax({
        url: '/jasaku/includes/ajax-handler.php',
        type: 'POST',
        data: {
            action: 'update_deal_stage',
            deal_id: dealId,
            stage: newStage
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.message || 'Gagal mengupdate stage');
            }
        },
        error: function() {
            alert('Terjadi kesalahan. Silakan coba lagi.');
        }
    });
}

// Delete record via AJAX
function deleteRecord(type, id) {
    if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        return;
    }
    
    $.ajax({
        url: '/jasaku/includes/ajax-handler.php',
        type: 'POST',
        data: {
            action: 'delete_' + type,
            id: id
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.message || 'Gagal menghapus data');
            }
        },
        error: function() {
            alert('Terjadi kesalahan. Silakan coba lagi.');
        }
    });
}

// Initialize chart
function initChart(canvasId, type, data, options) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return null;
    
    return new Chart(ctx, {
        type: type,
        data: data,
        options: options || {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}
