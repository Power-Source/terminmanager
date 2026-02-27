"use strict";
( function () {

    tinymce.PluginManager.add( 'appointments_shortcodes', function ( editor ) {

        const datepickers = [];

        /**
         * Generate the content for a shortcode popup
         *
         * @param shortcode
         * @returns {{text: *, onclick: onclick}}
         */
        function appointments_shortcode_item( shortcode ) {
            let body = [],
                field;

            for ( const i in shortcode.defaults ) {
                field = appointments_shortcode_field( i, shortcode.defaults[i] );
                if ( field ) {
                    body.push( field );
                }
            }

            return {
                text: shortcode.name,
                onclick: function () {
                    editor.windowManager.open({
                        title: shortcode.name,
                        body: body,
                        onsubmit: appointments_shortcode_on_submit( shortcode )
                    });
                }
            };
        }

        /**
         * Generates a single field for Editor popup
         *
         * @param id
         * @param definition
         * @returns {boolean}
         */
        function appointments_shortcode_field( id, definition ) {
            let field = {};
            switch ( definition.type ) {
                case 'text': {
                    field = {
                        type: 'textbox',
                        name: id,
                        label: definition.name,
                        value: definition.value
                    };
                    break;
                }
                case 'datepicker': {
                    field = {
                        type: 'textbox',
                        name: id,
                        label: definition.name,
                        value: definition.value,
                        onclick: function() {
                            const id = this._id;
                            const element = jQuery( '#' + id );
                            if ( datepickers.indexOf( id ) < 0 ) {
                                // Initialize Datepicker
                                datepickers.push( id );
                                if ( ! element.length || typeof element.datepicker === 'undefined' ) {
                                    return;
                                }

                                element.datepicker();
                                element.datepicker( "option", "dateFormat", 'yy-mm-dd' );
                                element.datepicker( "option", "firstDay", AppointmentsDateSettings.weekStart );
                            }

                            if ( typeof element.datepicker === 'function' ) {
                                element.datepicker( 'show' );
                            }

                        }
                    };
                    break;
                }
                case 'select': {
                    field = {
                        type: 'listbox',
                        name: id,
                        label: definition.name,
                        values: definition.options
                    };
                    break;
                }
                case 'checkbox': {
                    field = {
                        type: 'checkbox',
                        name: id,
                        label: definition.name,
                        value: 1
                    };

                    if ( definition.value ) {
                        field.checked = true;
                    }
                    break;
                }
            }

            if ( definition.help ) {
                field.tooltip = definition.help
            }

            return field;
        }

        /**
         * Generates the onSubmit action for a group of fields
         *
         * @param shortcode
         * @returns {Function}
         */
        function appointments_shortcode_on_submit( shortcode ) {
            return function( e ) {
                let atts = '';
                let value;
                for ( const i in shortcode.defaults ) {
                    if ( typeof e.data[ i ] !== 'undefined' && shortcode.defaults[i].value != e.data[i] ) {
                        value = e.data[i];
                        if ( 'checkbox' === shortcode.defaults[i].type ) {
                            value = value ? 1 : 0;
                        }
                        atts += ' ' + i + '="' + value + '"';
                    }
                }
                editor.insertContent( '[' + shortcode.shortcode + atts + ']' );
            }
        }

        let ed = tinymce.activeEditor,
            shortcode,
            registeredShortcodes;

        const app_menu = [];



        registeredShortcodes = ed.getLang( 'appointments_shortcodes.shortcodes' );

        for ( const i in registeredShortcodes ) {
            shortcode = registeredShortcodes[i];

            app_menu.push((function (short) {
                return appointments_shortcode_item(short);
            })(shortcode));
        }



        editor.addButton( 'appointments_shortcodes', {
            text: ed.getLang( 'appointments_shortcodes.label' ),
            type: 'menubutton',
            menu: app_menu
        });
    });
})();