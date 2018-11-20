define(
    [
        'uiComponent'
    ],
    function (Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'IVN_OneStepCheckout/cross-sell-items/thumbnail'
            },

            /**
             * Get src attribute
             *
             * @param {Object} item
             * @returns {string|null}
             */
            getSrc: function(item) {
                return this._getItemImageDataProperty(
                    item,
                    'src',
                    item.image_data.placeholderUrl
                );
            },

            getUrl: function(item) {
                return item.item_url;
            },

            /**
             * Get width attribute
             *
             * @param {Object} item
             * @returns {string|null}
             */
            getWidth: function(item) {
                return item.image_data.attributes.width;
            },

            /**
             * Get height attribute
             *
             * @param {Object} item
             * @returns {string|null}
             */
            getHeight: function(item) {
                return item.image_data.attributes.height;
            },

            /**
             * Get alt attribute
             *
             * @param {Object} item
             * @returns {string|null}
             */
            getAlt: function(item) {
                return item.image_data.alt;
            },

            /**
             * Get item image data property value
             *
             * @param {Number} itemId
             * @param {string} propName
             * @param {*} defaultValue
             * @returns {*}
             */
            _getItemImageDataProperty: function (item, propName, defaultValue) {
                var image = item.image_data;
                return image ? image[propName] : defaultValue;
            }
        });
    }
);
