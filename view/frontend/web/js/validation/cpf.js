define([
    'jquery',
    'jquery/validate',
    'mage/validation',
    'mage/translate'
], function ($) {
    'use strict';

    var invalidSequences = [
        '00000000000', '11111111111', '22222222222', '33333333333', '44444444444',
        '55555555555', '66666666666', '77777777777', '88888888888', '99999999999'
    ];

    if ($.validator && !$.validator.methods['validate-cpf']) {
        $.validator.addMethod(
            'validate-cpf',
            function (value) {
                var cpf = value.replace(/[^\d]+/g, ''),
                    sum,
                    remainder,
                    i;

                if ($.mage.isEmptyNoTrim(cpf)) {
                    return true;
                }

                if (cpf.length !== 11 || invalidSequences.indexOf(cpf) !== -1) {
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
            },
            $.mage.__('Please enter a valid CPF.')
        );
    }

    return function () {};
});
