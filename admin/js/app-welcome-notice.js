/**
 * Appointments+ Welcome Notice
 * Simple notice system to replace jQuery UI pointer tutorials
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Handle dismiss button
        $('.app-welcome-notice .notice-dismiss').on('click', function() {
            var $notice = $(this).closest('.app-welcome-notice');
            var noticeId = $notice.data('notice-id');
            
            $.post(ajaxurl, {
                action: 'app_dismiss_welcome_notice',
                notice_id: noticeId,
                nonce: appWelcomeNotice.nonce
            });
        });

        // Handle quick setup button
        $('.app-welcome-notice .app-quick-setup').on('click', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            
            // Dismiss notice and redirect
            var $notice = $(this).closest('.app-welcome-notice');
            var noticeId = $notice.data('notice-id');
            
            $.post(ajaxurl, {
                action: 'app_dismiss_welcome_notice',
                notice_id: noticeId,
                nonce: appWelcomeNotice.nonce
            }, function() {
                window.location.href = url;
            });
        });
    });

})(jQuery);
