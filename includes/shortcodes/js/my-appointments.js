Appointments = window.Appointments || {};

( function( global, strings, $ ) {
    "use strict";

    /**
     * Constructor
     *
     * @param options
     * @returns {MyAppointments}
     * @constructor
     */
    function MyAppointments( options ) {
        $(".app-my-appointments-cancel").on('change',  function(e) {
            const $target = $( e.target );
            const cancelAppointment = this.cancelAppointment;
            if ( $target.is( ':checked' ) ) {
                swal({
                        title: strings.aysCancel,
                        text: "",
                        type: "warning",
                        showCancelButton: true,
                        cancelButtonText: strings.no,
                        confirmButtonText: strings.yes,
                        closeOnConfirm: true
                    },
                    function( isConfirm ){
                        if ( ! isConfirm ) {
                            $target.attr("checked", false);
                        }
                        else {
                            cancelAppointment( $target.data( 'app-id' ) );
                        }
                    });

            }
        }.bind( this ));
        return this;
    }

    MyAppointments.prototype.cancelAppointment = function( appId ) {
        const data = {
            action: "cancel_user_app",
            app_id: appId,
            cancel_nonce: strings.nonce
        };

        $.post(strings.ajaxurl, data, function(response) {
            const cancel_box = $('input#cancel-' + appId );
            if ( typeof response.success === 'undefined' ) {
                cancel_box.attr("disabled",true);
                swal( strings.connectionError, '', 'error' );
                return;
            }

            if ( response.success ) {
                swal( strings.cancelled, '', 'success' );
                cancel_box.closest("tr").css("opacity","0.3");
                cancel_box.attr("disabled",true);
            }
            else {
                swal(response.data);
            }
        }, "json");
    };



    /**
     * Helper constructor function
     *
     * @param options
     * @returns {MyAppointments}
     */
    global.myAppointments = function( options ) {
        return new MyAppointments( options );
    };


	$('body').on( 'click', '.appointments-paid-button', function() {
		const appointment_data = $(this).data('appointment');
		if ( !appointment_data || !appointment_data.ID || !appointment_data.price || !appointment_data.service_name )
			{return false;}
		const post_data = {
			price: appointment_data.price,
			app_id: appointment_data.ID,
			service_name: appointment_data.service_name
		};
		$(".appointments-paypal").find(".app_amount").val(post_data.price);
		$(".appointments-paypal").find(".app_custom").val(post_data.app_id);
		const old_val = $(".appointments-paypal").find(".app_submit_btn").val();
		if ( old_val ) {
			const new_val = old_val.replace("PRICE",post_data.price).replace("SERVICE",post_data.service_name);
			$(".appointments-paypal").find(".app_submit_btn").val(new_val);
			const old_val2 = $(".appointments-paypal").find(".app_item_name").val();
			const new_val2 = old_val2.replace("SERVICE",post_data.service_name);
			$(".appointments-paypal").find(".app_item_name").val(new_val2);
			$(".appointments-paypal .app_submit_btn").focus();
		}

		$(".appointments-paypal").find("form").submit();
	});

})( Appointments, appMyAppointmentsStrings, jQuery );