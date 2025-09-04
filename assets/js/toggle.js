jQuery(document).ready(function($) {
    // Move toggle buttons to the service foot section
    function moveToggleButtonsToFoot() {
        $('.lp-toggle-service').each(function() {
            var button = $(this);
            var serviceTile = button.closest('.os-service');
            var footSection = serviceTile.find('.os-service-foot');
            
            if (footSection.length && !button.closest('.os-service-foot').length) {
                // Move button to foot section and add proper styling
                button.appendTo(footSection);
                
                // Apply proper button classes based on status
                var status = button.data('status');
                button.removeClass('latepoint-btn-primary latepoint-btn-warning');
                
                if (status === 'active') {
                    button.addClass('latepoint-btn-warning');
                } else {
                    button.addClass('latepoint-btn-primary');
                }
            }
        });
    }
    
    // Initial move
    moveToggleButtonsToFoot();
    
    // Also move buttons after AJAX operations (if page doesn't reload)
    $(document).on('click', '.lp-toggle-service', function(e) {
        e.preventDefault();
        
        var btn = $(this);
        var serviceId = btn.data('id');
        
        // Show loading state
        var originalText = btn.text();
        btn.text('...').prop('disabled', true);
        
        $.post(LPServiceToggle.ajax_url, {
            action: 'lp_toggle_service',
            service_id: serviceId,
            nonce: LPServiceToggle.nonce
        }, function(response) {
            btn.prop('disabled', false);
            
            if (response.success) {
                // Update button text and data-status
                btn.text(response.data.button_text);
                btn.data('status', response.data.new_status);
                
                // Update button classes based on new status
                btn.removeClass('latepoint-btn-primary latepoint-btn-warning');
                if (response.data.new_status === 'active') {
                    btn.addClass('latepoint-btn-warning');
                } else {
                    btn.addClass('latepoint-btn-primary');
                }
                
                // Re-move button to ensure it's in the right place
                moveToggleButtonsToFoot();
                
                // Optional: Show success message
                if (typeof OsNotificationHelper !== 'undefined') {
                    OsNotificationHelper.show(response.data.message, 'success');
                } else {
                    // Fallback notification
                    alert(response.data.message);
                }
                
                // Optional: Reload the page to reflect changes in service listings
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                // Restore original text and show error
                btn.text(originalText);
                if (typeof OsNotificationHelper !== 'undefined') {
                    OsNotificationHelper.show(response.data.message, 'error');
                } else {
                    alert(response.data.message);
                }
            }
        }).fail(function() {
            btn.prop('disabled', false);
            btn.text(originalText);
            if (typeof OsNotificationHelper !== 'undefined') {
                OsNotificationHelper.show('Network error occurred', 'error');
            } else {
                alert('Network error occurred');
            }
        });
    });
});
