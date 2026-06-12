define([
    'jquery',
    'SystemCode_CustomerIdentityBrazil/js/validation/cpf',
    'SystemCode_CustomerIdentityBrazil/js/validation/cnpj'
], function ($, cpfValidator, cnpjValidator) {
    'use strict';

    $.widget('systemCode.identityValidationInit', {
        _create: function () {
            cpfValidator();
            cnpjValidator();
        }
    });

    return $.systemCode.identityValidationInit;
});
