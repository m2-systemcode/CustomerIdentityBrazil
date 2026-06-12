define([
    'jquery',
    'jquery/ui',
    'mage/translate',
    'SystemCode_CustomerIdentityBrazil/js/mask-helper'
], function ($, ui, $t, maskHelper) {
    'use strict';

    $.widget('systemCode.changePersonType', {
        options: {
            individualSelector: '[data-role=type-individual]',
            corporateSelector: '[data-role=type-corporation]',
            individualContainer: '[data-container=type-individual]',
            corporateContainer: '[data-container=type-corporation]',
            taxvatField: '.field.taxvat',
            changeFirstnameLabel: false,
            changeLastnameLabel: false
        },

        _create: function () {
            $(document).on(
                'change',
                this.options.individualSelector + ',' + this.options.corporateSelector,
                $.proxy(this._checkChoice, this)
            );
            this._checkChoice();
        },

        _isCorporate: function () {
            return $(this.options.corporateSelector).is(':checked');
        },

        _checkChoice: function () {
            if (this._isCorporate()) {
                this._showCorporate();
            } else {
                this._showIndividual();
            }
        },

        _showIndividual: function () {
            $(this.options.individualContainer).show();
            $(this.options.corporateContainer).hide();

            $(this.options.taxvatField).show();
            this._updateTaxvat('cpf');

            if (this.options.changeFirstnameLabel) {
                $('.field-name-firstname label span').text($.mage.__('First Name'));
            }

            if (this.options.changeLastnameLabel) {
                $('.field-name-lastname label span').text($.mage.__('Last Name'));
            }
        },

        _showCorporate: function () {
            $(this.options.corporateContainer).show();
            $(this.options.individualContainer).hide();

            $(this.options.taxvatField).show();
            this._updateTaxvat('cnpj');

            if (this.options.changeFirstnameLabel) {
                $('.field-name-firstname label span').text($.mage.__('Social Name'));
            }

            if (this.options.changeLastnameLabel) {
                $('.field-name-lastname label span').text($.mage.__('Trade Name'));
            }
        },

        _updateTaxvat: function (type) {
            var $field = $(this.options.taxvatField),
                $input = $field.find('#taxvat'),
                $label = $field.find('label span');

            $input.removeClass('validate-cpf validate-cnpj');

            if (type === 'cpf') {
                $label.text($.mage.__('CPF'));
                $input.addClass('validate-cpf');
            } else {
                $label.text($.mage.__('CNPJ'));
                $input.addClass('validate-cnpj');
            }

            maskHelper.applyTaxvat(type);
        }
    });

    return $.systemCode.changePersonType;
});
