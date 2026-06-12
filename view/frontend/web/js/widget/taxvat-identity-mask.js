define([
    'jquery',
    'jquery/ui',
    'SystemCode_CustomerIdentityBrazil/js/mask-helper'
], function ($, ui, maskHelper) {
    'use strict';

    function getPersonType() {
        return $('[data-role=type-corporation]').is(':checked') ? 'cnpj' : 'cpf';
    }

    $.widget('systemCode.taxvatIdentityMask', {
        _create: function () {
            this.applyMask();

            $(document).on(
                'change.systemCodeTaxvatIdentityMask',
                '[data-role=type-individual], [data-role=type-corporation]',
                $.proxy(this.applyMask, this)
            );
        },

        applyMask: function () {
            maskHelper.apply(this.element, getPersonType());
        },

        _destroy: function () {
            $(document).off('change.systemCodeTaxvatIdentityMask');
        }
    });

    return $.systemCode.taxvatIdentityMask;
});
