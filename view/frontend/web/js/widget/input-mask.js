define([
    'jquery',
    'jquery/ui',
    'SystemCode_CustomerIdentityBrazil/js/mask-helper'
], function ($, ui, maskHelper) {
    'use strict';

    $.widget('systemCode.inputMask', {
        options: {
            maskType: ''
        },

        _create: function () {
            if (!this.options.maskType) {
                return;
            }

            maskHelper.apply(this.element, this.options.maskType);
        }
    });

    return $.systemCode.inputMask;
});
