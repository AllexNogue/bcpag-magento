define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'brasilcashPaymentGateway',
                component: 'Brasilcash_Gateway/js/view/payment/method-renderer/brasilcashPaymentGateway'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);