define([
    'jquery',
    'SystemCode_CustomerIdentityBrazil/js/model/checkout-identity',
    'SystemCode_CustomerIdentityBrazil/js/model/checkout-identity-sync'
], function ($, checkoutIdentity, checkoutIdentitySync) {
    'use strict';

    var supportedFields = {
        rg: true,
        ie: true,
        taxvat: true
    };

    function getExtensionData() {
        var config = window.checkoutConfig.customerIdentityBrazil || {},
            fields = config.fields || {},
            data = checkoutIdentity.getData(),
            extensionData = {};

        if (data.person_type) {
            extensionData.person_type = data.person_type;
        }

        $.each(fields, function (code) {
            if (supportedFields[code] && data[code]) {
                extensionData[code] = data[code];
            }
        });

        return extensionData;
    }

    return function (payloadExtender) {
        return function (payload) {
            payload = payloadExtender(payload);

            if (!payload.addressInformation) {
                return payload;
            }

            checkoutIdentitySync.syncFromProvider();

            payload.addressInformation.extension_attributes = payload.addressInformation.extension_attributes || {};
            Object.assign(payload.addressInformation.extension_attributes, getExtensionData());

            return payload;
        };
    };
});
