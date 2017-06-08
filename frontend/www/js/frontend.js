/**
 * Frontend
 *
 * @type {{security: {csrf: {tokenName: null, token: null}}, init: frontend.init}}
 */
frontend = {
    security: {
        csrf: {
            tokenName: null,
            token: null
        }
    },

    init: function(settings) {
        this.settings = $.extend(this.settings, settings || {});
    },

    /**
     *
     * @type {{showSuccess: frontend.popupMessage.showSuccess, showError: frontend.popupMessage.showError, showInfo: frontend.popupMessage.showInfo, showMessage: frontend.popupMessage.showMessage}}
     */
    popupMessage: {
        showSuccess: function(text, params) {
            return this.showMessage(text, 'success', params);
        },
        showError: function(text, params) {
            return this.showMessage(text, 'error', params);
        },
        showInfo: function(text, params) {
            return this.showMessage(text, 'info', params);
        },
        showHtml: function(html) {
            this.showMessage(html, 'html', {isHtml: true});
        },

        showMessage: function(text, type, params) {
            params = params || {};
            var isHtml = params.isHtml || true;

            var $dialog = $('#' + type + '-alert');
            if (!$dialog.length) {
                console.log('Error, dialog not found');
                return false;
            }
            var html;
            if (!isHtml) {
                html = text;
            } else {
                html = '<p class="popup-alert popup-alert-' + type + '">' + text + '</p>';
            }

            $dialog.html(html);
            var dialogParams = {};
            if (params.dialog) {
                dialogParams = params.dialog;
            }
            $dialog.dialog(dialogParams);
            $dialog.dialog('open');
            return $dialog;
        },

        closePopup: function($dialog) {
            $dialog.dialog('close');
        }
    }
};
