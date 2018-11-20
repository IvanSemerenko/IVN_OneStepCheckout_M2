define(
    [
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage',
        'Magento_Checkout/js/model/error-processor',
        'IVN_OneStepCheckout/js/model/cross-sell-items',
    ],
    function (
        quote,
        customer,
        urlBuilder,
        storage,
        errorProcessor,
        crossSell
    ) {
        'use strict';

        return {
            getCrossSellData: function () {
                var serviceUrl;

                if (!customer.isLoggedIn()) {
                    serviceUrl = urlBuilder.createUrl('/ivnOsc/guest-carts/:cartId/cross-sell', {
                        cartId: quote.getQuoteId()
                    });
                } else {
                    serviceUrl = urlBuilder.createUrl('/ivnOsc/carts/mine/cross-sell', {});
                }

                crossSell.isLoading(true);
                return storage.post(
                    serviceUrl
                ).done(
                    function (response) {
                        crossSell.setCrossSellData(response);
                    }
                ).fail(
                    function (response) {
                        errorProcessor.process(response);
                    }
                ).always(function () {
                    crossSell.isLoading(false);
                });
            },
        }
    }
);
