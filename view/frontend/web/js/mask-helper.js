define([
    'jquery',
    'SystemCode_Customer/js/jquery.mask'
], function ($) {
    'use strict';

    var maskOptions = {
            clearIfNotMatch: true
        },
        masks = {
            cpf: '000.000.000-00',
            cnpj: '00.000.000/0000-00'
        },
        phoneMaskBehavior = function (val) {
            return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
        },
        phoneMaskOptions = {
            onKeyPress: function (val, e, field, options) {
                field.mask(phoneMaskBehavior.apply({}, arguments), options);
            },
            clearIfNotMatch: true
        };

    function resolve$element(target) {
        if (!target) {
            return $();
        }

        if (target instanceof $) {
            return target;
        }

        if (target.nodeType === 1) {
            return $(target);
        }

        return $(target);
    }

    function applyTelephone(target) {
        var $element = resolve$element(target);

        if (!$element.length || typeof $.fn.mask !== 'function') {
            return false;
        }

        $element.unmask();
        $element.mask(phoneMaskBehavior, phoneMaskOptions);

        return true;
    }

    function applyMask($element, type) {
        if (!$element.length || typeof $.fn.mask !== 'function') {
            return false;
        }

        if (type === 'telephone') {
            return applyTelephone($element);
        }

        if (!masks[type]) {
            return false;
        }

        $element.unmask();
        $element.mask(masks[type], maskOptions);

        return true;
    }

    function applyWhenReady(target, type, attempts, delay) {
        var remaining = attempts || 20,
            wait = delay || 50;

        function tryApply() {
            if (applyMask(resolve$element(target), type)) {
                return;
            }

            remaining--;

            if (remaining > 0) {
                window.setTimeout(tryApply, wait);
            }
        }

        tryApply();
    }

    return {
        apply: function (target, type) {
            return applyMask(resolve$element(target), type);
        },

        applyWhenReady: applyWhenReady,

        applyTaxvat: function (type) {
            applyWhenReady('#taxvat', type);
        },

        applyTelephone: applyTelephone,

        applyCheckoutTelephone: function (container) {
            applyWhenReady(resolve$element(container).find('input[name="telephone"]'), 'telephone');
        },

        applyCheckout: function (container, config) {
            var $root = $(container),
                personType = config.personType === 'cnpj' ? 'cnpj' : 'cpf';

            applyWhenReady(
                personType === 'cpf' ? $root.find('#checkout-taxvat-cpf') : $root.find('#checkout-taxvat-cnpj'),
                personType
            );
        }
    };
});
