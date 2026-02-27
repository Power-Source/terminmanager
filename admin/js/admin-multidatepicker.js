/**
 * Multi-Date Picker using Flatpickr
 * Migrated from jQuery UI Datepicker - no jQuery UI dependency
 */
const AppDatepicker = function( $element ) {
    let selectedDates = [];
    let rel;
    let flatpickrInstance;

    const datepicker = {
        init: function( $el ) {
            const self = this;
            
            // Get related hidden input
            rel = jQuery($el.data('rel'));
            
            // Parse existing dates
            const existingDates = rel.val().split(',').filter(function(d) { return d.trim(); });
            selectedDates = existingDates.slice();
            
            // Initialize Flatpickr with multi-date support
            flatpickrInstance = flatpickr($el[0], {
                mode: 'multiple',
                dateFormat: 'Y-m-d',
                inline: true,
                defaultDate: selectedDates,
                showMonths: 2,
                onChange: function(dates, dateStr, instance) {
                    // Update selectedDates array
                    selectedDates = dates.map(function(date) {
                        const year = date.getFullYear();
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const day = String(date.getDate()).padStart(2, '0');
                        return year + '-' + month + '-' + day;
                    });
                    self.updateRel();
                }
            });
        },
        toggleDate: function( date ) {
            const index = selectedDates.indexOf(date);
            if (index > -1) {
                selectedDates.splice(index, 1);
            } else {
                selectedDates.push(date);
            }
            
            if (flatpickrInstance) {
                flatpickrInstance.setDate(selectedDates, false);
            }
            this.updateRel();
        },
        updateRel: function() {
            rel.val(this.getDates().join(','));
        },
        getDates: function() {
            return selectedDates;
        }
    };

    datepicker.init( $element );
    return datepicker;
};

window.AppDatepicker = AppDatepicker;

