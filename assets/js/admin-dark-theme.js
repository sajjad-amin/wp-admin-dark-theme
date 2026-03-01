jQuery(document).ready(function($) {
    $('#wp-admin-bar-wpadt-toggle a').on('click', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        
        // Visual feedback to show it's working
        $button.css('opacity', '0.5');

        $.ajax({
            url: wpadtSettings.ajaxurl,
            type: 'POST',
            data: {
                action: 'wpadt_toggle_theme',
                nonce: wpadtSettings.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Reload to cleanly apply/remove the CSS across the whole dashboard and iframes
                    window.location.reload();
                } else {
                    $button.css('opacity', '1');
                    alert('Error toggling dark theme.');
                }
            },
            error: function() {
                $button.css('opacity', '1');
                alert('Connection error toggling dark theme.');
            }
        });
    });
});
