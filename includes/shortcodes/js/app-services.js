jQuery(document).ready(function($) {
    "use strict";

    window.Appointments = window.Appointments || {};
    Appointments.shortcodes = Appointments.shortcodes || {};

    Appointments.shortcodes.services = {
        thumbnailsCache: [],
        strings: null,

        init: function() {
            const self = this;
            this.strings = appointmentsStrings;
            this.$servicesSelector = $('.app_select_services');
            this.$submitButton = $('.app_services_button');

            if ( this.strings.autorefresh == '1' ) {
                this.$submitButton.hide();
            }

            this.bind();
        },

        bind: function() {
            const self = this;

            this.$submitButton.on("click", function(){
                let selected_service = self.$servicesSelector.val();
                if ( typeof selected_service==='undefined' || selected_service===null ) {
                    selected_service=self.strings.first_service_id;
                }

                self.reload( selected_service );
            });

            this.$servicesSelector.on('change', function(){
                const selected_service = $(this).val();

                if ( self.strings.autorefresh == '1' ) {
                    self.$submitButton.trigger( 'click' );
                    return false;
                }

                $('.app_service_excerpt').hide();
                $('#app_service_excerpt_'+selected_service).show();
            });
        },

        reload: function( service ) {
            window.location.href = this.strings.reload_url.replace('__selected_service__', service);
        }

    };

    Appointments.shortcodes.services.init();
});