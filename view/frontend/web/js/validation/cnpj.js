define([
    'jquery',
    'jquery/validate',
    'mage/validation',
    'mage/translate'
], function ($) {
    'use strict';

    var invalidSequences = [
        '00000000000000', '11111111111111', '22222222222222', '33333333333333',
        '44444444444444', '55555555555555', '66666666666666', '77777777777777',
        '88888888888888', '99999999999999'
    ];

    if ($.validator && !$.validator.methods['validate-cnpj']) {
        $.validator.addMethod(
            'validate-cnpj',
            function (value) {
                var cnpj = value.replace(/[^\d]+/g, ''),
                    length,
                    numbers,
                    digits,
                    sum,
                    pos,
                    result,
                    i;

                if ($.mage.isEmptyNoTrim(cnpj)) {
                    return true;
                }

                if (cnpj.length !== 14 || invalidSequences.indexOf(cnpj) !== -1) {
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
            },
            $.mage.__('Please enter a valid CNPJ.')
        );
    }

    return function () {};
});
