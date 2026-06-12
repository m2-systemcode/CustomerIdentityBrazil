var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping': {
                'SystemCode_CustomerIdentityBrazil/js/view/shipping-mixin': true
            },
            'Magento_Ui/js/lib/validation/rules': {
                'SystemCode_CustomerIdentityBrazil/js/lib/validation/rules-mixin': true
            },
            'Magento_Checkout/js/model/shipping-save-processor/payload-extender': {
                'SystemCode_CustomerIdentityBrazil/js/model/shipping-save-processor/payload-extender-mixin': true
            },
            'Magento_Checkout/js/action/set-shipping-information': {
                'SystemCode_CustomerIdentityBrazil/js/action/set-shipping-information-mixin': true
            },
            'Magento_Checkout/js/action/place-order': {
                'SystemCode_CustomerIdentityBrazil/js/action/place-order-mixin': true
            }
        }
    }
};
