define([
    'Magento_Ui/js/form/element/abstract',
    'SystemCode_CustomerIdentityBrazil/js/mask-helper'
], function (Abstract, maskHelper) {
    'use strict';

    return Abstract.extend({
        telephoneMaskApplied: false,

        onElementRender: function (element) {
            if (this.telephoneMaskApplied) {
                return;
            }

            maskHelper.applyTelephone(element);
            this.telephoneMaskApplied = true;
        }
    });
});
