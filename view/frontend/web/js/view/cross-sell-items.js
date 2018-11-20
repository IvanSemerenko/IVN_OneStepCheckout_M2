define(
    [
        'uiComponent',
        'ko',
        'underscore',
        'IVN_OneStepCheckout/js/model/cross-sell-items'
    ],
    function (Component, ko, _,crossSellItems) {
        'use strict';

        return Component.extend({
            isLoading: crossSellItems.isLoading,

            isShow: crossSellItems.isShow,

            defaults: {
                template: 'IVN_OneStepCheckout/cross-sell-items',
                crossSellItems: crossSellItems.getCrossSellData(),
            },
            initialize: function () {
                var self = this;
                this._super();
            },
            /**
             * @inheritdoc
             */
            initObservable: function () {
                this._super();
                this.observe(['crossSellItems','isShow']);

                return this;
            }
        });
    }
);