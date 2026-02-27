/**
 * switchButton.js v2.0
 * Vanilla JS switch button (no jQuery UI dependency)
 * Migrated from jQuery UI Widget to vanilla JavaScript
 *
 * Copyright (c) Olivier Lance - released under MIT License
 * Modified for Appointments+ to remove jQuery UI dependency
 */

(function(window) {
    'use strict';

    /**
     * SwitchButton Class
     */
    class SwitchButton {
        constructor(element, options) {
            this.element = element;
            this.options = Object.assign({
                checked: undefined,
                show_labels: true,
                labels_placement: 'both',
                on_label: 'ON',
                off_label: 'OFF',
                width: 25,
                height: 11,
                button_width: 12,
                clear: true,
                clear_after: null,
                on_callback: undefined,
                off_callback: undefined
            }, options);

            // Init the switch from the checkbox if no state was specified
            if (this.options.checked === undefined) {
                this.options.checked = this.element.checked;
            }

            this.init();
        }

        init() {
            this.initLayout();
            this.initEvents();
        }

        initLayout() {
            // Hide the original checkbox
            this.element.style.display = 'none';

            // Create DOM elements
            this.offLabel = document.createElement('span');
            this.offLabel.className = 'switch-button-label';
            
            this.onLabel = document.createElement('span');
            this.onLabel.className = 'switch-button-label';
            
            this.buttonBg = document.createElement('div');
            this.buttonBg.className = 'switch-button-background';
            
            this.button = document.createElement('div');
            this.button.className = 'switch-button-button';

            // Insert elements after the checkbox
            this.element.parentNode.insertBefore(this.offLabel, this.element.nextSibling);
            this.offLabel.parentNode.insertBefore(this.buttonBg, this.offLabel.nextSibling);
            this.buttonBg.parentNode.insertBefore(this.onLabel, this.buttonBg.nextSibling);
            this.buttonBg.appendChild(this.button);

            // Insert clearing div if needed
            if (this.options.clear) {
                const clearDiv = document.createElement('div');
                clearDiv.style.clear = 'left';
                const clearAfter = this.options.clear_after || this.onLabel;
                clearAfter.parentNode.insertBefore(clearDiv, clearAfter.nextSibling);
            }

            // Update layout
            this.refresh();

            // Initialize state (with animation)
            this.options.checked = !this.options.checked;
            this.toggleSwitch(true);
        }

        refresh() {
            // Update labels visibility
            if (this.options.show_labels) {
                this.offLabel.style.display = '';
                this.onLabel.style.display = '';
            } else {
                this.offLabel.style.display = 'none';
                this.onLabel.style.display = 'none';
            }

            // Update labels text
            this.onLabel.textContent = this.options.on_label;
            this.offLabel.textContent = this.options.off_label;

            // Update dimensions
            this.buttonBg.style.width = this.options.width + 'px';
            this.buttonBg.style.height = this.options.height + 'px';
            this.button.style.width = this.options.button_width + 'px';
            this.button.style.height = this.options.height + 'px';
        }

        initEvents() {
            // Toggle on click
            this.buttonBg.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleSwitch(false);
            });

            this.button.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleSwitch(false);
            });

            // Label clicks
            this.onLabel.addEventListener('click', (e) => {
                if (this.options.checked && this.options.labels_placement === 'both') {
                    return;
                }
                this.toggleSwitch(false);
            });

            this.offLabel.addEventListener('click', (e) => {
                if (!this.options.checked && this.options.labels_placement === 'both') {
                    return;
                }
                this.toggleSwitch(false);
            });
        }

        toggleSwitch(isInitializing) {
            // Don't toggle if readonly or disabled
            if (!isInitializing && (this.element.readOnly || this.element.disabled)) {
                return;
            }

            this.options.checked = !this.options.checked;
            
            if (this.options.checked) {
                // Update checkbox
                this.element.checked = true;
                this.element.dispatchEvent(new Event('change', { bubbles: true }));

                // Calculate position
                const targetLeft = this.options.width - this.options.button_width;

                // Update labels
                if (this.options.labels_placement === 'both') {
                    this.offLabel.classList.remove('on');
                    this.offLabel.classList.add('off');
                    this.onLabel.classList.remove('off');
                    this.onLabel.classList.add('on');
                } else {
                    this.offLabel.style.display = 'none';
                    this.onLabel.style.display = '';
                }
                
                this.buttonBg.classList.add('checked');
                
                // Animate
                this.animate(this.button, { left: targetLeft }, 250);
                
                // Callback
                if (typeof this.options.on_callback === 'function') {
                    this.options.on_callback.call(this);
                }
            } else {
                // Update checkbox
                this.element.checked = false;
                this.element.dispatchEvent(new Event('change', { bubbles: true }));

                // Update labels
                if (this.options.labels_placement === 'both') {
                    this.offLabel.classList.remove('off');
                    this.offLabel.classList.add('on');
                    this.onLabel.classList.remove('on');
                    this.onLabel.classList.add('off');
                } else {
                    this.offLabel.style.display = '';
                    this.onLabel.style.display = 'none';
                }
                
                this.buttonBg.classList.remove('checked');
                
                // Animate
                this.animate(this.button, { left: -1 }, 250);
                
                // Callback
                if (typeof this.options.off_callback === 'function') {
                    this.options.off_callback.call(this);
                }
            }
        }

        animate(element, properties, duration) {
            const start = performance.now();
            const startLeft = parseInt(window.getComputedStyle(element).left) || -1;
            const targetLeft = properties.left;
            const change = targetLeft - startLeft;

            const step = (timestamp) => {
                const elapsed = timestamp - start;
                const progress = Math.min(elapsed / duration, 1);
                
                // Easing function (easeInOutCubic)
                const eased = progress < 0.5
                    ? 4 * progress * progress * progress
                    : 1 - Math.pow(-2 * progress + 2, 3) / 2;
                
                element.style.left = (startLeft + change * eased) + 'px';

                if (progress < 1) {
                    requestAnimationFrame(step);
                }
            };

            requestAnimationFrame(step);
        }

        setOption(key, value) {
            if (key === 'checked') {
                this.setChecked(value);
                return;
            }
            this.options[key] = value;
            this.refresh();
        }

        setChecked(value) {
            if (value === this.options.checked) {
                return;
            }
            this.options.checked = !value;
            this.toggleSwitch(false);
        }

        destroy() {
            this.offLabel.remove();
            this.onLabel.remove();
            this.buttonBg.remove();
            this.element.style.display = '';
        }
    }

    // jQuery plugin compatibility wrapper
    if (window.jQuery) {
        (function($) {
            $.fn.switchButton = function(options) {
                return this.each(function() {
                    if (!this._switchButton) {
                        this._switchButton = new SwitchButton(this, options);
                    }
                });
            };
        })(window.jQuery);
    }

    // Expose to window for non-jQuery usage
    window.SwitchButton = SwitchButton;

})(window);
