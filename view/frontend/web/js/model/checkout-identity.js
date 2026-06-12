define([
    'jquery',
    'ko',
    'mage/translate'
], function ($, ko, $t) {
    'use strict';

    var initialized = false,
        personType = ko.observable('cpf'),
        data = {
            person_type: 'cpf',
            cpf: '',
            cnpj: '',
            rg: '',
            ie: '',
            socialname: '',
            tradename: '',
            taxvat: ''
        };

    personType.subscribe(function (value) {
        data.person_type = value;
    });

    return {
        init: function (config) {
            if (!initialized) {
                initialized = true;
                personType(config.personType || 'cpf');
                data.person_type = personType();
            }

            if (config.identityData) {
                this.applyIdentityData(config.identityData);
            }
        },

        applyIdentityData: function (identityData) {
            Object.keys(identityData).forEach(function (field) {
                if (identityData[field]) {
                    data[field] = identityData[field];
                }
            });

            if (identityData.person_type) {
                personType(identityData.person_type);
                data.person_type = identityData.person_type;
            }
        },

        getPersonType: function () {
            return personType;
        },

        set: function (field, value) {
            data[field] = value;
        },

        get: function (field) {
            return data[field] || '';
        },

        getData: function () {
            return $.extend({}, data);
        },

        bindField: function (field, observable) {
            observable.subscribe(function (value) {
                data[field] = value;
            });
            data[field] = observable();
        },

        validate: function (config) {
            if (!config || !config.isActive || config.showIdentityFields === false || config.useAccountIdentity) {
                return true;
            }

            var type = data.person_type || config.personType || 'cpf',
                codes = type === 'cpf' ? ['taxvat', 'rg'] : ['taxvat', 'ie'],
                code;

            for (var i = 0; i < codes.length; i++) {
                code = codes[i];

                if (code === 'taxvat') {
                    if (type === 'cnpj' ? !config.taxvatCorporationRequired : !config.taxvatIndividualRequired) {
                        continue;
                    }

                    if (!data[code]) {
                        throw new Error($t('%1 is a required field.').replace('%1', type === 'cnpj' ? 'CNPJ' : 'CPF'));
                    }

                    continue;
                }

                if (code === 'rg') {
                    if (!config.rgRequired || type !== 'cpf') {
                        continue;
                    }

                    if (!data[code]) {
                        throw new Error($t('%1 is a required field.').replace('%1', 'RG'));
                    }

                    continue;
                }

                if (code === 'ie') {
                    if (!config.ieRequired || type !== 'cnpj') {
                        continue;
                    }

                    if (!data[code]) {
                        throw new Error($t('%1 is a required field.').replace('%1', 'IE'));
                    }
                }
            }

            return true;
        }
    };
});
