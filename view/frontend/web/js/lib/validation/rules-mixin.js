define([
    'jquery',
    'Magento_Ui/js/lib/validation/utils',
    'mage/translate'
], function ($, utils) {
    'use strict';

    var invalidCpfSequences = [
        '00000000000', '11111111111', '22222222222', '33333333333', '44444444444',
        '55555555555', '66666666666', '77777777777', '88888888888', '99999999999'
    ];

    var invalidCnpjSequences = [
        '00000000000000', '11111111111111', '22222222222222', '33333333333333',
        '44444444444444', '55555555555555', '66666666666666', '77777777777777',
        '88888888888888', '99999999999999'
    ];

    function isValidCpf(value) {
        var cpf = String(value || '').replace(/[^\d]+/g, ''),
            sum,
            remainder,
            i;

        if (cpf.length !== 11 || invalidCpfSequences.indexOf(cpf) !== -1) {
            return false;
        }

        for (i = 9; i < 11; i++) {
            sum = 0;
            for (var position = 0; position < i; position++) {
                sum += parseInt(cpf.charAt(position), 10) * ((i + 1) - position);
            }
            remainder = ((10 * sum) % 11) % 10;
            if (parseInt(cpf.charAt(i), 10) !== remainder) {
                return false;
            }
        }

        return true;
    }

    function isValidCnpj(value) {
        var cnpj = String(value || '').replace(/[^\d]+/g, ''),
            length,
            numbers,
            digits,
            sum,
            pos,
            result,
            i;

        if (cnpj.length !== 14 || invalidCnpjSequences.indexOf(cnpj) !== -1) {
            return false;
        }

        length = cnpj.length - 2;
        numbers = cnpj.substring(0, length);
        digits = cnpj.substring(length);
        sum = 0;
        pos = length - 7;

        for (i = length; i >= 1; i--) {
            sum += parseInt(numbers.charAt(length - i), 10) * pos--;
            if (pos < 2) {
                pos = 9;
            }
        }

        result = sum % 11 < 2 ? 0 : 11 - (sum % 11);
        if (result !== parseInt(digits.charAt(0), 10)) {
            return false;
        }

        length = length + 1;
        numbers = cnpj.substring(0, length);
        sum = 0;
        pos = length - 7;

        for (i = length; i >= 1; i--) {
            sum += parseInt(numbers.charAt(length - i), 10) * pos--;
            if (pos < 2) {
                pos = 9;
            }
        }

        result = sum % 11 < 2 ? 0 : 11 - (sum % 11);

        return result === parseInt(digits.charAt(1), 10);
    }

    return function (rules) {
        rules['validate-cpf'] = {
            handler: function (value) {
                return utils.isEmpty(value) || isValidCpf(value);
            },
            message: $.mage.__('Please enter a valid CPF.')
        };

        rules['validate-cnpj'] = {
            handler: function (value) {
                return utils.isEmpty(value) || isValidCnpj(value);
            },
            message: $.mage.__('Please enter a valid CNPJ.')
        };

        return rules;
    };
});
