define([
    'ko',
    'underscore'
], function (ko, _) {
    'use strict';

    var crossSellData = ko.observable(window.checkoutConfig.crossSellItems);
    var isShow = ko.observable(false);

    return {
        isLoading: ko.observable(false),
        isShow: function () {
            if (Object.keys(crossSellData()).length < 1) {
                return ko.observable(false);
            }

            return ko.observable(true);
        },
        /**
         * @return {*}
         */
        getCrossSellData: function () {
            return crossSellData;
        },

        setCrossSellData: function (data) {
            crossSellData(data);
            this.isShow;
        }
    };
});
