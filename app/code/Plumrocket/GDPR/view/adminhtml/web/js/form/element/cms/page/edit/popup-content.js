/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_GDPR
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

/**
 * Single Checkbox Component Extended
 * @method extend(jsonObject)
 */

define([
    'uiRegistry',
    'Magento_Ui/js/form/element/wysiwyg'
], function (uiRegistry, wysiwyg) {
    'use strict';

    return wysiwyg.extend({
        defaults: {
            elementSelector: 'textarea',
            value: '',
            $wysiwygEditorButton: '',
            links: {
                value: '${ $.provider }:${ $.dataScope }'
            },
            template: 'ui/form/field',
            elementTmpl: 'ui/form/element/wysiwyg',
            content:        '',
            showSpinner:    false,
            loading:        false,
            disabled:       false,
            listens: {
                disabled: 'setDisabled'
            }
        },

        /**
         * Initialize handler.
         */
        initialize: function () {
            var self = this;

            self._super();

            if (! self.value()) {
                self.value(self.default);
            }

            var enableRevisionsField = uiRegistry.get('index = enable_revisions');
            if (enableRevisionsField && typeof enableRevisionsField !== 'undefined') {
                enableRevisionsField.on('update', function () {
                    var isEnabledRevisions = parseInt(enableRevisionsField.value()) === 1;

                    var notifyViaPopupField = uiRegistry.get('index = notify_via_popup');
                    if (notifyViaPopupField && typeof notifyViaPopupField !== 'undefined') {
                        var isEnabledNotifyViaPopup = parseInt(notifyViaPopupField.value()) === 1;
                        self.visible(isEnabledRevisions && isEnabledNotifyViaPopup);
                    } else {
                        self.visible(isEnabledRevisions);
                    }
                });

                var notifyViaPopupField = uiRegistry.get('index = notify_via_popup');
                if (notifyViaPopupField && typeof notifyViaPopupField !== 'undefined') {
                    var isEnabledNotifyViaPopup = parseInt(notifyViaPopupField.value()) === 1;
                    self.visible(isEnabledNotifyViaPopup);
                }
            }

            return self;
        }
    });
});