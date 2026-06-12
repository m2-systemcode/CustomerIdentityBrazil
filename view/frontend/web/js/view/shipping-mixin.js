define([
    'mage/utils/wrapper',
    'Magento_Customer/js/model/customer',
    'SystemCode_CustomerIdentityBrazil/js/model/checkout-identity',
    'SystemCode_CustomerIdentityBrazil/js/model/checkout-identity-sync'
], function (wrapper, customer, checkoutIdentity, checkoutIdentitySync) {
    'use strict';

    return function (Shipping) {
        Shipping.prototype.triggerShippingDataValidateEvent = wrapper.wrap(
            Shipping.prototype.triggerShippingDataValidateEvent,
            function (originalTrigger) {
                var config = window.checkoutConfig.customerIdentityBrazil || {};

                originalTrigger.call(this);

                if (!config.isActive
                    || config.showIdentityFields === false
                    || config.useAccountIdentity
                    || customer.isLoggedIn()
                ) {
                    return;
                }

                checkoutIdentity.init(config);
                checkoutIdentitySync.syncFromProvider();
                this.source.trigger('brazilIdentity.data.validate');
            }
        );

        return Shipping;
    };
});
