define([
    'jquery',
    'SystemCode_CustomerIdentityBrazil/js/mask-helper'
], function ($, maskHelper) {
    'use strict';

    return function () {
        maskHelper.applyWhenReady('#telephone', 'telephone');
        maskHelper.applyWhenReady('input[name="telephone"]', 'telephone');
    };
});
